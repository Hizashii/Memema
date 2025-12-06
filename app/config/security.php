<?php
/**
 * Security Functions
 * 
 * Comprehensive security utilities for the Cinema application
 * Includes: CSRF protection, XSS prevention, input validation, rate limiting
 */

if (!defined('CINEMA_APP')) {
    die('Direct access not allowed');
}

// Load environment configuration
require_once __DIR__ . '/env.php';

// ===========================================
// SESSION SECURITY
// ===========================================

/**
 * Initialize secure session
 */
function initSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Secure session settings
        ini_set('session.cookie_httponly', Env::get('SESSION_HTTPONLY', true) ? 1 : 0);
        ini_set('session.cookie_secure', Env::get('SESSION_SECURE', false) ? 1 : 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.gc_maxlifetime', Env::get('SESSION_LIFETIME', 7200));
        
        session_start();
        
        // Regenerate session ID periodically to prevent fixation
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 300) { // Every 5 minutes
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
}

// ===========================================
// INPUT SANITIZATION & VALIDATION
// ===========================================

/**
 * Sanitize input to prevent XSS
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize output for HTML display
 */
function e($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Validate integer within range
 */
function validateInt($value, $min = 0, $max = PHP_INT_MAX) {
    $int = filter_var($value, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => $min, 'max_range' => $max]
    ]);
    return $int !== false ? $int : 0;
}

/**
 * Validate email address
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate URL
 */
function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

/**
 * Sanitize filename to prevent directory traversal
 */
function sanitizeFilename($filename) {
    // Remove any directory components
    $filename = basename($filename);
    // Remove special characters
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    return $filename;
}

// ===========================================
// CSRF PROTECTION
// ===========================================

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    initSecureSession();
    
    $lifetime = Env::get('CSRF_TOKEN_LIFETIME', 3600);
    
    // Check if token exists and is still valid
    if (!isset($_SESSION['csrf_token']) || 
        !isset($_SESSION['csrf_token_time']) || 
        (time() - $_SESSION['csrf_token_time']) > $lifetime) {
        
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    initSecureSession();
    
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    // Use timing-safe comparison
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token input field HTML
 */
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

/**
 * Validate CSRF token from request
 * Call this at the start of POST handlers
 */
function validateCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!verifyCSRFToken($token)) {
            http_response_code(403);
            die('CSRF token validation failed. Please refresh the page and try again.');
        }
    }
}

// ===========================================
// RATE LIMITING
// ===========================================

/**
 * Check rate limit for an action
 */
function checkRateLimit($key, $max_attempts = null, $time_window = null) {
    initSecureSession();
    
    $max_attempts = $max_attempts ?? Env::get('RATE_LIMIT_MAX_ATTEMPTS', 10);
    $time_window = $time_window ?? Env::get('RATE_LIMIT_WINDOW', 300);
    
    $now = time();
    $rate_key = "rate_limit_$key";
    
    if (!isset($_SESSION[$rate_key])) {
        $_SESSION[$rate_key] = ['count' => 0, 'reset_time' => $now + $time_window];
    }
    
    $rate_data = $_SESSION[$rate_key];
    
    if ($now > $rate_data['reset_time']) {
        $_SESSION[$rate_key] = ['count' => 1, 'reset_time' => $now + $time_window];
        return true;
    }
    
    if ($rate_data['count'] >= $max_attempts) {
        return false;
    }
    
    $_SESSION[$rate_key]['count']++;
    return true;
}

/**
 * Get remaining rate limit attempts
 */
function getRateLimitRemaining($key, $max_attempts = null) {
    initSecureSession();
    
    $max_attempts = $max_attempts ?? Env::get('RATE_LIMIT_MAX_ATTEMPTS', 10);
    $rate_key = "rate_limit_$key";
    
    if (!isset($_SESSION[$rate_key])) {
        return $max_attempts;
    }
    
    return max(0, $max_attempts - $_SESSION[$rate_key]['count']);
}

// ===========================================
// PASSWORD SECURITY
// ===========================================

/**
 * Hash password securely
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
}

/**
 * Verify password against hash
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if password needs rehashing
 */
function passwordNeedsRehash($hash) {
    return password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => 12]);
}

// ===========================================
// SECURITY HEADERS
// ===========================================

/**
 * Set security headers
 * Call this at the start of your page
 */
function setSecurityHeaders() {
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Enable XSS filter
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Content Security Policy (adjust as needed)
    if (Env::isProduction()) {
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; img-src 'self' data:; font-src 'self' https://cdnjs.cloudflare.com;");
    }
    
    // HTTPS enforcement in production
    if (Env::isProduction() && Env::get('SESSION_SECURE', true)) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// ===========================================
// ERROR HANDLING
// ===========================================

/**
 * Configure error handling based on environment
 */
function configureErrorHandling() {
    if (Env::isDebug()) {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        // Log errors in production
        ini_set('log_errors', 1);
    }
}

// ===========================================
// ENCRYPTION
// ===========================================

/**
 * Encrypt data
 */
function encryptData($data) {
    $key = Env::get('APP_SECRET_KEY');
    if (strlen($key) < 32) {
        $key = hash('sha256', $key, true);
    } else {
        $key = substr($key, 0, 32);
    }
    
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    
    return base64_encode($iv . $encrypted);
}

/**
 * Decrypt data
 */
function decryptData($encryptedData) {
    $key = Env::get('APP_SECRET_KEY');
    if (strlen($key) < 32) {
        $key = hash('sha256', $key, true);
    } else {
        $key = substr($key, 0, 32);
    }
    
    $data = base64_decode($encryptedData);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}

// Initialize error handling
configureErrorHandling();
