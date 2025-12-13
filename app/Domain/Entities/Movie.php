<?php
/**
 * Movie Entity
 */

class Movie {
    private $id;
    private $title;
    private $img;
    private $durationMinutes;
    private $rating;
    private $genres;
    
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->img = $data['img'] ?? '';
        $this->durationMinutes = $data['duration_minutes'] ?? null;
        $this->rating = $data['rating'] ?? null;
        $this->genres = $data['genres'] ?? [];
    }
    
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getImg() { return $this->img; }
    public function getDurationMinutes() { return $this->durationMinutes; }
    public function getRating() { return $this->rating; }
    public function getGenres() { return $this->genres; }
    
    public function setId($id) { $this->id = $id; }
    public function setTitle($title) { $this->title = $title; }
    public function setImg($img) { $this->img = $img; }
    public function setDurationMinutes($duration) { $this->durationMinutes = $duration; }
    public function setRating($rating) { $this->rating = $rating; }
    public function setGenres($genres) { $this->genres = is_array($genres) ? $genres : [$genres]; }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'img' => $this->img,
            'duration_minutes' => $this->durationMinutes,
            'rating' => $this->rating,
            'genres' => $this->genres
        ];
    }
}

