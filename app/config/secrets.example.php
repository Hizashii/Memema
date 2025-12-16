<?php


if (!defined('CINEMA_APP')) {
    die('Direct access not allowed');
}

// DATABASE CREDENTIALS
define('DB_HOST', 'localhost');                
define('DB_USER', 'your_database_username');  
define('DB_PASS', 'your_database_password'); 
define('DB_NAME', 'your_database_name');         

// ADMIN CREDENTIALS
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'change_this_password'); 
define('ADMIN_EMAIL', 'admin@yourdomain.com');

// APPLICATION SETTINGS
define('APP_ENV', 'development'); 
define('APP_DEBUG', true);     
define('APP_URL', 'http://localhost/Cinema');

define('APP_SECRET_KEY', 'CHANGE_THIS_TO_A_RANDOM_SECRET_KEY_IN_PRODUCTION');

// SESSION SETTINGS
define('SESSION_LIFETIME', 7200);
define('SESSION_SECURE', false);  
define('SESSION_HTTPONLY', true);

// CSRF SETTINGS
define('CSRF_TOKEN_LIFETIME', 3600);

// RATE LIMITING
define('RATE_LIMIT_MAX_ATTEMPTS', 10);
define('RATE_LIMIT_WINDOW', 300);

