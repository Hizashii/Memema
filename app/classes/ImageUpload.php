<?php
/**
 * ImageUpload Class
 * Handles image upload operations
 */
class ImageUpload {
    private $uploadDir;
    private $maxFileSize;
    private $allowedExtensions;
    
    public function __construct($uploadDir = null, $maxFileSize = 5242880) {
        // Default upload directory
        if ($uploadDir === null) {
            $uploadDir = __DIR__ . '/../../assets/img/';
        }
        
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
        $this->maxFileSize = $maxFileSize; // 5MB default
        $this->allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    }
    
    /**
     * Upload an image file
     */
    public function upload($file, $prefix = 'img_') {
        // Validate file
        $this->validateFile($file);
        
        // Get file info
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Generate unique filename
        $newFileName = $prefix . uniqid('', true) . '_' . time() . '.' . $fileExt;
        $filePath = $this->uploadDir . $newFileName;
        
        // Ensure upload directory exists
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                throw new Exception('Failed to create upload directory.');
            }
        }
        
        // Move uploaded file
        if (!move_uploaded_file($fileTmpName, $filePath)) {
            throw new Exception('Failed to move uploaded file to server.');
        }
        
        // Return file info (url will be calculated by caller to avoid dependencies)
        return [
            'filename' => $newFileName,
            'path' => $filePath,
            'relative_path' => './assets/img/' . $newFileName
        ];
    }
    
    /**
     * Validate uploaded file
     */
    private function validateFile($file) {
        if (!isset($file) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('No file uploaded or invalid file.');
        }
        
        $fileError = $file['error'];
        if ($fileError !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive.',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive.',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
            ];
            $errorMsg = $errorMessages[$fileError] ?? 'Unknown upload error: ' . $fileError;
            throw new Exception($errorMsg);
        }
        
        $fileSize = $file['size'];
        if ($fileSize > $this->maxFileSize) {
            throw new Exception('File too large. Maximum size is ' . ($this->maxFileSize / 1024 / 1024) . 'MB.');
        }
        
        $fileName = $file['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (!in_array($fileExt, $this->allowedExtensions)) {
            throw new Exception('Invalid file type. Allowed: ' . implode(', ', $this->allowedExtensions));
        }
        
        // Validate file is actually an image
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new Exception('File is not a valid image.');
        }
    }
    
    /**
     * Delete image file
     */
    public function delete($filename) {
        $filePath = $this->uploadDir . basename($filename);
        if (file_exists($filePath) && is_file($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}

