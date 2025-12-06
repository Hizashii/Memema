<?php
require_once __DIR__ . '/Database.php';

/**
 * Booking Class
 * Handles booking operations
 */
class Booking {
    private $id;
    private $userId;
    private $movieId;
    private $venueId;
    private $screenId;
    private $showDate;
    private $showTime;
    private $seatsCount;
    private $totalPrice;
    private $createdAt;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->userId = $data['user_id'] ?? null;
            $this->movieId = $data['movie_id'] ?? null;
            $this->venueId = $data['venue_id'] ?? null;
            $this->screenId = $data['screen_id'] ?? null;
            $this->showDate = $data['show_date'] ?? null;
            $this->showTime = $data['show_time'] ?? null;
            $this->seatsCount = $data['seats_count'] ?? null;
            $this->totalPrice = $data['total_price'] ?? null;
            $this->createdAt = $data['created_at'] ?? null;
        }
    }
    
    /**
     * Get all bookings
     */
    public static function getAll() {
        $sql = "SELECT b.*, 
                u.full_name as user_name, u.email as user_email,
                m.title as movie_title,
                v.name as venue_name,
                s.screen_name
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u.id
                LEFT JOIN movies m ON b.movie_id = m.id
                LEFT JOIN venues v ON b.venue_id = v.id
                LEFT JOIN screens s ON b.screen_id = s.id
                ORDER BY b.created_at DESC";
        return Database::query($sql);
    }
    
    /**
     * Get booking by ID
     */
    public static function getById($id) {
        $sql = "SELECT b.*, 
                u.full_name as user_name, u.email as user_email,
                m.title as movie_title,
                v.name as venue_name,
                s.screen_name
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u.id
                LEFT JOIN movies m ON b.movie_id = m.id
                LEFT JOIN venues v ON b.venue_id = v.id
                LEFT JOIN screens s ON b.screen_id = s.id
                WHERE b.id = ?";
        return Database::queryOne($sql, [$id], 'i');
    }
    
    /**
     * Get bookings by user ID
     */
    public static function getByUserId($userId) {
        $sql = "SELECT b.*, 
                m.title as movie_title, m.img as movie_img,
                v.name as venue_name,
                s.screen_name
                FROM bookings b
                LEFT JOIN movies m ON b.movie_id = m.id
                LEFT JOIN venues v ON b.venue_id = v.id
                LEFT JOIN screens s ON b.screen_id = s.id
                WHERE b.user_id = ?
                ORDER BY b.created_at DESC";
        return Database::query($sql, [$userId], 'i');
    }
    
    /**
     * Get booked seats for a show
     */
    public static function getBookedSeats($screenId, $showDate, $showTime) {
        $sql = "SELECT sr.seat_row, sr.seat_number 
                FROM seat_reservations sr 
                JOIN bookings b ON sr.booking_id = b.id 
                WHERE b.screen_id = ? 
                AND b.show_date = ? 
                AND b.show_time = ?";
        return Database::query($sql, [$screenId, $showDate, $showTime], 'iss');
    }
    
    /**
     * Create a new booking
     */
    public function create($seats = []) {
        $sql = "INSERT INTO bookings (user_id, movie_id, venue_id, screen_id, show_date, show_time, seats_count, total_price) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $this->id = Database::execute(
            $sql,
            [$this->userId, $this->movieId, $this->venueId, $this->screenId, 
             $this->showDate, $this->showTime, $this->seatsCount, $this->totalPrice],
            'iiiissid'
        );
        
        // Save seat reservations
        if (!empty($seats)) {
            $this->saveSeats($seats);
        }
        
        return $this->id;
    }
    
    /**
     * Save seat reservations
     */
    private function saveSeats($seats) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "INSERT INTO seat_reservations (booking_id, screen_id, seat_row, seat_number, is_wheelchair) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        foreach ($seats as $seat) {
            $seatRow = $seat['row'] ?? $seat['seat_row'] ?? '';
            $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? 0;
            $isWheelchair = $seat['is_wheelchair'] ?? false;
            
            $stmt->bind_param('iisii', $this->id, $this->screenId, $seatRow, $seatNumber, $isWheelchair);
            $stmt->execute();
        }
        
        $stmt->close();
    }
    
    /**
     * Delete booking
     */
    public static function delete($id) {
        // Seat reservations will be deleted automatically due to CASCADE
        return Database::execute("DELETE FROM bookings WHERE id = ?", [$id], 'i');
    }
    
    // Getters and Setters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function getMovieId() { return $this->movieId; }
    public function setMovieId($movieId) { $this->movieId = $movieId; }
    public function getVenueId() { return $this->venueId; }
    public function setVenueId($venueId) { $this->venueId = $venueId; }
    public function getScreenId() { return $this->screenId; }
    public function setScreenId($screenId) { $this->screenId = $screenId; }
    public function getShowDate() { return $this->showDate; }
    public function setShowDate($showDate) { $this->showDate = $showDate; }
    public function getShowTime() { return $this->showTime; }
    public function setShowTime($showTime) { $this->showTime = $showTime; }
    public function getSeatsCount() { return $this->seatsCount; }
    public function setSeatsCount($seatsCount) { $this->seatsCount = $seatsCount; }
    public function getTotalPrice() { return $this->totalPrice; }
    public function setTotalPrice($totalPrice) { $this->totalPrice = $totalPrice; }
}

