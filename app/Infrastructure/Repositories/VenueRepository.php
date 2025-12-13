<?php
/**
 * Venue Repository
 */

class VenueRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findAll() {
        $sql = "SELECT * FROM venues ORDER BY name";
        $venues = $this->db->query($sql);
        
        foreach ($venues as &$venue) {
            $venue['screens'] = $this->getScreens($venue['id']);
        }
        
        return $venues;
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM venues WHERE id = ?";
        $venue = $this->db->queryOne($sql, [$id]);
        
        if ($venue) {
            $venue['screens'] = $this->getScreens($id);
        }
        
        return $venue;
    }
    
    public function getScreens($venueId) {
        $sql = "SELECT * FROM screens WHERE venue_id = ? ORDER BY screen_name";
        return $this->db->query($sql, [$venueId]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO venues (name, address, phone, image) VALUES (?, ?, ?, ?)";
        return $this->db->execute($sql, [
            $data['name'],
            $data['address'],
            $data['phone'],
            $data['image']
        ]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE venues SET name = ?, address = ?, phone = ?, image = ? WHERE id = ?";
        return $this->db->execute($sql, [
            $data['name'],
            $data['address'],
            $data['phone'],
            $data['image'],
            $id
        ]);
    }
    
    public function delete($id) {
        return $this->db->execute("DELETE FROM venues WHERE id = ?", [$id]);
    }
}

