<?php
/**
 * Environment Configuration Validator
 * Checks for required and recommended configuration at application startup
 * 
 * Run this script during deployment to verify configuration:
 * php config/validate-env.php
 */

require_once __DIR__ . '/env.php';

class EnvironmentValidator {
    private $errors = [];
    private $warnings = [];
    private $infos = [];
    
    /**
     * Required configuration variables
     */
    private $required = [
        'DB_HOST' => 'Database hostname/IP address',
        'DB_NAME' => 'Database name',
        'DB_USER' => 'Database username',
        'APP_ENV' => 'Application environment (development/production/staging)',
    ];
    
    /**
     * Optional configuration variables (recommended but not required)
     */
    private $optional = [
        'MAIL_HOST' => 'SMTP server hostname (required for email features)',
        'MAIL_PORT' => 'SMTP server port',
        'MAIL_USERNAME' => 'SMTP authentication username',
        'MAIL_PASSWORD' => 'SMTP authentication password',
        'MAIL_FROM_ADDRESS' => 'Sender email address',
        'MAIL_FROM_NAME' => 'Sender display name',
        'JWT_SECRET' => 'JWT signing secret (if using JWT authentication)',
        'ENCRYPTION_KEY' => 'Data encryption key (if using encryption)',
        'RATE_LIMIT_REQUESTS' => 'API rate limiting requests count',
        'RATE_LIMIT_WINDOW' => 'API rate limiting time window (seconds)',
    ];
    
    /**
     * Validate all configuration
     */
    public function validate() {
        $this->validateRequired();
        $this->validateOptional();
        $this->validateValues();
        $this->validateSecurity();
        
        $this->displayResults();
        
        return empty($this->errors);
    }
    
    /**
     * Check required configuration variables
     */
    private function validateRequired() {
        foreach ($this->required as $var => $description) {
            $value = getenv($var);
            if ($value === false || $value === '') {
                $this->addError("Missing required variable: $var ($description)");
            } else {
                $this->addInfo("✓ $var is configured");
            }
        }
    }
    
    /**
     * Check optional configuration variables
     */
    private function validateOptional() {
        foreach ($this->optional as $var => $description) {
            $value = getenv($var);
            if ($value === false || $value === '') {
                $this->addWarning("Missing optional variable: $var ($description)");
            } else {
                $this->addInfo("✓ $var is configured");
            }
        }
    }
    
    /**
     * Validate specific configuration values
     */
    private function validateValues() {
        // Validate APP_ENV
        $app_env = getenv('APP_ENV');
        if ($app_env) {
            $valid_envs = ['development', 'production', 'staging', 'testing'];
            if (!in_array($app_env, $valid_envs)) {
                $this->addError("Invalid APP_ENV value: '$app_env' (must be one of: " . implode(', ', $valid_envs) . ")");
            }
        }
        
        // Validate MAIL_PORT if set
        $mail_port = getenv('MAIL_PORT');
        if ($mail_port && !is_numeric($mail_port)) {
            $this->addError("MAIL_PORT must be numeric (received: $mail_port)");
        }
        
        // Validate RATE_LIMIT_REQUESTS if set
        $rate_limit = getenv('RATE_LIMIT_REQUESTS');
        if ($rate_limit && !is_numeric($rate_limit)) {
            $this->addError("RATE_LIMIT_REQUESTS must be numeric (received: $rate_limit)");
        }
        
        // Validate RATE_LIMIT_WINDOW if set
        $rate_window = getenv('RATE_LIMIT_WINDOW');
        if ($rate_window && !is_numeric($rate_window)) {
            $this->addError("RATE_LIMIT_WINDOW must be numeric (received: $rate_window)");
        }
    }
    
    /**
     * Security checks
     */
    private function validateSecurity() {
        // Check database password is set in production
        if (getenv('APP_ENV') === 'production') {
            $db_pass = getenv('DB_PASS');
            if (!$db_pass || $db_pass === '') {
                $this->addError("Database password is empty in PRODUCTION environment (security risk)");
            }
            
            $db_user = getenv('DB_USER');
            if ($db_user === 'root') {
                $this->addWarning("Using 'root' database user in production (use dedicated application user)");
            }
            
            $app_debug = getenv('APP_DEBUG');
            if ($app_debug === 'true' || $app_debug === '1') {
                $this->addError("APP_DEBUG is enabled in PRODUCTION environment (security risk)");
            }
        }
        
        // Check JWT_SECRET strength if set
        $jwt_secret = getenv('JWT_SECRET');
        if ($jwt_secret && strlen($jwt_secret) < 32) {
            $this->addWarning("JWT_SECRET is too short (less than 32 characters). Minimum 32 characters recommended.");
        }
        
        // Check ENCRYPTION_KEY length if set
        $encryption_key = getenv('ENCRYPTION_KEY');
        if ($encryption_key && strlen($encryption_key) !== 32) {
            $this->addError("ENCRYPTION_KEY must be exactly 32 characters (received: " . strlen($encryption_key) . ")");
        }
    }
    
    /**
     * Add error message
     */
    private function addError($message) {
        $this->errors[] = $message;
    }
    
    /**
     * Add warning message
     */
    private function addWarning($message) {
        $this->warnings[] = $message;
    }
    
    /**
     * Add info message
     */
    private function addInfo($message) {
        $this->infos[] = $message;
    }
    
    /**
     * Display validation results
     */
    private function displayResults() {
        echo "\n=== ENVIRONMENT CONFIGURATION VALIDATION ===\n\n";
        
        // Display infos
        if (!empty($this->infos)) {
            echo "CONFIGURATION STATUS:\n";
            foreach ($this->infos as $info) {
                echo "  $info\n";
            }
            echo "\n";
        }
        
        // Display warnings
        if (!empty($this->warnings)) {
            echo "⚠ WARNINGS:\n";
            foreach ($this->warnings as $warning) {
                echo "  ⚠ $warning\n";
            }
            echo "\n";
        }
        
        // Display errors
        if (!empty($this->errors)) {
            echo "✗ ERRORS:\n";
            foreach ($this->errors as $error) {
                echo "  ✗ $error\n";
            }
            echo "\n";
            echo "Configuration validation FAILED\n";
            echo "Please fix the errors above and try again.\n\n";
            return;
        }
        
        echo "✓ Configuration validation SUCCESSFUL\n";
        echo "All required variables are properly configured.\n\n";
    }
    
    /**
     * Get validation status
     */
    public function isValid() {
        return empty($this->errors);
    }
    
    /**
     * Get error count
     */
    public function getErrorCount() {
        return count($this->errors);
    }
    
    /**
     * Get warning count
     */
    public function getWarningCount() {
        return count($this->warnings);
    }
}

// Run validation if executed directly
if (php_sapi_name() === 'cli') {
    $validator = new EnvironmentValidator();
    $valid = $validator->validate();
    
    // Exit with status code
    exit($valid ? 0 : 1);
}
?>
