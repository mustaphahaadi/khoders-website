<?php
/**
 * Standardized API Response Helper
 */
class ApiResponse {
    public static function success($data = null, $message = '', $meta = []) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta
        ], JSON_PRETTY_PRINT);
        exit;
    }
    
    public static function error($message, $code = 400, $errors = []) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], JSON_PRETTY_PRINT);
        exit;
    }
    
    public static function notFound($message = 'Resource not found') {
        self::error($message, 404);
    }
    
    public static function unauthorized($message = 'Unauthorized') {
        self::error($message, 401);
    }
    
    public static function serverError($message = 'Internal server error') {
        self::error($message, 500);
    }
}
