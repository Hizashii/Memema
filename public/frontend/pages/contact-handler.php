<?php
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/core/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (empty($subject)) {
    $errors[] = 'Subject is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    $result = executeQuery(
        "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)",
        [$name, $email, $subject, $message]
    );
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Thank you for your message! We will get back to you soon.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to send message. Please try again.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to send message. Please try again.']);
}
?>
