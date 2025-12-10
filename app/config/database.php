<?php
/**
 * Database Configuration
 * 
 * Loads credentials from secrets.php and sets up database constants.
 * All database operations should use the Database class in app/classes/Database.php
 */

if (!defined('CINEMA_APP')) {
    define('CINEMA_APP', true);
}

// Load secrets configuration
$secretsPath = __DIR__ . '/secrets.php';

if (!file_exists($secretsPath)) {
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
    }
    die('
        <h1>Configuration Error</h1>
        <p><strong>secrets.php</strong> file not found!</p>
        <p>Please copy <code>secrets.example.php</code> to <code>secrets.php</code> and configure your credentials.</p>
    ');
}

require_once $secretsPath;
