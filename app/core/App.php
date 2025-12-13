<?php

class App {
    private static $instance = null;
    private $router = null;
    
    private function __construct() {
        $this->router = new Router();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function boot() {
        return $this;
    }
    
    public function loadRoutes($routeFile) {
        if (file_exists($routeFile)) {
            $router = $this->router;
            require $routeFile;
        }
    }
    
    public function run() {
        $this->router->dispatch();
    }
    
    public function getRouter() {
        return $this->router;
    }
}

