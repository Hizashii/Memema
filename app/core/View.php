<?php

class View {
    private static $basePath = null;
    
    public static function render($view, $data = []) {
        if (!function_exists('url')) {
            require_once __DIR__ . '/helpers.php';
        }
        if (!function_exists('getImagePath')) {
            require_once __DIR__ . '/helpers.php';
        }
        
        extract($data);
        
        $viewPath = self::getViewPath($view);
        if (!file_exists($viewPath)) {
            die("View not found: " . $view);
        }
        
        require $viewPath;
    }
    
    public static function partial($partial, $data = []) {
        extract($data);
        
        $partialPath = self::getViewPath('partials/' . $partial);
        if (!file_exists($partialPath)) {
            die("Partial not found: " . $partial);
        }
        
        require $partialPath;
    }
    
    public static function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    private static function getViewPath($view) {
        if (self::$basePath === null) {
            self::$basePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
        }
        
        $view = str_replace('.', DIRECTORY_SEPARATOR, $view);
        $viewPath = self::$basePath . $view . '.php';
        
        return $viewPath;
    }
}

