<?php
require_once __DIR__ . '/Database.php';

/**
 * Movie Class
 * 
 * Handles movie CRUD operations with genres.
 */
class Movie {
    private $id;
    private $title;
    private $img;
    private $durationMinutes;
    private $rating;
    private $genres;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->title = $data['title'] ?? '';
            $this->img = $data['img'] ?? '';
            $this->durationMinutes = $data['duration_minutes'] ?? null;
            $this->rating = $data['rating'] ?? null;
            $this->genres = $data['genres'] ?? [];
            
            // Ensure genres is an array of strings
            if (!empty($this->genres)) {
                $this->genres = array_filter(array_map(function($g) {
                    return is_array($g) ? ($g['genre'] ?? '') : trim($g);
                }, $this->genres));
            }
        }
    }
    
    /**
     * Get all movies with genres
     */
    public static function getAll() {
        $movies = Database::query("SELECT * FROM movies ORDER BY id DESC");
        
        foreach ($movies as &$movie) {
            $movie['genres'] = self::getGenres($movie['id']);
        }
        
        return $movies;
    }
    
    /**
     * Get movie by ID with genres
     */
    public static function getById($id) {
        $movie = Database::queryOne("SELECT * FROM movies WHERE id = ?", [$id], 'i');
        if ($movie) {
            $movie['genres'] = self::getGenres($id);
        }
        return $movie;
    }
    
    /**
     * Get genres for a movie (returns array of genre strings)
     */
    public static function getGenres($movieId) {
        $genres = Database::query(
            "SELECT genre FROM movie_genres WHERE movie_id = ? ORDER BY genre",
            [$movieId],
            'i'
        );
        return array_column($genres, 'genre');
    }
    
    /**
     * Create a new movie
     */
    public function create() {
        $sql = "INSERT INTO movies (title, img, duration_minutes, rating) VALUES (?, ?, ?, ?)";
        $this->id = Database::execute(
            $sql,
            [$this->title, $this->img, $this->durationMinutes, $this->rating],
            'ssid'
        );
        
        if (!empty($this->genres)) {
            $this->saveGenres();
        }
        
        return $this->id;
    }
    
    /**
     * Update movie
     */
    public function update() {
        if (!$this->id) {
            throw new Exception('Cannot update movie without ID');
        }
        
        $sql = "UPDATE movies SET title = ?, img = ?, duration_minutes = ?, rating = ? WHERE id = ?";
        Database::execute(
            $sql,
            [$this->title, $this->img, $this->durationMinutes, $this->rating, $this->id],
            'ssidi'
        );
        
        $this->saveGenres();
        
        return true;
    }
    
    /**
     * Delete movie
     */
    public static function delete($id) {
        return Database::execute("DELETE FROM movies WHERE id = ?", [$id], 'i');
    }
    
    /**
     * Save genres for the movie
     */
    private function saveGenres() {
        // Delete existing genres
        Database::execute("DELETE FROM movie_genres WHERE movie_id = ?", [$this->id], 'i');
        
        // Insert new genres
        if (!empty($this->genres)) {
            foreach ($this->genres as $genre) {
                $genre = trim($genre);
                if (!empty($genre)) {
                    Database::execute(
                        "INSERT INTO movie_genres (movie_id, genre) VALUES (?, ?)",
                        [$this->id, $genre],
                        'is'
                    );
                }
            }
        }
    }
    
    // Getters and Setters
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function setTitle($title) { $this->title = $title; }
    public function getImg() { return $this->img; }
    public function setImg($img) { $this->img = $img; }
    public function getDurationMinutes() { return $this->durationMinutes; }
    public function setDurationMinutes($duration) { $this->durationMinutes = $duration; }
    public function getRating() { return $this->rating; }
    public function setRating($rating) { $this->rating = $rating; }
    public function getMovieGenres() { return $this->genres; }
    public function setGenres($genres) { $this->genres = is_array($genres) ? $genres : [$genres]; }
}
