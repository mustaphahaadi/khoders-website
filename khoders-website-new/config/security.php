<?php
/**
 * Security Helper Class
 * Provides security utilities for the application
 */

class Security {
    private static $rateLimitStore = [];
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Sanitize input string
     */
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        return $input;
    }
    
    /**
     * Validate email format
     */
    public static function validateEmail($email) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Rate limiting check
     */
    public static function checkRateLimit($identifier, $maxRequests = 10, $timeWindow = 60) {
        $currentTime = time();
        
        // Clean up old entries
        self::$rateLimitStore = array_filter(
            self::$rateLimitStore,
            function($data) use ($currentTime, $timeWindow) {
                return ($currentTime - $data['time']) < $timeWindow;
            }
        );
        
        // Count requests from this identifier
        $requests = array_filter(
            self::$rateLimitStore,
            function($data) use ($identifier) {
                return $data['identifier'] === $identifier;
            }
        );
        
        if (count($requests) >= $maxRequests) {
            return false;
        }
        
        // Add new request
        self::$rateLimitStore[] = [
            'identifier' => $identifier,
            'time' => $currentTime
        ];
        
        return true;
    }
    
    /**
     * Get client IP address
     */
    public static function getClientIP() {
        $ipKeys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER)) {
                $ip = $_SERVER[$key];
                // Handle multiple IPs (take the first one)
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }
    
    /**
     * Validate string length
     */
    public static function validateLength($string, $min = 1, $max = 255) {
        $length = mb_strlen($string, 'UTF-8');
        return $length >= $min && $length <= $max;
    }
    
    /**
     * Check if request is AJAX
     */
    public static function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Log security event
     */
    public static function logSecurityEvent($event, $details = []) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => self::getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'details' => $details
        ];
        
        error_log('[SECURITY] ' . json_encode($logEntry));
    }
    
    /**
     * Validate JSON input
     */
    public static function validateJSON($json) {
        if (empty($json)) {
            return false;
        }
        
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Generate secure random string
     */
    public static function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Hash password securely
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    
    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Prevent SQL injection in LIKE queries
     */
    public static function escapeLikeString($string) {
        return str_replace(['%', '_'], ['\\%', '\\_'], $string);
    }
}
?>

