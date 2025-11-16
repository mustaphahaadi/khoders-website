<?php
/**
 * KHODERS WORLD Admin Router
 * Simple router for the admin panel to handle URL routing and page management
 */

class Router {
    private static $routes = [];
    private static $notFoundCallback;
    private static $baseUrl = '';
    private static $currentRoute = '';

    /**
     * Register a new route
     * 
     * @param string $path Route path
     * @param callable|string $callback Function or controller method to call
     * @param array $options Additional options (middleware, etc.)
     */
    public static function register($path, $callback, $options = []) {
        // Normalize path
        $path = trim($path, '/');
        
        // Add route to collection
        self::$routes[$path] = [
            'callback' => $callback,
            'middleware' => $options['middleware'] ?? [],
            'name' => $options['name'] ?? '',
            'title' => $options['title'] ?? 'KHODERS WORLD Admin',
            'requiredRole' => $options['requiredRole'] ?? ''
        ];
    }

    /**
     * Set the 404 not found handler
     * 
     * @param callable $callback Function to call when route is not found
     */
    public static function notFound($callback) {
        self::$notFoundCallback = $callback;
    }
    
    /**
     * Execute the 404 not found handler
     */
    public static function execute404() {
        if (is_callable(self::$notFoundCallback)) {
            call_user_func(self::$notFoundCallback);
        } else {
            echo '<h1>404 Not Found</h1>';
            echo '<p>The page you requested could not be found.</p>';
        }
    }

    /**
     * Set the base URL for the router
     */
    public static function setBaseUrl($url) {
        self::$baseUrl = rtrim($url, '/');
    }

    /**
     * Get the current route path
     */
    public static function getCurrentRoute() {
        return self::$currentRoute;
    }
    
    /**
     * Get all registered routes
     */
    public static function getRoutes() {
        return self::$routes;
    }

    /**
     * Check if the current route matches a specific name
     */
    public static function isRoute($name) {
        foreach (self::$routes as $route) {
            if ($route['name'] === $name && self::$currentRoute === $route['name']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate a URL for a named route
     */
    public static function url($name, $params = []) {
        foreach (self::$routes as $path => $route) {
            if ($route['name'] === $name) {
                $url = self::$baseUrl . '/' . $path;
                
                // Add query parameters if provided
                if (!empty($params)) {
                    $url .= '?' . http_build_query($params);
                }
                
                return $url;
            }
        }
        
        // Return base URL if route not found
        return self::$baseUrl;
    }

    /**
     * Dispatch the router
     */
    public static function dispatch() {
        // Check for route parameter first (for index.php?route=xxx style URLs)
        if (isset($_GET['route'])) {
            $path = trim($_GET['route']);
            if (empty($path)) {
                $path = 'index';
            }
            
            // Store current route for later use
            self::$currentRoute = $path;
            
            // Check if this route exists
            if (isset(self::$routes[$path])) {
                $route = self::$routes[$path];
                
                // Check role requirements
                if (!empty($route['requiredRole'])) {
                    require_once __DIR__ . '/../../config/auth.php';
                    if (!Auth::hasRole($route['requiredRole'])) {
                        header('Location: login.php?error=insufficient_permissions');
                        exit;
                    }
                }
                
                // Execute middleware
                foreach ($route['middleware'] as $middleware) {
                    if (is_callable($middleware)) {
                        call_user_func($middleware);
                    }
                }
                
                // Set page title if provided
                if (!empty($route['title']) && !defined('PAGE_TITLE')) {
                    define('PAGE_TITLE', $route['title']);
                }
                
                // Execute route callback
                if (is_callable($route['callback'])) {
                    call_user_func($route['callback']);
                } else {
                    // Assume string is a file path to include
                    // Use absolute path to ensure file is found
                    $filePath = __DIR__ . '/../' . $route['callback'];
                    if (file_exists($filePath)) {
                        include $filePath;
                    } else {
                        echo "<div class='alert alert-danger'>Error: File not found: {$route['callback']} (Looking for: {$filePath})</div>";
                    }
                }
                
                return;
            }
            
            // Route parameter provided but not found
            if (!headers_sent()) {
                header('HTTP/1.0 404 Not Found');
            }
            self::execute404();
            return;
        }
        
        // For direct file access (e.g., /admin/index.php), use 'index' as the route
        if (basename($_SERVER['SCRIPT_NAME']) === 'index.php') {
            $path = 'index';
            self::$currentRoute = $path;
            
            // Check if this route exists
            if (isset(self::$routes[$path])) {
                $route = self::$routes[$path];
                
                // Set page title if provided
                if (!empty($route['title']) && !defined('PAGE_TITLE')) {
                    define('PAGE_TITLE', $route['title']);
                }
                
                // Execute route callback
                if (is_callable($route['callback'])) {
                    call_user_func($route['callback']);
                } else {
                    // Assume string is a file path to include
                    // Use absolute path to ensure file is found
                    $filePath = __DIR__ . '/../' . $route['callback'];
                    if (file_exists($filePath)) {
                        include $filePath;
                    } else {
                        echo "<div class='alert alert-danger'>Error: File not found: {$route['callback']} (Looking for: {$filePath})</div>";
                    }
                }
                
                return;
            }
        }
        
        // For URL routing (not currently used but kept for future use)
        $requestUri = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = dirname($scriptName);
        
        // Remove base path from request URI
        if (strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        
        // Extract path without query string
        $path = parse_url($requestUri, PHP_URL_PATH);
        $path = trim($path, '/');
        
        // Default to index if path is empty
        if (empty($path)) {
            $path = 'index';
        }
        
        // Store current route for later use
        self::$currentRoute = $path;
        
        // Find matching route
        if (isset(self::$routes[$path])) {
            $route = self::$routes[$path];
            
            // Check role requirements
            if (!empty($route['requiredRole'])) {
                require_once __DIR__ . '/../../config/auth.php';
                if (!Auth::hasRole($route['requiredRole'])) {
                    header('Location: login.php?error=insufficient_permissions');
                    exit;
                }
            }
            
            // Execute middleware
            foreach ($route['middleware'] as $middleware) {
                if (is_callable($middleware)) {
                    call_user_func($middleware);
                }
            }
            
            // Set page title if provided
            if (!empty($route['title']) && !defined('PAGE_TITLE')) {
                define('PAGE_TITLE', $route['title']);
            }
            
            // Execute route callback
            if (is_callable($route['callback'])) {
                call_user_func($route['callback']);
            } else {
                // Assume string is a file path to include
                // Use absolute path to ensure file is found
                $filePath = __DIR__ . '/../' . $route['callback'];
                if (file_exists($filePath)) {
                    include $filePath;
                } else {
                    echo "<div class='alert alert-danger'>Error: File not found: {$route['callback']} (Looking for: {$filePath})</div>";
                }
            }
            
            return;
        }
        
        // No matching route found, use 404 handler
        // Set the HTTP status code first
        if (!headers_sent()) {
            header('HTTP/1.0 404 Not Found');
        }
        
        // Execute the 404 handler
        self::execute404();
    }
}
