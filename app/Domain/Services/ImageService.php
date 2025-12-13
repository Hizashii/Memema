<?php
/**
 * Image Service
 */

class ImageService {
    private $uploadDir;
    private $maxFileSize = 5 * 1024 * 1024;
    private $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/x-png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];
    
    public function __construct() {
        $this->uploadDir = __DIR__ . '/../../../assets/img/';
        
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                throw new Exception('Failed to create upload directory');
            }
        }
        
        if (!is_writable($this->uploadDir)) {
            throw new Exception('Upload directory is not writable');
        }
    }
    
    public function uploadImage($file) {
        if (!isset($file) || !isset($file['tmp_name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            throw new Exception('No file uploaded');
        }
        
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
            throw new Exception($errorMsg);
        }
        
        if ($file['size'] > $this->maxFileSize) {
            throw new Exception('File too large. Maximum size is 5MB');
        }
        
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new Exception('File is not a valid image');
        }
        
        $detectedMime = $imageInfo['mime'];
        
        if (!isset($this->allowedMimeTypes[$detectedMime])) {
            throw new Exception('Invalid image type: ' . $detectedMime . '. Allowed: JPG, PNG, GIF, WebP');
        }
        
        $correctExt = $this->allowedMimeTypes[$detectedMime];
        $newFileName = 'img_' . uniqid() . '_' . time() . '.' . $correctExt;
        $filePath = $this->uploadDir . $newFileName;
        
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new Exception('Failed to move uploaded file');
        }
        
        return [
            'success' => true,
            'filename' => $newFileName,
            'path' => './assets/img/' . $newFileName,
            'relative_path' => './assets/img/' . $newFileName
        ];
    }
    
    public function deleteImage($imagePath) {
        if (empty($imagePath)) {
            return true;
        }
        
        $filePath = $imagePath;
        
        if (strpos($imagePath, './') === 0) {
            $filePath = __DIR__ . '/../../../' . ltrim($imagePath, './');
        } elseif (strpos($imagePath, '/') !== 0) {
            $filePath = $this->uploadDir . basename($imagePath);
        } else {
            if (strpos($filePath, $this->uploadDir) !== 0) {
                return true;
            }
        }
        
        $filePath = realpath($filePath);
        $uploadDirReal = realpath($this->uploadDir);
        if ($filePath && $uploadDirReal && strpos($filePath, $uploadDirReal) === 0) {
            if (file_exists($filePath) && is_file($filePath)) {
                return unlink($filePath);
            }
        }
        
        return true;
    }
    
    public function getUploadDir() {
        return $this->uploadDir;
    }
}

