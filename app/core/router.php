<?php
$routes = require_once __DIR__ . '/../config/routes.php';
require_once __DIR__ . '/database.php';

function route($routeName, $params = []) {
    global $routes;
    
    if (!isset($routes[$routeName])) {
        return $routeName;
    }
    
    $url = $routes[$routeName];
    $base = getBasePath();
    
    if (preg_match('#^/(Cinema|cinema)/#i', $url)) {
        if ($base) {
            $url = preg_replace('#^/(Cinema|cinema)#i', $base, $url);
        } else {
            $url = preg_replace('#^/(Cinema|cinema)/#i', '/', $url);
        }
    }
    
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    return $url;
}

function redirect($routeName, $params = []) {
    header('Location: ' . route($routeName, $params));
    exit;
}

function isRoute($routeName) {
    global $routes;
    
    if (!isset($routes[$routeName])) {
        return false;
    }
    
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $routePath = parse_url($routes[$routeName], PHP_URL_PATH);
    
    return $currentPath === $routePath;
}
