<?php


if (!defined('CINEMA_APP')) {
    die('Direct access not allowed');
}

// DATABASE CREDENTIALS
define('DB_HOST', 'localhost');                
define('DB_USER', 'u295920917_u295920917');  
define('DB_PASS', 'Kookaburra!23'); 
define('DB_NAME', 'u295920917_u295920917');         
// ADMIN CREDENTIALS
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'passoword'); 
define('ADMIN_EMAIL', 'admin@yourdomain.com');

// APPLICATION SETTINGS
define('APP_ENV', 'development'); 
define('APP_DEBUG', true);       
define('APP_URL', 'http://localhost/Cinema');


define('APP_SECRET_KEY', 'dev_only_change_in_production_abc123');

// SESSION SETTINGS
define('SESSION_LIFETIME', 7200);
define('SESSION_SECURE', false);  
define('SESSION_HTTPONLY', true);

// CSRF SETTINGS
define('CSRF_TOKEN_LIFETIME', 3600);

// RATE LIMITING
define('RATE_LIMIT_MAX_ATTEMPTS', 10);
define('RATE_LIMIT_WINDOW', 300);

