<?php

class ShowRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findAll() {
        $sql = "SELECT * FROM shows ORDER BY created_at DESC";
        $shows = $this->db->query($sql);
        
        foreach ($shows as &$show) {
            $show['showtimes'] = $this->getShowtimes($show['id']);
        }
        
        return $shows;
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM shows WHERE id = ?";
        $show = $this->db->queryOne($sql, [$id]);
        
        if ($show) {
            $show['showtimes'] = $this->getShowtimes($id);
        }
        
        return $show;
    }
    
    public function getShowtimes($showId) {
        $sql = "SELECT time FROM showtimes WHERE show_id = ? ORDER BY time";
        $times = $this->db->query($sql, [$showId]);
        return array_column($times, 'time');
    }
    
    public function create($data) {
        $sql = "INSERT INTO shows (title, img, tag_text, tag_color) VALUES (?, ?, ?, ?)";
        $id = $this->db->execute($sql, [
            $data['title'],
            $data['img'],
            $data['tag_text'],
            $data['tag_color']
        ]);
        
        if (!empty($data['showtimes']) && $id) {
            $this->saveShowtimes($id, $data['showtimes']);
        }
        
        return $id;
    }
    
    public function update($id, $data) {
        $sql = "UPDATE shows SET title = ?, img = ?, tag_text = ?, tag_color = ? WHERE id = ?";
        $this->db->execute($sql, [
            $data['title'],
            $data['img'],
            $data['tag_text'],
            $data['tag_color'],
            $id
        ]);
        
        if (isset($data['showtimes'])) {
            $this->saveShowtimes($id, $data['showtimes']);
        }
        
        return true;
    }
    
    public function delete($id) {
        $this->db->execute("DELETE FROM showtimes WHERE show_id = ?", [$id]);
        return $this->db->execute("DELETE FROM shows WHERE id = ?", [$id]);
    }
    
    private function saveShowtimes($showId, $showtimes) {
        $this->db->execute("DELETE FROM showtimes WHERE show_id = ?", [$showId]);
        
        if (!empty($showtimes)) {
            foreach ($showtimes as $time) {
                $time = trim($time);
                if (!empty($time)) {
                    $this->db->execute(
                        "INSERT INTO showtimes (show_id, time) VALUES (?, ?)",
                        [$showId, $time]
                    );
                }
            }
        }
    }
}

