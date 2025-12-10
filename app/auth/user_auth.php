<?php
/**
 * User Authentication
 * 
 * Handles user login, registration, and session management.
 * Uses OOP Database class for all operations.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';

/**
 * Check if user is logged in
 */
function isUserLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

/**
 * Login user
 */
function userLogin($email, $password) {
    try {
        $users = Database::query(
            "SELECT id, full_name, email, password, phone FROM users WHERE email = ?", 
            [$email], 
            's'
        );
        
        if (empty($users)) {
            return ['success' => false, 'error' => 'Invalid email or password'];
        }
        
        $user = $users[0];
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_phone'] = $user['phone'];
            return ['success' => true, 'user' => $user];
        } else {
            return ['success' => false, 'error' => 'Invalid email or password'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Login failed. Please try again.'];
    }
}

/**
 * Register new user
 */
function userRegister($fullName, $email, $password, $phone = null) {
    try {
        $existingUsers = Database::query(
            "SELECT id FROM users WHERE email = ?", 
            [$email], 
            's'
        );
        
        if (!empty($existingUsers)) {
            return ['success' => false, 'error' => 'Email already registered'];
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        Database::execute(
            "INSERT INTO users (full_name, email, password, phone) VALUES (?, ?, ?, ?)",
            [$fullName, $email, $hashedPassword, $phone],
            'ssss'
        );
        
        return ['success' => true, 'message' => 'Registration successful'];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Registration failed. Please try again.'];
    }
}

/**
 * Logout user
 */
function userLogout() {
    session_destroy();
    require_once __DIR__ . '/../core/router.php';
    redirect('public.home');
}

/**
 * Require user login (redirect if not logged in)
 */
function requireUserLogin() {
    if (!isUserLoggedIn()) {
        require_once __DIR__ . '/../core/database.php';
        $base = getBasePath();
        header('Location: ' . $base . '/public/frontend/pages/login.php');
        exit;
    }
}

/**
 * Get current user info from session
 */
function getCurrentUser() {
    if (isUserLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? 'User',
            'email' => $_SESSION['user_email'] ?? '',
            'phone' => $_SESSION['user_phone'] ?? ''
        ];
    }
    return null;
}

/**
 * Get user bookings
 */
function getUserBookings($userId, $limit = 10) {
    try {
        return Database::query(
            "SELECT b.id, b.show_date, b.show_time, b.seats_count, b.total_price, b.created_at,
                    m.title as movie_title, m.img as movie_img,
                    v.name as venue_name, s.screen_name, s.screen_type
             FROM bookings b
             LEFT JOIN movies m ON m.id = b.movie_id
             LEFT JOIN venues v ON v.id = b.venue_id
             LEFT JOIN screens s ON s.id = b.screen_id
             WHERE b.user_id = ?
             ORDER BY b.created_at DESC
             LIMIT ?",
            [$userId, $limit],
            'ii'
        );
    } catch (Exception $e) {
        return [];
    }
}
