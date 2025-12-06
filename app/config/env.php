<?php
/**
 * Environment Configuration Loader
 * 
 * Loads environment variables from environment.php file
 * This allows sensitive data to be stored outside of version control
 */

class Env {
    private static $loaded = false;
    private static $vars = [];
    
    /**
     * Load environment variables from environment.php file
     */
    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }
        
        if ($path === null) {
            $path = __DIR__ . '/environment.php';
        }
        
        if (!file_exists($path)) {
            // Fall back to environment.example.php for development
            $examplePath = __DIR__ . '/environment.example.php';
            if (file_exists($examplePath)) {
                $path = $examplePath;
            } else {
                throw new Exception('environment.php not found. Please copy environment.example.php to environment.php and configure it.');
            }
        }
        
        self::$vars = require $path;
        self::$loaded = true;
    }
    
    /**
     * Get an environment variable
     */
    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }
        
        return self::$vars[$key] ?? $default;
    }
    
    /**
     * Check if running in production
     */
    public static function isProduction() {
        return self::get('APP_ENV', 'development') === 'production';
    }
    
    /**
     * Check if debug mode is enabled
     */
    public static function isDebug() {
        return filter_var(self::get('APP_DEBUG', true), FILTER_VALIDATE_BOOLEAN);
    }
    
    /**
     * Get all environment variables
     */
    public static function all() {
        if (!self::$loaded) {
            self::load();
        }
        return self::$vars;
    }
}

// Auto-load on include
Env::load();

