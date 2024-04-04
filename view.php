<?php
class View
{
    /**
     * Renders page with list of articles.
     *
     * @param array $articles
     */
    public static function render_article_list(array $articles): void
    {
        $path = 'templates' . DIRECTORY_SEPARATOR . 'articles.html';
        ob_start();
        require $path;
        $html = ob_get_clean();
        $html = str_replace('{ARTICLES_OBJECT}', json_encode($articles), $html);
        echo $html;
    }

    /**
     * Renders page with article.
     *
     * @param bool $edit
     * @param array $article
     * @param array $specific_utm_source
     */
    public static function render_article(bool $edit, array $article, array $specific_utm_source): void
    {
        extract($article);
        $path = 'templates' . DIRECTORY_SEPARATOR;
        if ($edit) $path .= 'edit.html';
        else $path .= 'detail.html';
        ob_start();
        require $path;
        $html = ob_get_clean();
        $html = str_replace('{ARTICLE_ID}', $ID, $html);
        $html = str_replace('{ARTICLE_NAME}', htmlspecialchars($name), $html);
        $html = str_replace('{ARTICLE_CONTENT}', htmlspecialchars($content), $html);
        foreach ($specific_utm_source as $utm_source => $count)
        {
            $replacement = "<tr> <td>$utm_source</td> <td>$count</td> </tr>";
            $html = str_replace('{UTM_SOURCE}', $replacement . PHP_EOL . '{UTM_SOURCE}', $html);
        }
        $html = str_replace('{UTM_SOURCE}', '', $html);
        echo $html;
    }
}