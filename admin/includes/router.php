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
        $path = trim($path, '/');
        
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
                
                if (!empty($params)) {
                    $url .= '?' . http_build_query($params);
                }
                
                return $url;
            }
        }
        
        return self::$baseUrl;
    }

    /**
     * Dispatch the router
     */
    public static function dispatch() {
        if (isset($_GET['route'])) {
            $path = trim($_GET['route']);
            if (empty($path)) {
                $path = 'index';
            }
            
            self::$currentRoute = $path;
            
            if (isset(self::$routes[$path])) {
                $route = self::$routes[$path];
                
                if (!empty($route['requiredRole'])) {
                    require_once __DIR__ . '/../../config/auth.php';
                    if (!Auth::hasRole($route['requiredRole'])) {
                        header('Location: login.php?error=insufficient_permissions');
                        exit;
                    }
                }
                
                foreach ($route['middleware'] as $middleware) {
                    if (is_callable($middleware)) {
                        call_user_func($middleware);
                    }
                }
                
                if (!empty($route['title']) && !defined('PAGE_TITLE')) {
                    define('PAGE_TITLE', $route['title']);
                }
                
                if (is_callable($route['callback'])) {
                    call_user_func($route['callback']);
                } else {
                    $filePath = __DIR__ . '/../' . $route['callback'];
                    $realPath = realpath($filePath);
                    $baseDir = realpath(__DIR__ . '/../');
                    
                    if ($realPath && $baseDir && strpos($realPath, $baseDir) === 0 && file_exists($filePath)) {
                        include $filePath;
                    } else {
                        echo "<div class='alert alert-danger'>Error: File not found</div>";
                    }
                }
                
                return;
            }
            
            if (!headers_sent()) {
                header('HTTP/1.0 404 Not Found');
            }
            self::execute404();
            return;
        }
        
        if (basename($_SERVER['SCRIPT_NAME']) === 'index.php') {
            $path = 'index';
            self::$currentRoute = $path;
            
            if (isset(self::$routes[$path])) {
                $route = self::$routes[$path];
                
                if (!empty($route['title']) && !defined('PAGE_TITLE')) {
                    define('PAGE_TITLE', $route['title']);
                }
                
                if (is_callable($route['callback'])) {
                    call_user_func($route['callback']);
                } else {
                    $filePath = __DIR__ . '/../' . $route['callback'];
                    $realPath = realpath($filePath);
                    $baseDir = realpath(__DIR__ . '/../');
                    
                    if ($realPath && $baseDir && strpos($realPath, $baseDir) === 0 && file_exists($filePath)) {
                        include $filePath;
                    } else {
                        echo "<div class='alert alert-danger'>Error: File not found</div>";
                    }
                }
                
                return;
            }
        }
        
        $requestUri = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = dirname($scriptName);
        
        if (strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        
        $path = parse_url($requestUri, PHP_URL_PATH);
        $path = trim($path, '/');
        
        if (empty($path)) {
            $path = 'index';
        }
        
        self::$currentRoute = $path;
        
        if (isset(self::$routes[$path])) {
            $route = self::$routes[$path];
            
            if (!empty($route['requiredRole'])) {
                require_once __DIR__ . '/../../config/auth.php';
                if (!Auth::hasRole($route['requiredRole'])) {
                    header('Location: login.php?error=insufficient_permissions');
                    exit;
                }
            }
            
            foreach ($route['middleware'] as $middleware) {
                if (is_callable($middleware)) {
                    call_user_func($middleware);
                }
            }
            
            if (!empty($route['title']) && !defined('PAGE_TITLE')) {
                define('PAGE_TITLE', $route['title']);
            }
            
            if (is_callable($route['callback'])) {
                call_user_func($route['callback']);
            } else {
                $filePath = __DIR__ . '/../' . $route['callback'];
                $realPath = realpath($filePath);
                $baseDir = realpath(__DIR__ . '/../');
                
                if ($realPath && $baseDir && strpos($realPath, $baseDir) === 0 && file_exists($filePath)) {
                    include $filePath;
                } else {
                    echo "<div class='alert alert-danger'>Error: File not found</div>";
                }
            }
            
            return;
        }
        
        if (!headers_sent()) {
            header('HTTP/1.0 404 Not Found');
        }
        
        self::execute404();
    }
}
