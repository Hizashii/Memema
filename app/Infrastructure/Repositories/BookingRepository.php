<?php
/**
 * Booking Repository
 */

class BookingRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findAll() {
        $sql = "
            SELECT b.*, 
                u.full_name as user_name, u.email as user_email,
                m.title as movie_title,
                v.name as venue_name,
                s.screen_name
            FROM bookings b
            LEFT JOIN users u ON b.user_id = u.id
            LEFT JOIN movies m ON b.movie_id = m.id
            LEFT JOIN venues v ON b.venue_id = v.id
            LEFT JOIN screens s ON b.screen_id = s.id
            ORDER BY b.created_at DESC
        ";
        return $this->db->query($sql);
    }
    
    public function findById($id) {
        $sql = "
            SELECT b.*, 
                u.full_name as user_name, u.email as user_email,
                m.title as movie_title,
                v.name as venue_name,
                s.screen_name
            FROM bookings b
            LEFT JOIN users u ON b.user_id = u.id
            LEFT JOIN movies m ON b.movie_id = m.id
            LEFT JOIN venues v ON b.venue_id = v.id
            LEFT JOIN screens s ON b.screen_id = s.id
            WHERE b.id = ?
        ";
        return $this->db->queryOne($sql, [$id]);
    }
    
    public function findByUserId($userId) {
        $sql = "
            SELECT b.*, 
                m.title as movie_title, m.img as movie_img,
                v.name as venue_name,
                s.screen_name
            FROM bookings b
            LEFT JOIN movies m ON b.movie_id = m.id
            LEFT JOIN venues v ON b.venue_id = v.id
            LEFT JOIN screens s ON b.screen_id = s.id
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC
        ";
        return $this->db->query($sql, [$userId]);
    }
    
    public function getBookedSeats($screenId, $showDate, $showTime) {
        // Normalize time format - handle both "10:00" and "10:00:00"
        $normalizedTime = $showTime;
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $showTime, $matches)) {
            $normalizedTime = $matches[1] . ':' . $matches[2] . ':00';
        }
        
        // Query to find all booked seats for this specific show
        // Use TIME() function to handle format variations
        // CAST seat_number to CHAR to ensure it's returned as string (handles both INT and VARCHAR columns)
        $sql = "
            SELECT sr.seat_row, CAST(sr.seat_number AS CHAR) as seat_number
            FROM seat_reservations sr
            INNER JOIN bookings b ON sr.booking_id = b.id
            WHERE sr.screen_id = ? 
            AND b.show_date = ? 
            AND (TIME(b.show_time) = TIME(?) OR TIME(b.show_time) = TIME(?))
            AND b.status IN ('confirmed', 'paid')
        ";
        return $this->db->query($sql, [$screenId, $showDate, $showTime, $normalizedTime]);
    }
    
    public function create($data) {
        $sql = "
            INSERT INTO bookings 
            (user_id, movie_id, venue_id, screen_id, show_date, show_time, seats_count, total_price, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        try {
            $result = $this->db->execute($sql, [
                $data['user_id'],
                $data['movie_id'],
                $data['venue_id'],
                $data['screen_id'],
                $data['show_date'],
                $data['show_time'],
                $data['seats_count'],
                $data['total_price'],
                $data['status'] ?? 'confirmed'
            ]);
            
            // If execute returns an insert ID (numeric), use it
            // Otherwise get it from lastInsertId()
            if (is_numeric($result) && $result > 0) {
                return (int)$result;
            }
            
            $insertId = $this->db->lastInsertId();
            if ($insertId && $insertId > 0) {
                return (int)$insertId;
            }
            
            error_log("BookingRepository::create - No insert ID returned. Result: " . var_export($result, true));
            return false;
        } catch (Exception $e) {
            error_log("BookingRepository::create error: " . $e->getMessage());
            throw $e;
        }
    }
    public function createSeatReservation($bookingId, $screenId, $seatRow, $seatNumber, $isWheelchair = false) {
        // Ensure seatNumber is stored as string
        $seatNumberStr = (string)$seatNumber;
        error_log("createSeatReservation: bookingId=$bookingId, screenId=$screenId, row='$seatRow', number='$seatNumberStr'");
        
        $sql = "
            INSERT INTO seat_reservations 
            (booking_id, screen_id, seat_row, seat_number, is_wheelchair)
            VALUES (?, ?, ?, ?, ?)
        ";
        $result = $this->db->execute($sql, [
            $bookingId,
            $screenId,
            $seatRow,
            $seatNumberStr,
            $isWheelchair ? 1 : 0
        ]);
        
        error_log("createSeatReservation: Result = " . var_export($result, true));
        return $result;
    }
    
    public function updateStatus($bookingId, $status) {
        $sql = "UPDATE bookings SET status = ? WHERE id = ?";
        return $this->db->execute($sql, [$status, $bookingId]);
    }
    
    public function getBookingDetails($bookingId) {
        $booking = $this->findById($bookingId);
        if (!$booking) {
            return null;
        }
        
        $seats = $this->getSeatsByBookingId($bookingId);
        $seatList = [];
        foreach ($seats as $seat) {
            $seatList[] = $seat['seat_row'] . $seat['seat_number'];
        }
        
        return [
            'booking_id' => $booking['id'],
            'booking_number' => 'BK' . str_pad($booking['id'], 6, '0', STR_PAD_LEFT),
            'customer_name' => $booking['user_name'] ?? 'Customer #' . $booking['user_id'],
            'customer_email' => $booking['user_email'] ?? '',
            'movie_title' => $booking['movie_title'] ?? 'Movie #' . $booking['movie_id'],
            'venue_name' => $booking['venue_name'] ?? 'Venue #' . $booking['venue_id'],
            'screen_name' => $booking['screen_name'] ?? 'Screen #' . $booking['screen_id'],
            'show_date' => $booking['show_date'],
            'show_time' => $booking['show_time'],
            'seats_count' => $booking['seats_count'],
            'seat_details' => implode(', ', $seatList),
            'total_price' => $booking['total_price'],
            'status' => $booking['status']
        ];
    }
    
    public function getSeatsByBookingId($bookingId) {
        $sql = "SELECT seat_row, seat_number, is_wheelchair FROM seat_reservations WHERE booking_id = ?";
        return $this->db->query($sql, [$bookingId]);
    }
    
    public function delete($id) {
        return $this->db->execute("DELETE FROM bookings WHERE id = ?", [$id]);
    }
}

