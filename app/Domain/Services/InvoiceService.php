<?php
/**
 * Invoice Service
 */

class InvoiceService {
    private $db;
    private $bookingRepository;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->bookingRepository = new BookingRepository();
    }
    
    public function generateInvoice($bookingId) {
        $booking = $this->bookingRepository->findById($bookingId);
        
        if (!$booking) {
            throw new Exception('Booking not found.');
        }
        
        $sql = "
            SELECT sr.* 
            FROM seat_reservations sr
            WHERE sr.booking_id = ?
            ORDER BY sr.seat_row, sr.seat_number
        ";
        $seats = $this->db->query($sql, [$bookingId]);
        
        $invoice = [
            'booking_id' => $booking['id'],
            'booking_number' => 'BK' . str_pad($booking['id'], 6, '0', STR_PAD_LEFT),
            'user_name' => $booking['user_name'] ?? 'Guest',
            'user_email' => $booking['user_email'] ?? '',
            'movie_title' => $booking['movie_title'] ?? '',
            'venue_name' => $booking['venue_name'] ?? '',
            'screen_name' => $booking['screen_name'] ?? '',
            'show_date' => $booking['show_date'],
            'show_time' => $booking['show_time'],
            'seats' => $seats,
            'seats_count' => $booking['seats_count'],
            'total_price' => $booking['total_price'],
            'status' => $booking['status'],
            'created_at' => $booking['created_at']
        ];
        
        return $invoice;
    }
    
    public function processPayment($bookingId, $paymentMethod = 'simulated') {
        $this->db->beginTransaction();
        
        try {
            $this->bookingRepository->updateStatus($bookingId, 'paid');
            $this->db->commit();
            
            return [
                'success' => true,
                'booking_id' => $bookingId,
                'payment_method' => $paymentMethod,
                'message' => 'Payment processed successfully'
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function getAllInvoices() {
        $bookings = $this->bookingRepository->findAll();
        
        $invoices = [];
        foreach ($bookings as $booking) {
            $invoices[] = $this->generateInvoice($booking['id']);
        }
        
        return $invoices;
    }
}

