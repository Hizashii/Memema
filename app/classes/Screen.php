<?php
require_once __DIR__ . '/Database.php';

/**
 * Screen Class
 * 
 * Handles screen operations within venues.
 */
class Screen {
    private $id;
    private $venueId;
    private $screenName;
    private $screenType;
    private $capacity;
    private $basePrice;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->venueId = $data['venue_id'] ?? null;
            $this->screenName = $data['screen_name'] ?? '';
            $this->screenType = $data['screen_type'] ?? 'standard';
            $this->capacity = $data['capacity'] ?? 0;
            $this->basePrice = $data['base_price'] ?? 12.50;
        }
    }
    
    /**
     * Get all screens
     */
    public static function getAll() {
        return Database::query("
            SELECT s.*, v.name as venue_name 
            FROM screens s 
            LEFT JOIN venues v ON s.venue_id = v.id 
            ORDER BY v.name, s.screen_name
        ");
    }
    
    /**
     * Get screen by ID
     */
    public static function getById($id) {
        return Database::queryOne("SELECT * FROM screens WHERE id = ?", [$id], 'i');
    }
    
    /**
     * Get screens by venue ID
     */
    public static function getByVenueId($venueId) {
        return Database::query(
            "SELECT * FROM screens WHERE venue_id = ? ORDER BY screen_name",
            [$venueId],
            'i'
        );
    }
    
    /**
     * Create a new screen
     */
    public function create() {
        $sql = "INSERT INTO screens (venue_id, screen_name, screen_type, capacity, base_price) VALUES (?, ?, ?, ?, ?)";
        $this->id = Database::execute(
            $sql,
            [$this->venueId, $this->screenName, $this->screenType, $this->capacity, $this->basePrice],
            'issid'
        );
        return $this->id;
    }
    
    /**
     * Update screen
     */
    public function update() {
        if (!$this->id) {
            throw new Exception('Cannot update screen without ID');
        }
        
        $sql = "UPDATE screens SET venue_id = ?, screen_name = ?, screen_type = ?, capacity = ?, base_price = ? WHERE id = ?";
        Database::execute(
            $sql,
            [$this->venueId, $this->screenName, $this->screenType, $this->capacity, $this->basePrice, $this->id],
            'issidi'
        );
        return true;
    }
    
    /**
     * Delete screen
     */
    public static function delete($id) {
        return Database::execute("DELETE FROM screens WHERE id = ?", [$id], 'i');
    }
    
    // Getters and Setters
    public function getId() { return $this->id; }
    public function getVenueId() { return $this->venueId; }
    public function setVenueId($venueId) { $this->venueId = $venueId; }
    public function getScreenName() { return $this->screenName; }
    public function setScreenName($screenName) { $this->screenName = $screenName; }
    public function getScreenType() { return $this->screenType; }
    public function setScreenType($screenType) { $this->screenType = $screenType; }
    public function getCapacity() { return $this->capacity; }
    public function setCapacity($capacity) { $this->capacity = $capacity; }
    public function getBasePrice() { return $this->basePrice; }
    public function setBasePrice($basePrice) { $this->basePrice = $basePrice; }
}

