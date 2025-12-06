<?php
/**
 * Environment Configuration Example
 * 
 * INSTRUCTIONS:
 * 1. Copy this file to 'environment.php'
 * 2. Fill in your actual values
 * 3. NEVER commit environment.php to version control
 */

return [
    // ===========================================
    // DATABASE CONFIGURATION
    // ===========================================
    'DB_HOST' => 'localhost',
    'DB_USER' => 'your_database_user',
    'DB_PASS' => 'your_database_password',
    'DB_NAME' => 'Cinema',
    
    // ===========================================
    // ADMIN CREDENTIALS
    // ===========================================
    'ADMIN_USERNAME' => 'admin',
    'ADMIN_PASSWORD' => 'change_to_secure_password', // Use a strong password!
    'ADMIN_EMAIL' => 'admin@yourdomain.com',
    
    // ===========================================
    // APPLICATION SETTINGS
    // ===========================================
    'APP_ENV' => 'production',
    'APP_DEBUG' => false, // NEVER enable debug in production
    'APP_URL' => 'https://yourdomain.com',
    
    // ===========================================
    // SECURITY SETTINGS
    // ===========================================
    // Generate with: php -r "echo bin2hex(random_bytes(32));"
    'APP_SECRET_KEY' => 'generate_a_64_character_random_string',
    
    // Session settings
    'SESSION_LIFETIME' => 7200,
    'SESSION_SECURE' => true, // Requires HTTPS
    'SESSION_HTTPONLY' => true,
    
    // CSRF token lifetime in seconds
    'CSRF_TOKEN_LIFETIME' => 3600,
    
    // Rate limiting
    'RATE_LIMIT_MAX_ATTEMPTS' => 10,
    'RATE_LIMIT_WINDOW' => 300,
];

