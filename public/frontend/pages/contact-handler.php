<?php
/**
 * Contact Form Handler
 * 
 * Handles contact form submissions with CSRF protection and rate limiting
 */

if (!defined('CINEMA_APP')) {
    define('CINEMA_APP', true);
}

require_once __DIR__ . '/../../../app/config/security.php';
require_once __DIR__ . '/../../../app/classes/autoload.php';

header('Content-Type: application/json');

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Verify CSRF token
$csrfToken = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($csrfToken)) {
    echo json_encode(['success' => false, 'error' => 'Security validation failed. Please refresh the page and try again.']);
    exit;
}

// Check rate limiting (max 5 messages per 10 minutes)
if (!checkRateLimit('contact_form', 5, 600)) {
    echo json_encode(['success' => false, 'error' => 'Too many messages. Please wait a few minutes before trying again.']);
    exit;
}

// Sanitize and validate inputs
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

// Validate name
if (empty($name)) {
    $errors[] = 'Name is required';
} elseif (strlen($name) < 2 || strlen($name) > 100) {
    $errors[] = 'Name must be between 2 and 100 characters';
}

// Validate email
if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

// Validate subject
if (empty($subject)) {
    $errors[] = 'Subject is required';
} elseif (strlen($subject) < 3 || strlen($subject) > 200) {
    $errors[] = 'Subject must be between 3 and 200 characters';
}

// Validate message
if (empty($message)) {
    $errors[] = 'Message is required';
} elseif (strlen($message) < 10 || strlen($message) > 5000) {
    $errors[] = 'Message must be between 10 and 5000 characters';
}

// Return errors if any
if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    // Create contact message using ContactMessage class
    $contactMessage = new ContactMessage([
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'status' => 'new'
    ]);
    
    if ($contactMessage->create()) {
        // Generate new CSRF token for the next request
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Thank you for your message! We will get back to you soon.',
            'new_csrf_token' => $_SESSION['csrf_token']
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to send message. Please try again.']);
    }
    
} catch (Exception $e) {
    // Log error for debugging
    error_log('Contact form error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to send message. Please try again.']);
}
