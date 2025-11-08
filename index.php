<?php
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$requestPath = parse_url($requestUri, PHP_URL_PATH);

if (strpos($requestPath, '/admin/') === 0 || 
    strpos($requestPath, '/assets/') === 0 || 
    strpos($requestPath, '/uploads/') === 0 ||
    strpos($requestPath, '/public/') === 0) {
    http_response_code(404);
    exit;
}

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = dirname($scriptName);
$basePath = '';

if (preg_match('#^/(cinema|Cinema)/#i', $requestPath) || preg_match('#/(cinema|Cinema)/#i', $scriptName)) {
    if (preg_match('#^/([^/]+)/#', $requestPath, $matches)) {
        $basePath = '/' . $matches[1];
    } elseif (preg_match('#/([^/]+)/#', $scriptName, $matches)) {
        $basePath = '/' . $matches[1];
    } else {
        $basePath = '/cinema';
    }
}

header('Location: ' . $basePath . '/public/frontend/index.php');
exit;
?>
