<?php
/**
 * News Entity
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
        $this->id = $data['id'] ?? null;
        $this->img = $data['img'] ?? '';
        $this->title = $data['title'] ?? '';
        $this->excerpt = $data['excerpt'] ?? '';
        $this->url = $data['url'] ?? '#';
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }
    
    public function getId() { return $this->id; }
    public function getImg() { return $this->img; }
    public function getTitle() { return $this->title; }
    public function getExcerpt() { return $this->excerpt; }
    public function getUrl() { return $this->url; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    
    public function setId($id) { $this->id = $id; }
    public function setImg($img) { $this->img = $img; }
    public function setTitle($title) { $this->title = $title; }
    public function setExcerpt($excerpt) { $this->excerpt = $excerpt; }
    public function setUrl($url) { $this->url = $url; }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'img' => $this->img,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'url' => $this->url,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}

