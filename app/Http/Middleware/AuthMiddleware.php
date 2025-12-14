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
            $httpHost = $_SERVER['HTTP_HOST'] ?? '';
            $isProduction = strpos($httpHost, 'hostingersite.com') !== false;
            
            if ($isProduction) {
                header('Location: /index.php?route=/admin/login');
            } else {
                $base = getBasePath();
                header('Location: ' . $base . '/public/index.php?route=/admin/login');
            }
            exit;
        }
        
        return true;
    }
}

