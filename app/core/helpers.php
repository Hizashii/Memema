<?php

if (!function_exists('url')) {
    function url($path = '/') {
        $httpHost = $_SERVER['HTTP_HOST'] ?? '';
        $isProduction = strpos($httpHost, 'hostingersite.com') !== false;
        
        $queryString = '';
        if (strpos($path, '?') !== false) {
            list($path, $queryString) = explode('?', $path, 2);
        }
        
        $path = '/' . trim($path, '/');
        if ($path === '/') $path = '';
        
        if ($isProduction) {
            $url = '/index.php?route=' . urlencode($path);
        } else {
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/public/index.php';
            
            if (preg_match('#^/([^/]+)/public#', $scriptName, $matches)) {
                $base = '/' . $matches[1];
            } elseif (preg_match('#^/([^/]+)#', $scriptName, $matches)) {
                $base = '/' . $matches[1];
            } else {
                $requestUri = $_SERVER['REQUEST_URI'] ?? '';
                if (preg_match('#^/([^/]+)/public#', $requestUri, $matches)) {
                    $base = '/' . $matches[1];
                } else {
                    $base = '/Cinema';
                }
            }
            
            $url = $base . '/public/index.php?route=' . urlencode($path);
        }
        
        if (!empty($queryString)) {
            $url .= '&' . $queryString;
        }
        
        return $url;
    }
}

if (!function_exists('getBasePath')) {
    function getBasePath() {
        $httpHost = $_SERVER['HTTP_HOST'] ?? '';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/public/index.php';
        
        if (strpos($httpHost, 'hostingersite.com') !== false) {
            return '';
        }
        
        if (preg_match('#^/([^/]+)/public#', $scriptName, $matches)) {
            return '/' . $matches[1];
        } elseif (preg_match('#^/([^/]+)#', $scriptName, $matches)) {
            return '/' . $matches[1];
        } else {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            if (preg_match('#^/([^/]+)/public#', $requestUri, $matches)) {
                return '/' . $matches[1];
            }
        }
        
        return '/Cinema';
    }
}

if (!function_exists('getImagePath')) {
    function getImagePath($imagePath) {
        if (empty($imagePath)) {
            $base = getBasePath();
            return $base . '/assets/img/default.jpg';
        }
        
        if (strpos($imagePath, 'http') === 0) {
            return $imagePath;
        }
        
        $base = getBasePath();
        
        if (strpos($imagePath, './') === 0) {
            return $base . '/' . ltrim($imagePath, './');
        }
        
        if (strpos($imagePath, '/') === 0) {
            return $base . $imagePath;
        }
        
        return $base . '/assets/img/' . basename($imagePath);
    }
}

