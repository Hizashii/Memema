<?php
/**
 * PRODUCTION SECRETS TEMPLATE
 * 
 * Copy this file to app/config/secrets.php on your production server
 * and fill in your actual Hostinger database credentials.
 * 
 * Get your credentials from: Hostinger hPanel -> Databases -> MySQL Databases
 */

if (!defined('CINEMA_APP')) {
    die('Direct access not allowed');
}

// ===========================================
// DATABASE CREDENTIALS
// Get these from Hostinger hPanel -> Databases
// ===========================================
define('DB_HOST', 'localhost');                    // Usually 'localhost' on Hostinger
define('DB_USER', 'u295920917_php_cinema');        // Your Hostinger database username
define('DB_PASS', 'YOUR_ACTUAL_DATABASE_PASSWORD'); // ⚠️ REPLACE THIS with your actual password from hPanel
define('DB_NAME', 'u295920917_cinema');            // Your Hostinger database name (usually starts with u295920917_)

// ===========================================
// ADMIN CREDENTIALS
// Use strong, unique credentials in production!
// ===========================================
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'CHANGE_THIS_TO_SECURE_PASSWORD'); // ⚠️ Change this!
define('ADMIN_EMAIL', 'admin@yourdomain.com');

// ===========================================
// APPLICATION SETTINGS
// ===========================================
define('APP_ENV', 'production');
define('APP_DEBUG', false);                        // Set to false in production
define('APP_URL', 'https://papayawhip-llama-422273.hostingersite.com');

// ===========================================
// SECURITY KEYS
// Generate with: php -r "echo bin2hex(random_bytes(32));"
// ===========================================
define('APP_SECRET_KEY', 'GENERATE_A_UNIQUE_KEY_HERE');

// ===========================================
// SESSION SETTINGS
// ===========================================
define('SESSION_LIFETIME', 7200);                 // 2 hours
define('SESSION_SECURE', true);                    // true for HTTPS
define('SESSION_HTTPONLY', true);

// ===========================================
// CSRF SETTINGS
// ===========================================
define('CSRF_TOKEN_LIFETIME', 3600);              // 1 hour

// ===========================================
// RATE LIMITING
// ===========================================
define('RATE_LIMIT_MAX_ATTEMPTS', 10);
define('RATE_LIMIT_WINDOW', 300);                 // 5 minutes

