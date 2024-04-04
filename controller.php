<?php

use JetBrains\PhpStorm\NoReturn;

class Controller
{
    /**
     * Chooses action based on requested page and used method.
     *
     * @param string $request_path
     * @param string $method
     */
    public static function route(string $request_path, string $method): void
    {
        [$requested_page, $article_id] = self::validate_path($request_path);
        try {
            require 'model.php';
            $model = new Model();
        } catch (Exception) {
            exit_with_code(500, 'Failed to connect to database');
        }

        if ($requested_page === 'articles' && $method === 'GET')
        {
            if (is_null($article_id))
                self::serve_article_list($model);
            else
                self::serve_article($model, false, $article_id);
        }

        elseif ($requested_page === 'articles' && $method !== 'GET')
        {
            if ($method === 'POST' && is_null($article_id) && isset($_POST['name']))
            {
                $article_id = $model->create_article(self::validate_article_name($_POST['name']));
                header('Location: /~80697138/cms/article-edit/' . $article_id);
            }
            elseif ($method === 'DELETE' && !is_null($article_id))
                $model->delete_article($article_id);
            else exit_with_code(400, 'Bad request');
        }

        elseif ($requested_page === 'article-edit' && $method === 'GET'
            && !is_null($article_id))
        {
            self::serve_article($model, true, $article_id);
        }

        elseif ($requested_page === 'article-edit' && $method === 'POST'
            && !is_null($article_id) && isset($_POST['name']))
        {
            $article_name = self::validate_article_name($_POST['name']);
            $article_content = $_POST['content'] ?? '';
            $model->edit_article($article_id, $article_name, $article_content);
            header('Location: /~80697138/cms/articles');
        }
        else exit_with_code(400, 'Bad request');
    }

    /**
     * Serves page with list of articles.
     *
     * @param $model
     */
    private static function serve_article_list($model): void
    {
        $articles = $model->get_article_list();
        require 'view.php';
        View::render_article_list($articles);
    }

    /**
     * Serves page with requested article or article edit form.
     *
     * @param $model
     * @param bool $to_edit
     * @param int|string $article_id
     */
    private static function serve_article($model, bool $to_edit, int|string $article_id): void
    {
        $article = $model->get_article($article_id);
        if ($article === false)
            exit_with_code(500, 'Database error');
        elseif ($article === null)
            exit_with_code(404, 'Article not found');

        if (!$to_edit)
        {
            $utm_source = self::validate_utm_source($_GET['utm_source'] ?? null);
            $utm_array = unserialize(file_get_contents('utm_source.txt'));
            if (!is_array($utm_array))
                $utm_array = [];
            if ($utm_source !== null)
            {
                $utm_array[$article_id][$utm_source] = ($utm_array[$article_id][$utm_source] + 1) ?? 1;
                file_put_contents('utm_source.txt', serialize($utm_array));
            }
        }
        require 'view.php';
        if (isset($utm_array[$article_id]))
            View::render_article($to_edit, $article, $utm_array[$article_id]);
        else
            View::render_article($to_edit, $article, []);
    }

    /**
     * Verifies requested path and if succeeds, returns requested_page and article_id.
     * Otherwise, exits with code 404.
     *
     * @param string $path
     * @return array [requested_page, article_id]
     */
    private static function validate_path(string $path): array
    {
        $path_parts = explode('/', $path);
        if (count($path_parts) < 2 || count($path_parts) > 3 || $path_parts[0] != '')
            exit_with_code(404, 'File not found');
        $requested_page = $path_parts[1] == '' ? 'articles' : $path_parts[1];

        if ($requested_page === 'articles' && count($path_parts) === 2)
            return [$requested_page, null];
        elseif (in_array($requested_page, ['articles', 'article', 'article-edit']))
        {
            $requested_page = $requested_page === 'article' ? 'articles' : $requested_page;
            $article_id = self::validate_article_id($path_parts[2] ?? -1);
            return [$requested_page, $article_id];
        } else
            exit_with_code(404, 'File not found');
    }

    /**
     * Checks if article_id is a valid non-negative integer and returns it.
     * If not, exits with code 404 (page with invalid article_id cannot be found).
     *
     * @param int|string $article_id
     * @return int
     */
    private static function validate_article_id(int|string $article_id): int
    {
        $article_id = filter_var($article_id, FILTER_VALIDATE_INT) ? intval($article_id) : -1;
        if ($article_id < 0)
            exit_with_code(404, 'Invalid article id');
        return $article_id;
    }

    /**
     * Validates and sanitizes article_name and returns it.
     * In case of failure, exits with code 400 (bad request).
     *
     * @param string $article_name
     * @return string
     */
    private static function validate_article_name(string $article_name): string
    {
        if (strlen($article_name) == 0 || strlen($article_name) > 32)
            exit_with_code(400, 'Invalid article name');
        return $article_name;
    }

    /**
     * Validates utm_source and returns it.
     * In case of failure, returns null.
     *
     * @param string|null $utm_source
     * @return string|null
     */
    private static function validate_utm_source(string|null $utm_source): string|null
    {
        $expected_pattern = '/^[a-z0-9]{1,64}$/';
        if (is_null($utm_source) || !preg_match($expected_pattern, $utm_source))
            return null;
        return $utm_source;
    }
}

/**
 * Sends back given http code and exits the application.
 *
 * @param int $code
 * @param string $statement
 */
#[NoReturn] function exit_with_code(int $code, string $statement = ''): void
{
    http_response_code($code);
    // header('HTTP/1.1 ' . $code . ' ' . $statement, true, $code);
    exit;
}