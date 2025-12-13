<?php
/**
 * Security Functions
 */

if (!defined('CINEMA_APP')) {
    die('Direct access not allowed');
}

if (!defined('APP_SECRET_KEY')) {
    require_once __DIR__ . '/secrets.php';
}

function initSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', defined('SESSION_HTTPONLY') && SESSION_HTTPONLY ? 1 : 0);
        ini_set('session.cookie_secure', defined('SESSION_SECURE') && SESSION_SECURE ? 1 : 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.gc_maxlifetime', defined('SESSION_LIFETIME') ? SESSION_LIFETIME : 7200);
        
        session_start();
        
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 300) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
}

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function e($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

function validateInt($value, $min = 0, $max = PHP_INT_MAX) {
    $int = filter_var($value, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => $min, 'max_range' => $max]
    ]);
    return $int !== false ? $int : 0;
}

function generateCSRFToken() {
    initSecureSession();
    
    $lifetime = defined('CSRF_TOKEN_LIFETIME') ? CSRF_TOKEN_LIFETIME : 3600;
    
    if (!isset($_SESSION['csrf_token']) || 
        !isset($_SESSION['csrf_token_time']) || 
        (time() - $_SESSION['csrf_token_time']) > $lifetime) {
        
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    initSecureSession();
    
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

function getCSRFToken() {
    return generateCSRFToken();
}

function validateCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!verifyCSRFToken($token)) {
            http_response_code(403);
            die('CSRF token validation failed. Please refresh the page and try again.');
        }
    }
}

function checkRateLimit($key, $max_attempts = null, $time_window = null) {
    initSecureSession();
    
    $max_attempts = $max_attempts ?? (defined('RATE_LIMIT_MAX_ATTEMPTS') ? RATE_LIMIT_MAX_ATTEMPTS : 10);
    $time_window = $time_window ?? (defined('RATE_LIMIT_WINDOW') ? RATE_LIMIT_WINDOW : 300);
    
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

function isProduction() {
    return defined('APP_ENV') && APP_ENV === 'production';
}

function isDebug() {
    return defined('APP_DEBUG') && APP_DEBUG === true;
}

function setSecurityHeaders() {
    if (headers_sent()) return;
    
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    if (isProduction()) {
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; img-src 'self' data:; font-src 'self' https://cdnjs.cloudflare.com;");
    }
    
    if (isProduction() && defined('SESSION_SECURE') && SESSION_SECURE) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function configureErrorHandling() {
    if (isDebug()) {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        ini_set('log_errors', 1);
    }
}

configureErrorHandling();
