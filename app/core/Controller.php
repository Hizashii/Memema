<?php

class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    protected function view($view, $data = []) {
        View::render($view, $data);
    }
    
    protected function redirect($path) {
        $httpHost = $_SERVER['HTTP_HOST'] ?? '';
        $isProduction = strpos($httpHost, 'hostingersite.com') !== false;
        
        $queryString = '';
        if (strpos($path, '?') !== false) {
            list($path, $queryString) = explode('?', $path, 2);
        }
        
        $path = '/' . trim($path, '/');
        $path = rtrim($path, '/');
        if ($path === '/') $path = '';
        
        if ($isProduction) {
            $url = '/index.php?route=' . urlencode($path);
        } else {
            $base = $this->getBasePath();
            $url = $base . '/public/index.php?route=' . urlencode($path);
        }
        
        if (!empty($queryString)) {
            $url .= '&' . $queryString;
        }
        
        header('Location: ' . $url);
        exit;
    }
    
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    protected function get($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
    
    protected function post($key, $default = null) {
        return $_POST[$key] ?? $default;
    }
    
    protected function getBasePath() {
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

