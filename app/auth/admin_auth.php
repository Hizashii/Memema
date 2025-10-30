<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('CINEMA_ADMIN', true);

$admin_credentials = [
    'username' => 'admin',
    'password' => 'admin123', // Change this in production!
    'email' => 'admin@cinemabook.com'
];

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function adminLogin($username, $password) {
    global $admin_credentials;
    
    // In production, store hashed passwords in database
    if ($username === $admin_credentials['username'] && 
        $password === $admin_credentials['password']) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_email'] = $admin_credentials['email'];
        return true;
    }
    return false;
}

function adminLogout() {
    session_destroy();
    header('Location: /Cinema/admin/login.php');
    exit;
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: /Cinema/admin/login.php');
        exit;
    }
}

function getAdminInfo() {
    if (isAdminLoggedIn()) {
        return [
            'username' => $_SESSION['admin_username'] ?? 'Admin',
            'email' => $_SESSION['admin_email'] ?? 'admin@cinemabook.com'
        ];
    }
    return null;
}
