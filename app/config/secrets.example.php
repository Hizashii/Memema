<?php


if (!defined('CINEMA_APP')) {
    die('Direct access not allowed');
}

// ===========================================
// DATABASE CREDENTIALS
// ===========================================
define('DB_HOST', 'localhost');
define('DB_USER', 'your_database_user');        // Change this
define('DB_PASS', 'your_database_password');    // Change this
define('DB_NAME', 'Cinema');                    // Change this if different

// ADMIN CREDENTIALS
// Use strong, unique credentials in production!
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'passoword'); 
define('ADMIN_EMAIL', 'admin@yourdomain.com');

// ===========================================
// APPLICATION SETTINGS
// ===========================================
define('APP_ENV', 'development'); // Change to 'production' when deploying
define('APP_DEBUG', true);        // Set to false in production
define('APP_URL', 'http://localhost/Cinema');

// ===========================================
// SECURITY KEYS
// IMPORTANT: Generate a unique key for production!
// Run: php -r "echo bin2hex(random_bytes(32));"
// ===========================================
define('APP_SECRET_KEY', 'dev_only_change_in_production_abc123');

// ===========================================
// SESSION SETTINGS
// ===========================================
define('SESSION_LIFETIME', 7200); // 2 hours
define('SESSION_SECURE', false);  // Set to true when using HTTPS
define('SESSION_HTTPONLY', true);

// ===========================================
// CSRF SETTINGS
// ===========================================
define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour

// ===========================================
// RATE LIMITING
// ===========================================
define('RATE_LIMIT_MAX_ATTEMPTS', 10);
define('RATE_LIMIT_WINDOW', 300); // 5 minutes

