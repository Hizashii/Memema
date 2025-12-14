<?php
if (!isset($_GET['route'])) {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $queryString = $_SERVER['QUERY_STRING'] ?? '';
    
    if ($requestUri !== '/' && $requestUri !== '/index.php') {
        $route = parse_url($requestUri, PHP_URL_PATH);
        if ($route && $route !== '/') {
            $_GET['route'] = $route;
        }
    }
    
    if (empty($_GET['route']) && empty($queryString)) {
        $_GET['route'] = '/';
    }
}

$publicPath = __DIR__ . '/public/index.php';
if (file_exists($publicPath)) {
    require $publicPath;
} else {
    http_response_code(500);
    die('Application not configured correctly. Please contact the administrator.');
}

