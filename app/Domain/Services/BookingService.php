<?php
/**
 * Booking Service
 */

class BookingService {
    private $db;
    private $bookingRepository;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->bookingRepository = new BookingRepository();
    }
    
    public function createBooking($bookingData, $seats) {
        if (count($seats) > 5) {
            throw new Exception('Maximum 5 seats per booking allowed.');
        }
        
        if (empty($seats)) {
            throw new Exception('At least one seat must be selected.');
        }
        
        foreach ($seats as $seat) {
            if (empty($seat['row']) || empty($seat['number'])) {
                throw new Exception('Invalid seat data provided.');
            }
        }
        
        // Get all booked seats for this show to prevent double-booking
        $bookedSeats = $this->bookingRepository->getBookedSeats(
            $bookingData['screen_id'],
            $bookingData['show_date'],
            $bookingData['show_time']
        );
        
        $bookedSeatsMap = [];
        foreach ($bookedSeats as $booked) {
            // Create key from seat row and number (always convert to string for consistency)
            $seatNumber = (string)$booked['seat_number'];
            $seatKey = $booked['seat_row'] . $seatNumber;
            $bookedSeatsMap[$seatKey] = true;
            error_log("BookingService: Found booked seat - row='{$booked['seat_row']}', number='{$booked['seat_number']}' (type: " . gettype($booked['seat_number']) . "), key='$seatKey'");
        }
        
        // Log for debugging
        error_log("BookingService: Found " . count($bookedSeatsMap) . " booked seats: " . implode(', ', array_keys($bookedSeatsMap)));
        error_log("BookingService: Requesting seats: " . json_encode(array_map(function($s) { return $s['row'] . $s['number']; }, $seats)));
        
        // Check each requested seat against booked seats
        foreach ($seats as $seat) {
            $seatKey = $seat['row'] . (string)$seat['number'];
            error_log("BookingService: Checking seat key '$seatKey' against booked seats");
            if (isset($bookedSeatsMap[$seatKey])) {
                error_log("BookingService: CONFLICT! Seat '$seatKey' is already booked!");
                throw new Exception("Seat {$seat['row']}{$seat['number']} is already booked for this show. Please select a different seat.");
            }
        }
        
        error_log("BookingService: No conflicts found, proceeding with booking");
        
        $this->db->beginTransaction();
        
        try {
            // Double-check booked seats right before creating (prevents race conditions)
            // This ensures no one else booked the same seats between our initial check and now
            $recheckBookedSeats = $this->bookingRepository->getBookedSeats(
                $bookingData['screen_id'],
                $bookingData['show_date'],
                $bookingData['show_time']
            );
            
            $recheckBookedSeatsMap = [];
            foreach ($recheckBookedSeats as $booked) {
                $seatNumber = (string)$booked['seat_number'];
                $seatKey = $booked['seat_row'] . $seatNumber;
                $recheckBookedSeatsMap[$seatKey] = true;
            }
            
            error_log("BookingService: Recheck found " . count($recheckBookedSeatsMap) . " booked seats: " . implode(', ', array_keys($recheckBookedSeatsMap)));
            
            // Verify none of the requested seats are now booked
            foreach ($seats as $seat) {
                $seatKey = $seat['row'] . (string)$seat['number'];
                error_log("BookingService: Rechecking seat key '$seatKey'");
                if (isset($recheckBookedSeatsMap[$seatKey])) {
                    $this->db->rollback();
                    error_log("BookingService: RECHECK CONFLICT! Seat '$seatKey' is booked!");
                    throw new Exception("Seat {$seat['row']}{$seat['number']} was just booked by another user. Please select a different seat.");
                }
            }
            
            $bookingId = $this->bookingRepository->create($bookingData);
            
            if (!$bookingId) {
                throw new Exception('Failed to create booking.');
            }
            
            foreach ($seats as $seat) {
                error_log("BookingService: Storing seat - row='{$seat['row']}', number='{$seat['number']}', full key='{$seat['row']}{$seat['number']}'");
                $result = $this->bookingRepository->createSeatReservation(
                    $bookingId,
                    $bookingData['screen_id'],
                    $seat['row'],
                    $seat['number'],
                    $seat['wheelchair'] ?? false
                );
                if (!$result) {
                    throw new Exception("Failed to reserve seat {$seat['row']}{$seat['number']}");
                }
            }
            
            // Verify what was actually stored
            $storedSeats = $this->bookingRepository->getSeatsByBookingId($bookingId);
            error_log("BookingService: Verified stored seats for booking $bookingId:");
            foreach ($storedSeats as $stored) {
                $storedKey = $stored['seat_row'] . (string)$stored['seat_number'];
                error_log("  - Stored: row='{$stored['seat_row']}', number='{$stored['seat_number']}', key='$storedKey'");
            }
            
            $this->db->commit();
            
            try {
                $this->notifyAdminOfBooking($bookingId);
            } catch (Exception $e) {
                error_log("Failed to send admin notification: " . $e->getMessage());
            }
            
            return $bookingId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function getBookedSeats($screenId, $showDate, $showTime) {
        return $this->bookingRepository->getBookedSeats($screenId, $showDate, $showTime);
    }
    
    public function areSeatsAvailable($screenId, $showDate, $showTime, $seats) {
        $bookedSeats = $this->getBookedSeats($screenId, $showDate, $showTime);
        
        $bookedSeatsMap = [];
        foreach ($bookedSeats as $booked) {
            $key = $booked['seat_row'] . $booked['seat_number'];
            $bookedSeatsMap[$key] = true;
        }
        
        foreach ($seats as $seat) {
            $key = $seat['row'] . $seat['number'];
            if (isset($bookedSeatsMap[$key])) {
                return false;
            }
        }
        
        return true;
    }
    
    public function updateBookingStatus($bookingId, $status) {
        $allowedStatuses = ['confirmed', 'cancelled', 'completed', 'paid'];
        if (!in_array($status, $allowedStatuses)) {
            throw new Exception('Invalid booking status.');
        }
        
        return $this->bookingRepository->updateStatus($bookingId, $status);
    }
    
    public function getBooking($bookingId) {
        return $this->bookingRepository->findById($bookingId);
    }
    
    private function notifyAdminOfBooking($bookingId) {
        $bookingDetails = $this->bookingRepository->getBookingDetails($bookingId);
        
        if (!$bookingDetails) {
            error_log("Could not get booking details for ID: " . $bookingId);
            return false;
        }
        
        $mailService = new MailService();
        return $mailService->sendBookingNotificationToAdmin($bookingDetails);
    }
    
    public function getUserBookings($userId) {
        return $this->bookingRepository->findByUserId($userId);
    }
}

