<?php
/**
 * Image Upload Handler
 * 
 * Handles image uploads with proper security checks
 * Auto-detects correct file extension from content
 */

// Set JSON header first to ensure proper response format
header('Content-Type: application/json');

// Define constant before including other files
if (!defined('CINEMA_APP')) {
    define('CINEMA_APP', true);
}

// Disable error display to prevent breaking JSON output
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Custom error handler to catch errors and return as JSON
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

try {
    // Load admin authentication
    require_once __DIR__ . '/../app/auth/admin_auth.php';
    
    // Check authentication
    if (!isAdminLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Authentication required']);
        exit;
    }
    
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'Invalid request method']);
        exit;
    }
    
    // Check if file was uploaded
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        echo json_encode(['success' => false, 'error' => 'No file uploaded']);
        exit;
    }
    
    // Check for upload errors
    $file = $_FILES['image'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server upload limit',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form upload limit',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload'
        ];
        $errorMsg = $errorMessages[$file['error']] ?? 'Unknown upload error';
        echo json_encode(['success' => false, 'error' => $errorMsg]);
        exit;
    }
    
    // Validate file size (5MB max)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        echo json_encode(['success' => false, 'error' => 'File too large. Maximum size is 5MB']);
        exit;
    }
    
    // Validate it's actually an image using getimagesize
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        echo json_encode(['success' => false, 'error' => 'File is not a valid image']);
        exit;
    }
    
    // Map MIME types to file extensions (use actual content, not filename)
    $mimeToExt = [
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/x-png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];
    
    $detectedMime = $imageInfo['mime'];
    
    // Check if the detected MIME type is allowed
    if (!isset($mimeToExt[$detectedMime])) {
        echo json_encode(['success' => false, 'error' => 'Invalid image type: ' . $detectedMime . '. Allowed: JPG, PNG, GIF, WebP']);
        exit;
    }
    
    // Use the correct extension based on actual file content (not the uploaded filename)
    $correctExt = $mimeToExt[$detectedMime];
    
    // Set upload directory
    $uploadDir = __DIR__ . '/../assets/img/';
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            echo json_encode(['success' => false, 'error' => 'Failed to create upload directory']);
            exit;
        }
    }
    
    // Check directory is writable
    if (!is_writable($uploadDir)) {
        echo json_encode(['success' => false, 'error' => 'Upload directory is not writable']);
        exit;
    }
    
    // Generate unique filename with CORRECT extension based on content
    $newFileName = 'img_' . uniqid() . '_' . time() . '.' . $correctExt;
    $filePath = $uploadDir . $newFileName;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file']);
        exit;
    }
    
    // Success response
    echo json_encode([
        'success' => true,
        'filename' => $newFileName,
        'path' => './assets/img/' . $newFileName,
        'relative_path' => './assets/img/' . $newFileName
    ]);
    
} catch (Throwable $e) {
    // Log error for debugging (in production, log to file instead)
    error_log('Upload error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    
    // Return generic error message
    echo json_encode([
        'success' => false, 
        'error' => 'Upload failed: ' . $e->getMessage()
    ]);
}

// Restore error handler
restore_error_handler();
