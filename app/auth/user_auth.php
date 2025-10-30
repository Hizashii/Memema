<?php
/**
 * User Authentication System
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/database.php';

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
        $users = executeQuery("SELECT id, full_name, email, password, phone FROM users WHERE email = ?", [$email]);
        
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
 * Register user
 */
function userRegister($fullName, $email, $password, $phone = null) {
    try {
        // Check if user already exists
        $existingUsers = executeQuery("SELECT id FROM users WHERE email = ?", [$email]);
        
        if (!empty($existingUsers)) {
            return ['success' => false, 'error' => 'Email already registered'];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $result = executeQuery(
            "INSERT INTO users (full_name, email, password, phone) VALUES (?, ?, ?, ?)",
            [$fullName, $email, $hashedPassword, $phone]
        );
        
        if ($result) {
            return ['success' => true, 'message' => 'Registration successful'];
        } else {
            return ['success' => false, 'error' => 'Registration failed'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Registration failed. Please try again.'];
    }
}

/**
 * Logout user
 */
function userLogout() {
    session_destroy();
    // Determine the correct path based on where logout.php is called from
    $isInPages = strpos($_SERVER['REQUEST_URI'], '/pages/') !== false;
    $redirectPath = $isInPages ? '../index.php' : 'index.php';
    header('Location: ' . $redirectPath);
    exit;
}

/**
 * Require user login
 */
function requireUserLogin() {
    if (!isUserLoggedIn()) {
        // Determine the correct path based on where this function is called from
        $isInPages = strpos($_SERVER['REQUEST_URI'], '/pages/') !== false;
        $loginPath = $isInPages ? 'login.php' : 'pages/login.php';
        header('Location: ' . $loginPath);
        exit;
    }
}

/**
 * Get current user info
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
 * Get user booking history
 */
function getUserBookings($userId, $limit = 10) {
    try {
        return executeQuery(
            "SELECT b.id, b.show_date, b.show_time, b.seats_count, b.total_price, b.created_at,
                    m.title as movie_title, m.img as movie_img,
                    v.name as venue_name, s.screen_name, s.screen_type
             FROM bookings b
             JOIN movies m ON m.id = b.movie_id
             JOIN venues v ON v.id = b.venue_id
             JOIN screens s ON s.id = b.screen_id
             WHERE b.user_id = ?
             ORDER BY b.created_at DESC
             LIMIT ?",
            [$userId, $limit]
        );
    } catch (Exception $e) {
        return [];
    }
}
?>
