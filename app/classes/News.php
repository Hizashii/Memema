<?php
require_once __DIR__ . '/Database.php';

/**
 * News Class
 * 
 * Handles news CRUD operations.
 */
class News {
    private $id;
    private $img;
    private $title;
    private $excerpt;
    private $url;
    private $createdAt;
    private $updatedAt;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->img = $data['img'] ?? '';
            $this->title = $data['title'] ?? '';
            $this->excerpt = $data['excerpt'] ?? '';
            $this->url = $data['url'] ?? '#';
            $this->createdAt = $data['created_at'] ?? null;
            $this->updatedAt = $data['updated_at'] ?? null;
        }
    }
    
    /**
     * Get all news items
     */
    public static function getAll($limit = null) {
        $sql = "SELECT * FROM news ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        return Database::query($sql);
    }
    
    /**
     * Get news by ID
     */
    public static function getById($id) {
        return Database::queryOne("SELECT * FROM news WHERE id = ?", [$id], 'i');
    }
    
    /**
     * Create a new news item
     */
    public function create() {
        $sql = "INSERT INTO news (img, title, excerpt, url) VALUES (?, ?, ?, ?)";
        $this->id = Database::execute(
            $sql,
            [$this->img, $this->title, $this->excerpt, $this->url],
            'ssss'
        );
        return $this->id;
    }
    
    /**
     * Update news item
     */
    public function update() {
        if (!$this->id) {
            throw new Exception('Cannot update news without ID');
        }
        
        $sql = "UPDATE news SET img = ?, title = ?, excerpt = ?, url = ? WHERE id = ?";
        Database::execute(
            $sql,
            [$this->img, $this->title, $this->excerpt, $this->url, $this->id],
            'ssssi'
        );
        return true;
    }
    
    /**
     * Delete news item
     */
    public static function delete($id) {
        return Database::execute("DELETE FROM news WHERE id = ?", [$id], 'i');
    }
    
    // Getters and Setters
    public function getId() { return $this->id; }
    public function getImg() { return $this->img; }
    public function setImg($img) { $this->img = $img; }
    public function getTitle() { return $this->title; }
    public function setTitle($title) { $this->title = $title; }
    public function getExcerpt() { return $this->excerpt; }
    public function setExcerpt($excerpt) { $this->excerpt = $excerpt; }
    public function getUrl() { return $this->url; }
    public function setUrl($url) { $this->url = $url; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
}
