<?php

class Router {
    private $routes = [];
    
    public function get($uri, $handler, $middleware = []) {
        $this->addRoute('GET', $uri, $handler, $middleware);
    }
    
    public function post($uri, $handler, $middleware = []) {
        $this->addRoute('POST', $uri, $handler, $middleware);
    }
    
    private function addRoute($method, $uri, $handler, $middleware) {
        $this->routes[] = ['method' => $method, 'uri' => $uri, 'handler' => $handler, 'middleware' => $middleware];
    }
    
    public function dispatch() {
        $uri = $this->getUri();
        $method = $_SERVER['REQUEST_METHOD'];
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchUri($route['uri'], $uri, $params)) {
                
                foreach ($route['middleware'] as $middlewareClass) {
                    if (!class_exists($middlewareClass)) {
                        $middlewarePath = str_replace('App\\', '', $middlewareClass);
                        $middlewarePath = APP_PATH . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $middlewarePath) . '.php';
                        if (file_exists($middlewarePath)) {
                            require_once $middlewarePath;
                        } else {
                            throw new Exception("Middleware class not found: " . $middlewareClass . " at " . $middlewarePath);
                        }
                    }
                    $className = basename(str_replace('\\', '/', $middlewareClass));
                    $middlewareInstance = new $className();
                    $middlewareInstance->handle();
                }
                
                $this->callHandler($route['handler'], $params);
                return;
            }
        }
        
        http_response_code(404);
        $this->render404($uri, $method);
    }
    
    private function getUri() {
        if (isset($_GET['route'])) {
            $uri = $_GET['route'];
            $uri = '/' . trim($uri, '/');
            return rtrim($uri, '/') ?: '/';
        }
        
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        
        $basePath = str_replace('index.php', '', $scriptName);
        $uri = '/' . trim(str_replace($basePath, '', $requestUri), '/');
        
        if (strpos($uri, '?') !== false) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        
        if (empty($uri) || $uri === '/') {
            $uri = '/';
        }
        
        return rtrim($uri, '/') ?: '/';
    }
    
    private function matchUri($routeUri, $requestUri, &$params) {
        $params = [];
        
        $routeUri = rtrim($routeUri, '/');
        $requestUri = rtrim($requestUri, '/');
        
        if ($routeUri === '' && $requestUri === '') {
            return true;
        }
        
        if ($routeUri === '' || $requestUri === '') {
            return false;
        }
        
        $routeParts = explode('/', trim($routeUri, '/'));
        $requestParts = explode('/', trim($requestUri, '/'));
        
        if (count($routeParts) !== count($requestParts)) {
            return false;
        }
        
        foreach ($routeParts as $key => $part) {
            if (preg_match('/^{([a-zA-Z0-9_]+)}$/', $part, $matches)) {
                $params[$matches[1]] = $requestParts[$key];
            } elseif ($part !== $requestParts[$key]) {
                return false;
            }
        }
        
        return true;
    }
    
    private function callHandler($handler, $params) {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
        } elseif (is_string($handler)) {
            list($controller, $method) = explode('@', $handler);
            
            if (!class_exists($controller)) {
                $controllerPath = str_replace('App\\', '', $controller);
                $controllerPath = APP_PATH . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $controllerPath) . '.php';
                if (file_exists($controllerPath)) {
                    require_once $controllerPath;
                } else {
                    throw new Exception("Controller class not found: " . $controller . " at " . $controllerPath);
                }
            }
            
            $className = basename(str_replace('\\', '/', $controller));
            $controllerInstance = new $className();
            if (method_exists($controllerInstance, $method)) {
                call_user_func_array([$controllerInstance, $method], $params);
            } else {
                throw new Exception("Method " . $method . " not found in controller " . $controller);
            }
        } else {
            throw new Exception("Invalid route handler: " . print_r($handler, true));
        }
    }
    
    private function render404($uri, $method) {
        $viewPath = APP_PATH . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'View.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
            View::render('errors/404', [
                'uri' => $uri,
                'method' => $method
            ]);
        } else {
            http_response_code(404);
            echo "<!DOCTYPE html><html><head><title>404 - Page Not Found</title></head><body>";
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>Sorry, the page you are looking for could not be found. It's just an accident that was not intentional.</p>";
            echo "</body></html>";
        }
    }
}

