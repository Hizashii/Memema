<?php
/**
 * Authentication Middleware
 */

class AuthMiddleware {
    public function handle() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            $base = getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/admin/login');
            exit;
        }
        
        return true;
    }
}

