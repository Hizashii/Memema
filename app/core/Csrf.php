<?php

class Csrf {
    public static function generate() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $lifetime = defined('CSRF_TOKEN_LIFETIME') ? CSRF_TOKEN_LIFETIME : 3600;
        
        if (!isset($_SESSION['csrf_token']) || 
            !isset($_SESSION['csrf_token_time']) || 
            (time() - $_SESSION['csrf_token_time']) > $lifetime) {
            
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        
        return $_SESSION['csrf_token'];
    }
    
    public static function validate() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        
        if (!isset($_SESSION['csrf_token']) || empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            die('CSRF token validation failed. Please refresh the page and try again.');
        }
        
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
    }
    
    public static function field() {
        return '<input type="hidden" name="csrf_token" value="' . self::generate() . '">';
    }
}

