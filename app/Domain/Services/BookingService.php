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
        
        $bookedSeats = $this->bookingRepository->getBookedSeats(
            $bookingData['screen_id'],
            $bookingData['show_date'],
            $bookingData['show_time']
        );
        
        $bookedSeatsMap = [];
        foreach ($bookedSeats as $booked) {
            $key = $booked['seat_row'] . $booked['seat_number'];
            $bookedSeatsMap[$key] = true;
        }
        
        foreach ($seats as $seat) {
            $key = $seat['row'] . $seat['number'];
            if (isset($bookedSeatsMap[$key])) {
                throw new Exception("Seat {$seat['row']}{$seat['number']} is already booked.");
            }
        }
        
        $this->db->beginTransaction();
        
        try {
            $bookingId = $this->bookingRepository->create($bookingData);
            
            if (!$bookingId) {
                throw new Exception('Failed to create booking.');
            }
            
            foreach ($seats as $seat) {
                $this->bookingRepository->createSeatReservation(
                    $bookingId,
                    $bookingData['screen_id'],
                    $seat['row'],
                    $seat['number'],
                    $seat['wheelchair'] ?? false
                );
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

