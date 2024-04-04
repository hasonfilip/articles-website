<?php
class Model
{
    private mysqli $db;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        global $db_config;
        require_once 'db_config.php';
        $this->db = new mysqli(
            $db_config['server'],
            $db_config['login'],
            $db_config['password'],
            $db_config['database']
        );
        if ($this->db->connect_errno) {
            throw new Exception('Database connection error');
        }
    }

    public function get_article_list(): array
    {
        $articles = [];
        if ($result = $this->db->query("SELECT * FROM articles"))
        {
            while ($row = $result->fetch_assoc())
            {
                $articles[] = $row;
            }
        }
        return $articles;
    }

    public function get_article($article_id): array|false|null
    {
        $stmt = $this->db->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->bind_param('i', $article_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create_article($article_name): int
    {
        $stmt = $this->db->prepare("INSERT INTO articles (name) VALUES (?)");
        $stmt->bind_param('s', $article_name);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function delete_article($article_id): void
    {
        $stmt = $this->db->prepare("DELETE FROM articles WHERE ID = ?");
        $stmt->bind_param('i', $article_id);
        $stmt->execute();
    }

    public function edit_article($article_id, $article_name, $article_content): void
    {
        $stmt = $this->db->prepare("UPDATE articles SET name = ?, content = ? WHERE ID = ?");
        $stmt->bind_param('ssi', $article_name, $article_content, $article_id);
        $stmt->execute();
    }

    public function __destruct()
    {
        $this->db->close();
    }
}