<?php
/**
 * Authentication Service
 */

class AuthService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function authenticateAdmin($username, $password) {
        if (!defined('ADMIN_USERNAME')) {
            require_once __DIR__ . '/../../config/secrets.php';
        }
        
        if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_email'] = ADMIN_EMAIL;
            
            return true;
        }
        
        return false;
    }
    
    public function isAdminLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    public function logoutAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_email']);
    }
    
    public function authenticateUser($email, $password) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $user = $this->db->queryOne($sql, [$email]);
        
        if ($user && password_verify($password, $user['password'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_phone'] = $user['phone'] ?? '';
            
            return true;
        }
        
        return false;
    }
    
    public function isUserLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
    }
    
    public function getCurrentUser() {
        if (!$this->isUserLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? 'User',
            'email' => $_SESSION['user_email'] ?? '',
            'phone' => $_SESSION['user_phone'] ?? ''
        ];
    }
    
    public function registerUser($fullName, $email, $password, $phone = null) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format.');
        }
        
        $sql = "SELECT id FROM users WHERE email = ?";
        $existing = $this->db->queryOne($sql, [$email]);
        if ($existing) {
            throw new Exception('Email already registered.');
        }
        
        if (strlen($password) < 6) {
            throw new Exception('Password must be at least 6 characters long.');
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, password, phone) VALUES (?, ?, ?, ?)";
        $userId = $this->db->execute($sql, [$fullName, $email, $hashedPassword, $phone]);
        
        if ($userId) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $fullName;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_phone'] = $phone ?? '';
            
            return true;
        }
        
        return false;
    }
    
    public function logoutUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        unset($_SESSION['user_logged_in']);
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_phone']);
    }
}

