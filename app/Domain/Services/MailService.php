<?php
/**
 * Mail Service
 */

class MailService {
    private $fromEmail;
    private $fromName;
    
    public function __construct() {
        $contactRepo = new ContactInfoRepository();
        $contactInfo = $contactRepo->find();
        
        $this->fromEmail = $contactInfo['email'] ?? 'noreply@cinemabook.com';
        $this->fromName = 'CinemaBook';
    }
    
    public function sendContactFormEmail($name, $email, $subject, $message) {
        $adminEmail = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : null;
        
        if (!$adminEmail) {
            $contactRepo = new ContactInfoRepository();
            $contactInfo = $contactRepo->find();
            $adminEmail = $contactInfo['email'] ?? 'admin@cinemabook.com';
        }
        
        $emailSubject = "Contact Form: " . $subject;
        $emailBody = $this->buildContactEmailBody($name, $email, $subject, $message);
        $headers = $this->buildHeaders($name, $email);
        
        $result = @mail($adminEmail, $emailSubject, $emailBody, $headers);
        
        if (!$result) {
            error_log("Failed to send contact form email to: " . $adminEmail);
        }
        
        return $result;
    }
    
    public function sendBookingNotificationToAdmin($bookingDetails) {
        $adminEmail = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : null;
        
        if (!$adminEmail) {
            $contactRepo = new ContactInfoRepository();
            $contactInfo = $contactRepo->find();
            $adminEmail = $contactInfo['email'] ?? 'admin@cinemabook.com';
        }
        
        $subject = "New Booking - " . $bookingDetails['booking_number'];
        $body = $this->buildBookingNotificationBody($bookingDetails);
        $headers = $this->buildHeaders();
        
        $result = @mail($adminEmail, $subject, $body, $headers);
        
        if (!$result) {
            error_log("Failed to send booking notification to admin: " . $adminEmail);
        }
        
        return $result;
    }
    
    private function buildBookingNotificationBody($bookingDetails) {
        $body = "New booking received!\n\n";
        $body .= "Booking Details:\n";
        $body .= str_repeat("-", 50) . "\n";
        $body .= "Booking ID: " . $bookingDetails['booking_id'] . "\n";
        $body .= "Booking Number: " . $bookingDetails['booking_number'] . "\n";
        $body .= "Customer: " . $bookingDetails['customer_name'] . "\n";
        $body .= "Customer Email: " . $bookingDetails['customer_email'] . "\n";
        $body .= "Movie: " . $bookingDetails['movie_title'] . "\n";
        $body .= "Venue: " . $bookingDetails['venue_name'] . "\n";
        $body .= "Screen: " . $bookingDetails['screen_name'] . "\n";
        $body .= "Date: " . $bookingDetails['show_date'] . "\n";
        $body .= "Time: " . $bookingDetails['show_time'] . "\n";
        $body .= "Seats: " . $bookingDetails['seats_count'] . "\n";
        $body .= "Seat Details: " . $bookingDetails['seat_details'] . "\n";
        $body .= "Total: $" . number_format($bookingDetails['total_price'], 2) . "\n";
        $body .= "Status: " . $bookingDetails['status'] . "\n";
        $body .= str_repeat("-", 50) . "\n\n";
        $body .= "Please check the admin dashboard for more details.\n";
        
        return $body;
    }
    
    private function buildContactEmailBody($name, $email, $subject, $message) {
        $body = "New contact form submission from CinemaBook website\n\n";
        $body .= "Name: " . $name . "\n";
        $body .= "Email: " . $email . "\n";
        $body .= "Subject: " . $subject . "\n\n";
        $body .= "Message:\n";
        $body .= str_repeat("-", 50) . "\n";
        $body .= $message . "\n";
        $body .= str_repeat("-", 50) . "\n\n";
        $body .= "---\n";
        $body .= "This email was sent from the CinemaBook contact form.\n";
        $body .= "Reply to: " . $email . "\n";
        
        return $body;
    }
    
    private function buildHeaders($replyName = null, $replyEmail = null) {
        $headers = [];
        $headers[] = "From: " . $this->fromName . " <" . $this->fromEmail . ">";
        $headers[] = "Reply-To: " . ($replyName ?? '') . " <" . ($replyEmail ?? $this->fromEmail) . ">";
        $headers[] = "X-Mailer: PHP/" . phpversion();
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/plain; charset=UTF-8";
        
        return implode("\r\n", $headers);
    }
    
    public function sendBookingConfirmation($userEmail, $userName, $bookingDetails) {
        $subject = "Booking Confirmation - " . $bookingDetails['booking_number'];
        $body = $this->buildBookingConfirmationBody($userName, $bookingDetails);
        $headers = $this->buildHeaders();
        
        return mail($userEmail, $subject, $body, $headers);
    }
    
    private function buildBookingConfirmationBody($userName, $bookingDetails) {
        $body = "Dear " . $userName . ",\n\n";
        $body .= "Thank you for your booking with CinemaBook!\n\n";
        $body .= "Booking Details:\n";
        $body .= str_repeat("-", 50) . "\n";
        $body .= "Booking Number: " . $bookingDetails['booking_number'] . "\n";
        $body .= "Movie: " . $bookingDetails['movie_title'] . "\n";
        $body .= "Venue: " . $bookingDetails['venue_name'] . "\n";
        $body .= "Screen: " . $bookingDetails['screen_name'] . "\n";
        $body .= "Date: " . $bookingDetails['show_date'] . "\n";
        $body .= "Time: " . $bookingDetails['show_time'] . "\n";
        $body .= "Seats: " . $bookingDetails['seats_count'] . "\n";
        $body .= "Total: $" . number_format($bookingDetails['total_price'], 2) . "\n";
        $body .= str_repeat("-", 50) . "\n\n";
        $body .= "We look forward to seeing you at the cinema!\n\n";
        $body .= "Best regards,\n";
        $body .= "CinemaBook Team\n";
        
        return $body;
    }
}

