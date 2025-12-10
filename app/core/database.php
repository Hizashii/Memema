<?php
/**
 * Core Helper Functions
 * 
 * Utility functions for path management and image handling.
 * Database operations are handled by the Database class in app/classes/Database.php
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Get the base path for the application
 */
function getBasePath() {
    static $basePath = null;
    
    if ($basePath === null) {
        $requestUri = strtolower(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
        $scriptName = strtolower($_SERVER['SCRIPT_NAME'] ?? '');
        
        if (preg_match('#^/(cinema|Cinema)/#i', $requestUri) || preg_match('#/(cinema|Cinema)/#i', $scriptName)) {
            $actualPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
            if (preg_match('#^/([^/]+)/#', $actualPath, $matches)) {
                $basePath = '/' . $matches[1];
            } else {
                $basePath = '/cinema';
            }
        } else {
            $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
            if ($scriptDir === '/' || $scriptDir === '.') {
                $basePath = '';
            } else {
                $basePath = rtrim($scriptDir, '/');
                $basePathLower = strtolower($basePath);
                if (strpos($basePathLower, '/public/frontend') !== false) {
                    $basePath = str_ireplace('/public/frontend', '', $basePath);
                    $basePath = rtrim($basePath, '/');
                }
                if (strpos($basePathLower, '/admin') !== false) {
                    $basePath = str_ireplace('/admin', '', $basePath);
                    $basePath = rtrim($basePath, '/');
                }
            }
        }
    }
    
    return $basePath;
}

/**
 * Get the proper image path with base path prefix
 */
function getImagePath($imagePath) {
    $base = getBasePath();
    
    if (empty($imagePath)) {
        return $base . '/assets/img/default.jpg';
    }
    
    if (strpos($imagePath, 'http') === 0) {
        return $imagePath;
    }
    
    if (preg_match('#^/(Cinema|cinema)/#i', $imagePath)) {
        $imagePath = preg_replace('#^/(Cinema|cinema)/#i', $base . '/', $imagePath);
        return $imagePath;
    }
    
    if (strpos($imagePath, './') === 0) {
        $imagePath = str_replace('./', '', $imagePath);
        if (strpos($imagePath, '/') !== 0) {
            $imagePath = '/' . $imagePath;
        }
        return $base . $imagePath;
    }
    
    if (strpos($imagePath, '/') === 0) {
        return $base . $imagePath;
    }
    
    $filename = basename($imagePath);
    return $base . '/assets/img/' . $filename;
}

