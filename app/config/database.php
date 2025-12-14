<?php
/**
 * Database Configuration
 */

if (!defined('CINEMA_APP')) {
    define('CINEMA_APP', true);
}

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

if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_NAME')) {
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
    }
    die('
        <h1>Configuration Error</h1>
        <p>Database credentials are not properly configured in <code>secrets.php</code>.</p>
        <p>Please ensure DB_HOST, DB_USER, and DB_NAME are all defined.</p>
        <p>DB_PASS can be an empty string if your database has no password.</p>
    ');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
