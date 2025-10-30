<?php
require_once __DIR__ . '/../app/auth/admin_auth.php';
require_once __DIR__ . '/../app/config/security.php';

// Check admin login for AJAX requests
if (!isAdminLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    try {
        $uploadDir = __DIR__ . '/../assets/img/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $file = $_FILES['image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        
        // Validate file
        if ($fileError !== UPLOAD_ERR_OK) {
            throw new Exception('File upload error: ' . $fileError);
        }
        
        // Check file size (max 5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            throw new Exception('File too large. Maximum size is 5MB.');
        }
        
        // Get file extension
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($fileExt, $allowedExts)) {
            throw new Exception('Invalid file type. Allowed: ' . implode(', ', $allowedExts));
        }
        
        // Generate unique filename
        $newFileName = uniqid() . '_' . time() . '.' . $fileExt;
        $filePath = $uploadDir . $newFileName;
        
        // Move uploaded file
        if (move_uploaded_file($fileTmpName, $filePath)) {
            $relativePath = './assets/img/' . $newFileName;
            $message = json_encode(['success' => true, 'path' => $relativePath, 'filename' => $newFileName]);
        } else {
            throw new Exception('Failed to move uploaded file.');
        }
        
    } catch (Exception $e) {
        $error = json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Return JSON response
header('Content-Type: application/json');
if ($message) {
    echo $message;
} else {
    echo $error ?: json_encode(['success' => false, 'error' => 'No file uploaded']);
}
?>
