<?php
/**
 * Movie Repository
 */

class MovieRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findAll() {
        $sql = "SELECT * FROM movies ORDER BY id DESC";
        $movies = $this->db->query($sql);
        
        foreach ($movies as &$movie) {
            $movie['genres'] = $this->getGenres($movie['id']);
        }
        
        return $movies;
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM movies WHERE id = ?";
        $movie = $this->db->queryOne($sql, [$id]);
        
        if ($movie) {
            $movie['genres'] = $this->getGenres($id);
        }
        
        return $movie;
    }
    
    public function getGenres($movieId) {
        $sql = "SELECT genre FROM movie_genres WHERE movie_id = ? ORDER BY genre";
        $genres = $this->db->query($sql, [$movieId]);
        return array_column($genres, 'genre');
    }
    
    public function create($data) {
        $sql = "INSERT INTO movies (title, img, duration_minutes, rating) VALUES (?, ?, ?, ?)";
        $id = $this->db->execute($sql, [
            $data['title'],
            $data['img'],
            $data['duration_minutes'],
            $data['rating']
        ]);
        
        if (!empty($data['genres']) && $id) {
            $this->saveGenres($id, $data['genres']);
        }
        
        return $id;
    }
    
    public function update($id, $data) {
        $sql = "UPDATE movies SET title = ?, img = ?, duration_minutes = ?, rating = ? WHERE id = ?";
        $this->db->execute($sql, [
            $data['title'],
            $data['img'],
            $data['duration_minutes'],
            $data['rating'],
            $id
        ]);
        
        if (isset($data['genres'])) {
            $this->saveGenres($id, $data['genres']);
        }
        
        return true;
    }
    
    public function delete($id) {
        $this->db->execute("DELETE FROM movie_genres WHERE movie_id = ?", [$id]);
        return $this->db->execute("DELETE FROM movies WHERE id = ?", [$id]);
    }
    
    private function saveGenres($movieId, $genres) {
        $this->db->execute("DELETE FROM movie_genres WHERE movie_id = ?", [$movieId]);
        
        if (!empty($genres)) {
            foreach ($genres as $genre) {
                $genre = trim($genre);
                if (!empty($genre)) {
                    $this->db->execute(
                        "INSERT INTO movie_genres (movie_id, genre) VALUES (?, ?)",
                        [$movieId, $genre]
                    );
                }
            }
        }
    }
}

