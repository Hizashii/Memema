<?php
header('Content-Type: application/json');

// Define constant before including security.php
if (!defined('CINEMA_APP')) {
    define('CINEMA_APP', true);
}

// Step 1: Load admin auth
try {
    require_once __DIR__ . '/../app/auth/admin_auth.php';
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Error loading admin_auth: ' . $e->getMessage(), 'step' => 1]);
    exit;
}

// Step 2: Check authentication
if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required', 'step' => 2]);
    exit;
}

// Step 3: Check request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded', 'step' => 3]);
    exit;
}

// Step 4: Check file error
if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Upload error: ' . $_FILES['image']['error'], 'step' => 4]);
    exit;
}

// Step 5: Process upload manually (no OOP class)
try {
    $uploadDir = __DIR__ . '/../assets/img/';
    
    // Create directory if needed
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Get file info
    $file = $_FILES['image'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    
    // Validate size (5MB max)
    if ($fileSize > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'error' => 'File too large (max 5MB)', 'step' => 5]);
        exit;
    }
    
    // Validate extension
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($fileExt, $allowedExts)) {
        echo json_encode(['success' => false, 'error' => 'Invalid file type', 'step' => 5]);
        exit;
    }
    
    // Validate it's actually an image
    $imageInfo = @getimagesize($fileTmpName);
    if ($imageInfo === false) {
        echo json_encode(['success' => false, 'error' => 'File is not a valid image', 'step' => 5]);
        exit;
    }
    
    // Generate unique filename
    $newFileName = 'img_' . uniqid() . '_' . time() . '.' . $fileExt;
    $filePath = $uploadDir . $newFileName;
    
    // Move file
    if (!move_uploaded_file($fileTmpName, $filePath)) {
        echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file', 'step' => 5]);
        exit;
    }
    
    // Success!
    echo json_encode([
        'success' => true,
        'path' => '/Cinema/assets/img/' . $newFileName,
        'filename' => $newFileName,
        'relative_path' => './assets/img/' . $newFileName
    ]);
    
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Upload error: ' . $e->getMessage(), 'step' => 5]);
}
?>
