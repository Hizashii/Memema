<?php
/**
 * Admin Credentials Configuration
 * 
 * Credentials are loaded from secrets.php
 * NEVER commit secrets.php with real credentials!
 */

if (!defined('CINEMA_APP')) {
    define('CINEMA_APP', true);
}

// Load secrets if not already loaded
if (!defined('ADMIN_USERNAME')) {
    require_once __DIR__ . '/secrets.php';
}

return [
    'username' => ADMIN_USERNAME,
    'password' => ADMIN_PASSWORD,
    'email' => ADMIN_EMAIL
];
