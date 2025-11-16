<?php
/**
 * KHODERS WORLD Error Handler
 * 
 * Environment-aware error handling:
 * - Production: Generic error messages, detailed logging server-side
 * - Development: Detailed error information for debugging
 * - Staging: Balanced error information
 * 
 * Usage:
 * - Set up in config/init.php: ErrorHandler::configure()
 * - Use in API endpoints: ErrorHandler::apiError($message)
 * - Use in try-catch: ErrorHandler::handleException($e)
 * - Log server-side: ErrorHandler::log($message)
 */

class ErrorHandler {
    // Configuration
    private static $configured = false;
    private static $environment = 'development';
    private static $logPath = '';
    private static $displayErrors = true;
    private static $logErrors = true;
    
    // Error codes
    const VALIDATION_ERROR = 400;
    const AUTHENTICATION_ERROR = 401;
    const AUTHORIZATION_ERROR = 403;
    const NOT_FOUND_ERROR = 404;
    const RATE_LIMIT_ERROR = 429;
    const SERVER_ERROR = 500;
    const SERVICE_UNAVAILABLE = 503;
    
    /**
     * Configure error handler
     * 
     * @param string $environment - 'development', 'staging', or 'production'
     * @param string $logPath - Path to log directory
     */
    public static function configure($environment = 'development', $logPath = '') {
        self::$environment = $environment;
        self::$logPath = $logPath ?: __DIR__ . '/../logs';
        
        // Environment-specific configuration
        switch ($environment) {
            case 'production':
                self::$displayErrors = false;
                self::$logErrors = true;
                error_reporting(E_ALL);
                ini_set('display_errors', '0');
                break;
                
            case 'staging':
                self::$displayErrors = true;
                self::$logErrors = true;
                error_reporting(E_ALL);
                ini_set('display_errors', '1');
                break;
                
            case 'development':
            default:
                self::$displayErrors = true;
                self::$logErrors = true;
                error_reporting(E_ALL);
                ini_set('display_errors', '1');
        }
        
        // Create logs directory if it doesn't exist
        if (!is_dir(self::$logPath)) {
            @mkdir(self::$logPath, 0755, true);
        }
        
        // Set up error handlers
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
        
        self::$configured = true;
    }
    
    /**
     * Handle PHP errors
     */
    public static function handleError($errno, $errstr, $errfile, $errline) {
        $error = [
            'type' => self::getErrorType($errno),
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Log the error
        self::log($error, 'error');
        
        // Display based on environment
        if (self::$displayErrors && !headers_sent()) {
            echo self::formatErrorDisplay($error);
        }
        
        return true; // Prevent default PHP error handler
    }
    
    /**
     * Handle exceptions
     */
    public static function handleException($exception) {
        $error = [
            'type' => 'Exception',
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Log the exception
        self::log($error, 'exception');
        
        // Return generic error to client
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(self::getClientErrorResponse('An unexpected error occurred'));
        }
    }
    
    /**
     * Handle fatal errors on shutdown
     */
    public static function handleShutdown() {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }
    
    /**
     * Get client-safe error response for API
     * Hides implementation details in production
     */
    public static function getClientErrorResponse($message = null, $code = 500, $errors = []) {
        $isProduction = self::$environment === 'production';
        
        $response = [
            'success' => false,
            'code' => $code,
            'message' => $isProduction ? 'An error occurred. Please try again later.' : ($message ?? 'Error'),
        ];
        
        // Only include validation errors in all environments
        if (!empty($errors) && $code === 400) {
            $response['errors'] = $errors;
        }
        
        // In development/staging, include more details
        if (!$isProduction && $message) {
            $response['details'] = $message;
        }
        
        return $response;
    }
    
    /**
     * API error response
     * 
     * @param string $message - Error message (user-facing in production)
     * @param int $code - HTTP status code
     * @param array $errors - Validation errors (optional)
     * @param array $details - Additional details (only shown in development)
     */
    public static function apiError($message, $code = 500, $errors = [], $details = []) {
        http_response_code($code);
        header('Content-Type: application/json');
        
        $response = self::getClientErrorResponse($message, $code, $errors);
        
        // Add details only in development/staging
        if (!self::$displayErrors === false && !empty($details)) {
            $response['debugDetails'] = $details;
        }
        
        echo json_encode($response);
        exit;
    }
    
    /**
     * API success response
     */
    public static function apiSuccess($data, $message = 'Success', $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        
        echo json_encode([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
    
    /**
     * Log message or error
     */
    public static function log($message, $type = 'info', $context = []) {
        if (!self::$logErrors) {
            return;
        }
        
        // Ensure logs directory exists
        if (!is_dir(self::$logPath)) {
            @mkdir(self::$logPath, 0755, true);
        }
        
        $logFile = self::$logPath . '/' . $type . '.log';
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s.u'),
            'type' => $type,
            'message' => is_array($message) ? json_encode($message) : $message,
            'context' => $context,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'CLI'
        ];
        
        // Write to log file
        $logLine = json_encode($logEntry) . "\n";
        @file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log database error with query details
     */
    public static function logDatabaseError($message, $query = '', $bindings = []) {
        $context = [
            'query' => $query,
            'bindings' => $bindings
        ];
        
        self::log('Database Error: ' . $message, 'database', $context);
    }
    
    /**
     * Log API error with request details
     */
    public static function logAPIError($endpoint, $method, $message, $details = []) {
        $context = [
            'endpoint' => $endpoint,
            'method' => $method,
            'details' => $details
        ];
        
        self::log($message, 'api', $context);
    }
    
    /**
     * Log security event
     */
    public static function logSecurityEvent($event, $details = []) {
        $context = [
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI',
            'details' => $details
        ];
        
        self::log($event, 'security', $context);
    }
    
    /**
     * Format error for display (HTML)
     */
    private static function formatErrorDisplay($error) {
        if (self::$environment === 'production') {
            return '';
        }
        
        $html = '<div style="background: #fee; border: 1px solid #f99; padding: 10px; margin: 10px; font-family: monospace; font-size: 12px;">';
        $html .= '<strong>' . htmlspecialchars($error['type']) . ':</strong> ';
        $html .= htmlspecialchars($error['message']) . '<br>';
        $html .= '<small>File: ' . htmlspecialchars($error['file']) . ':' . $error['line'] . '</small>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Get error type name
     */
    private static function getErrorType($errno) {
        $errorTypes = [
            E_ERROR => 'Fatal Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated',
        ];
        
        return $errorTypes[$errno] ?? 'Unknown Error';
    }
    
    /**
     * Get current environment
     */
    public static function getEnvironment() {
        return self::$environment;
    }
    
    /**
     * Check if production
     */
    public static function isProduction() {
        return self::$environment === 'production';
    }
    
    /**
     * Check if development
     */
    public static function isDevelopment() {
        return self::$environment === 'development';
    }
    
    /**
     * Get last log file (for debugging)
     */
    public static function getLastErrorLog() {
        if (!is_dir(self::$logPath)) {
            return '';
        }
        
        $errorLog = self::$logPath . '/error.log';
        if (file_exists($errorLog)) {
            $lines = file($errorLog);
            return end($lines); // Return last line
        }
        
        return '';
    }
    
    /**
     * Clear error logs
     */
    public static function clearLogs($type = 'all') {
        if (!is_dir(self::$logPath)) {
            return false;
        }
        
        if ($type === 'all') {
            $files = glob(self::$logPath . '/*.log');
        } else {
            $files = [self::$logPath . '/' . $type . '.log'];
        }
        
        foreach ($files as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
        
        return true;
    }
}
?>
