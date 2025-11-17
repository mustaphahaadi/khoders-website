<?php
/**
 * API Request Wrapper and Initialization
 * 
 * Include this at the top of any API file to:
 * - Initialize error handling with environment awareness
 * - Set up CORS headers
 * - Configure JSON response headers
 * 
 * Usage:
 *   require_once __DIR__ . '/../config/api-wrapper.php';
 *   // Then use ErrorHandler methods
 *   ErrorHandler::apiSuccess($data);
 *   ErrorHandler::apiError($message, $code);
 */

// Load error handler first
require_once __DIR__ . '/error-handler.php';

// Initialize error handler based on environment
$appEnv = getenv('APP_ENV') ?: 'development';
$logPath = __DIR__ . '/../logs';

ErrorHandler::configure($appEnv, $logPath);

// Set up JSON response headers
header('Content-Type: application/json; charset=utf-8');

// CORS Configuration
$allowedOrigins = [
    'http://localhost',
    'http://127.0.0.1',
    'http://localhost:8000',
    'http://localhost:3000'
];

// Add production domain if configured
if ($appEnv === 'production') {
    $productionDomain = getenv('APP_URL') ?: 'https://khodersclub.com';
    $allowedOrigins = [$productionDomain];
}

// Handle CORS preflight
$requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($requestOrigin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $requestOrigin);
} else {
    http_response_code(403);
    exit('CORS policy violation');
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Set up error logging context
if (!function_exists('log_api_request')) {
    function log_api_request($endpoint, $method, $statusCode) {
        ErrorHandler::log([
            'endpoint' => $endpoint,
            'method' => $method,
            'status' => $statusCode,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ], 'api_request');
    }
}
?>
