<?php
/**
 * CSRF Token Manager
 * Handles generation, validation, and storage of CSRF tokens
 * Supports both form submissions and JSON API requests
 */

class CSRFToken {
    private static $tokenName = 'csrf_token';
    private static $sessionKey = '_csrf_token';
    private static $headerName = 'X-CSRF-Token';
    
    /**
     * Initialize session if not already started
     */
    private static function ensureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Generate a new CSRF token
     * @return string The generated token
     */
    public static function generate() {
        self::ensureSession();
        
        // Generate a new token if one doesn't exist or is expired
        if (!isset($_SESSION[self::$sessionKey])) {
            $_SESSION[self::$sessionKey] = [
                'token' => bin2hex(random_bytes(32)),
                'time' => time()
            ];
        }
        
        return $_SESSION[self::$sessionKey]['token'];
    }
    
    /**
     * Get the current token value
     * @return string|null The token or null if not set
     */
    public static function getToken() {
        self::ensureSession();
        return isset($_SESSION[self::$sessionKey]) ? $_SESSION[self::$sessionKey]['token'] : null;
    }
    
    /**
     * Get the token name/key used in forms
     * @return string The token name
     */
    public static function getTokenName() {
        return self::$tokenName;
    }
    
    /**
     * Get the header name for JSON API requests
     * @return string The header name
     */
    public static function getHeaderName() {
        return self::$headerName;
    }
    
    /**
     * Validate a CSRF token from POST/REQUEST/Header
     * @param string $token The token to validate (optional - auto-detects source)
     * @param int $maxAge Maximum age of token in seconds (default: 3600 = 1 hour)
     * @return bool True if token is valid, false otherwise
     */
    public static function validate($token = null, $maxAge = 3600) {
        self::ensureSession();
        
        // Get token from parameter or auto-detect
        if ($token === null) {
            // Try POST/REQUEST data first (for form submissions)
            $token = $_POST[self::$tokenName] ?? $_REQUEST[self::$tokenName] ?? null;
            
            // If not found, try header (for JSON API requests)
            if ($token === null && function_exists('getallheaders')) {
                $headers = getallheaders();
                foreach ($headers as $key => $value) {
                    if (strtolower($key) === strtolower(self::$headerName)) {
                        $token = $value;
                        break;
                    }
                }
            }
            
            // Fallback for servers without getallheaders
            if ($token === null) {
                $headerKey = 'HTTP_' . str_replace('-', '_', strtoupper(self::$headerName));
                $token = $_SERVER[$headerKey] ?? null;
            }
        }
        
        // Check if token exists in session
        if (!isset($_SESSION[self::$sessionKey])) {
            return false;
        }
        
        $sessionData = $_SESSION[self::$sessionKey];
        
        // Verify token matches (timing-safe comparison)
        if (!hash_equals($sessionData['token'], $token ?? '')) {
            return false;
        }
        
        // Check token age
        if (time() - $sessionData['time'] > $maxAge) {
            unset($_SESSION[self::$sessionKey]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Regenerate token after validation
     * Useful for preventing token replay attacks
     */
    public static function regenerate() {
        self::ensureSession();
        unset($_SESSION[self::$sessionKey]);
        return self::generate();
    }
    
    /**
     * Get HTML input field for forms
     * @return string HTML hidden input field
     */
    public static function getFieldHTML() {
        $tokenName = self::getTokenName();
        $tokenValue = self::generate();
        return sprintf(
            '<input type="hidden" name="%s" value="%s" />',
            htmlspecialchars($tokenName, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($tokenValue, ENT_QUOTES, 'UTF-8')
        );
    }
    
    /**
     * Get JavaScript object with token for AJAX requests
     * Usage: var csrf = CSRFToken::getJSObject(); then use csrf.headerName and csrf.token
     * @return string JSON string with token info
     */
    public static function getJSObject() {
        return json_encode([
            'headerName' => self::$headerName,
            'token' => self::generate(),
            'tokenName' => self::$tokenName
        ]);
    }
}
?>

