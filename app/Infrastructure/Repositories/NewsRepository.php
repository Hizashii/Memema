<?php
/**
 * News Repository
 */

class NewsRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findAll($limit = null) {
        $sql = "SELECT * FROM news ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        return $this->db->query($sql);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM news WHERE id = ?";
        return $this->db->queryOne($sql, [$id]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO news (img, title, excerpt, url) VALUES (?, ?, ?, ?)";
        return $this->db->execute($sql, [
            $data['img'],
            $data['title'],
            $data['excerpt'],
            $data['url']
        ]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE news SET img = ?, title = ?, excerpt = ?, url = ? WHERE id = ?";
        return $this->db->execute($sql, [
            $data['img'],
            $data['title'],
            $data['excerpt'],
            $data['url'],
            $id
        ]);
    }
    
    public function delete($id) {
        return $this->db->execute("DELETE FROM news WHERE id = ?", [$id]);
    }
}

