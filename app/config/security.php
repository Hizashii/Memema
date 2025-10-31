<?php
if (!defined('CINEMA_APP')) {
    die('Direct access not allowed');
}
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validateInt($value, $min = 0, $max = PHP_INT_MAX) {
    $int = filter_var($value, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => $min, 'max_range' => $max]
    ]);
    return $int !== false ? $int : 0;
}
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}


function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function checkRateLimit($key, $max_attempts = 10, $time_window = 300) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
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
