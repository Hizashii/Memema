<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('CINEMA_ADMIN', true);

$admin_credentials = require __DIR__ . '/../config/admin_credentials.php';

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function adminLogin($username, $password) {
    global $admin_credentials;
    
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
    require_once __DIR__ . '/../core/database.php';
    $base = getBasePath();
    header('Location: ' . $base . '/admin/login.php');
    exit;
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        require_once __DIR__ . '/../core/database.php';
        $base = getBasePath();
        header('Location: ' . $base . '/admin/login.php');
        exit;
    }
}

function getAdminInfo() {
    return [
        'username' => $_SESSION['admin_username'] ?? 'Admin',
        'email' => $_SESSION['admin_email'] ?? 'admin@cinemabook.com'
    ];
}
