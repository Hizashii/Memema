<?php
/**
 * Contact Controller
 */

class ContactController extends Controller {
    private $contactInfoRepository;
    private $contactMessageRepository;
    private $mailService;
    
    public function __construct() {
        parent::__construct();
        $this->contactInfoRepository = new ContactInfoRepository();
        $this->contactMessageRepository = new ContactMessageRepository();
        $this->mailService = new MailService();
    }
    
    public function index() {
        try {
            $contactInfo = $this->contactInfoRepository->find();
            if (!$contactInfo) {
                $contactInfo = [
                    'phone' => '',
                    'email' => '',
                    'address' => ''
                ];
            }
        } catch (Exception $e) {
            $contactInfo = [
                'phone' => '',
                'email' => '',
                'address' => ''
            ];
        }
        
        $this->view('front/contact/index', [
            'contactInfo' => $contactInfo,
            'csrfToken' => Csrf::generate(),
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ]);
    }
    
    public function submit() {
        if (!$this->isPost()) {
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/contact');
            exit;
        }
        
        Csrf::validate();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $rateKey = 'contact_form_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $rateLimit = $_SESSION[$rateKey] ?? ['count' => 0, 'time' => 0];
        
        if (time() - $rateLimit['time'] < 600) {
            if ($rateLimit['count'] >= 5) {
                $base = $this->getBasePath();
                header('Location: ' . $base . '/public/index.php?route=/contact&error=' . urlencode('Too many messages. Please wait before sending another.'));
                exit;
            }
            $rateLimit['count']++;
        } else {
            $rateLimit = ['count' => 1, 'time' => time()];
        }
        $_SESSION[$rateKey] = $rateLimit;
        
        // Get and validate input
        $name = trim($this->post('name', ''));
        $email = trim($this->post('email', ''));
        $subject = trim($this->post('subject', ''));
        $message = trim($this->post('message', ''));
        
        $errors = [];
        
        if (empty($name) || strlen($name) < 2) {
            $errors[] = 'Name must be at least 2 characters';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email address is required';
        }
        
        if (empty($subject) || strlen($subject) < 3) {
            $errors[] = 'Subject must be at least 3 characters';
        }
        
        if (empty($message) || strlen($message) < 10) {
            $errors[] = 'Message must be at least 10 characters';
        }
        
        if (!empty($errors)) {
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/contact&error=' . urlencode(implode('. ', $errors)));
            exit;
        }
        
        try {
            $messageId = $this->contactMessageRepository->create([
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'status' => 'new'
            ]);
            
            try {
                $emailSent = $this->mailService->sendContactFormEmail($name, $email, $subject, $message);
                
                if ($emailSent) {
                    $base = $this->getBasePath();
                    header('Location: ' . $base . '/public/index.php?route=/contact&success=' . urlencode('Thank you! Your message has been sent successfully.'));
                    exit;
                } else {
                    error_log("Contact form email failed to send. Message saved to database with ID: " . $messageId);
                    $base = $this->getBasePath();
                    header('Location: ' . $base . '/public/index.php?route=/contact&success=' . urlencode('Your message has been received. We will get back to you soon.'));
                    exit;
                }
            } catch (Exception $e) {
                error_log("Contact form email error: " . $e->getMessage());
                $base = $this->getBasePath();
                header('Location: ' . $base . '/public/index.php?route=/contact&success=' . urlencode('Your message has been received. We will get back to you soon.'));
                exit;
            }
            
        } catch (Exception $e) {
            error_log("Contact form error: " . $e->getMessage());
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/contact&error=' . urlencode('Failed to send message: ' . $e->getMessage()));
            exit;
        }
    }
}

