<?php
require_once __DIR__ . '/Database.php';

/**
 * Movie Class
 * Handles movie CRUD operations
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
        }
    }
    
    /**
     * Get all movies
     */
    public static function getAll() {
        $movies = Database::query("SELECT * FROM movies ORDER BY id DESC");
        
        // Get genres for each movie
        foreach ($movies as &$movie) {
            $movie['genres'] = self::getGenres($movie['id']);
        }
        
        return $movies;
    }
    
    /**
     * Get movie by ID
     */
    public static function getById($id) {
        $movie = Database::queryOne("SELECT * FROM movies WHERE id = ?", [$id], 'i');
        if ($movie) {
            $movie['genres'] = self::getGenres($id);
        }
        return $movie;
    }
    
    /**
     * Get genres for a movie
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
        
        // Save genres
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
        
        // Update genres
        $this->saveGenres();
        
        return true;
    }
    
    /**
     * Delete movie
     */
    public static function delete($id) {
        // Genres will be deleted automatically due to CASCADE
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
            $conn = Database::getConnection();
            $stmt = $conn->prepare("INSERT INTO movie_genres (movie_id, genre) VALUES (?, ?)");
            foreach ($this->genres as $genre) {
                $stmt->bind_param('is', $this->id, $genre);
                $stmt->execute();
            }
            $stmt->close();
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
    public function getGenres() { return $this->genres; }
    public function setGenres($genres) { $this->genres = is_array($genres) ? $genres : [$genres]; }
}

