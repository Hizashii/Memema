<?php
require_once __DIR__ . '/Database.php';

/**
 * Venue Class
 * Handles venue CRUD operations
 */
class Venue {
    private $id;
    private $name;
    private $address;
    private $phone;
    private $image;
    private $screens;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? '';
            $this->address = $data['address'] ?? '';
            $this->phone = $data['phone'] ?? '';
            $this->image = $data['image'] ?? '';
        }
    }
    
    /**
     * Get all venues
     */
    public static function getAll() {
        $venues = Database::query("SELECT * FROM venues ORDER BY id DESC");
        
        // Get screens for each venue
        foreach ($venues as &$venue) {
            $venue['screens'] = self::getScreens($venue['id']);
        }
        
        return $venues;
    }
    
    /**
     * Get venue by ID
     */
    public static function getById($id) {
        $venue = Database::queryOne("SELECT * FROM venues WHERE id = ?", [$id], 'i');
        if ($venue) {
            $venue['screens'] = self::getScreens($id);
        }
        return $venue;
    }
    
    /**
     * Get screens for a venue
     */
    public static function getScreens($venueId) {
        return Database::query(
            "SELECT * FROM screens WHERE venue_id = ? ORDER BY screen_name",
            [$venueId],
            'i'
        );
    }
    
    /**
     * Create a new venue
     */
    public function create() {
        $sql = "INSERT INTO venues (name, address, phone, image) VALUES (?, ?, ?, ?)";
        $this->id = Database::execute(
            $sql,
            [$this->name, $this->address, $this->phone, $this->image],
            'ssss'
        );
        return $this->id;
    }
    
    /**
     * Update venue
     */
    public function update() {
        if (!$this->id) {
            throw new Exception('Cannot update venue without ID');
        }
        
        $sql = "UPDATE venues SET name = ?, address = ?, phone = ?, image = ? WHERE id = ?";
        Database::execute(
            $sql,
            [$this->name, $this->address, $this->phone, $this->image, $this->id],
            'ssssi'
        );
        return true;
    }
    
    /**
     * Delete venue
     */
    public static function delete($id) {
        // Screens will be deleted automatically due to CASCADE
        return Database::execute("DELETE FROM venues WHERE id = ?", [$id], 'i');
    }
    
    // Getters and Setters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    public function getAddress() { return $this->address; }
    public function setAddress($address) { $this->address = $address; }
    public function getPhone() { return $this->phone; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function getImage() { return $this->image; }
    public function setImage($image) { $this->image = $image; }
}

