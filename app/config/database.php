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
    $secretsPath = __DIR__ . '/secrets.php';
    $errorDetails = '<p><strong>secrets.php path:</strong> ' . htmlspecialchars($secretsPath) . '</p>';
    $errorDetails .= '<p><strong>File exists:</strong> ' . (file_exists($secretsPath) ? 'YES' : 'NO') . '</p>';
    
    if (file_exists($secretsPath)) {
        $errorDetails .= '<p><strong>File is readable:</strong> ' . (is_readable($secretsPath) ? 'YES' : 'NO') . '</p>';
    }
    
    $errorDetails .= '<p><strong>DB_HOST defined:</strong> ' . (defined('DB_HOST') ? 'YES (' . htmlspecialchars(DB_HOST) . ')' : 'NO') . '</p>';
    $errorDetails .= '<p><strong>DB_USER defined:</strong> ' . (defined('DB_USER') ? 'YES (' . htmlspecialchars(DB_USER) . ')' : 'NO') . '</p>';
    $errorDetails .= '<p><strong>DB_NAME defined:</strong> ' . (defined('DB_NAME') ? 'YES (' . htmlspecialchars(DB_NAME) . ')' : 'NO') . '</p>';
    $errorDetails .= '<p><strong>DB_PASS defined:</strong> ' . (defined('DB_PASS') ? 'YES' : 'NO') . '</p>';
    
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
    }
    die('
        <h1>Configuration Error</h1>
        <p>Database credentials are not properly configured in <code>secrets.php</code>.</p>
        ' . $errorDetails . '
        <p><strong>Action required:</strong> Create or update <code>app/config/secrets.php</code> on your production server with your database credentials.</p>
    ');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
