<?php
require_once __DIR__ . '/env.php';

/**
 * Database Connection Manager
 * Enhanced version with better security and error handling
 * 
 * Configuration Priority:
 * 1. Environment variables from .env or system
 * 2. Default values specified below
 * 3. Constructor parameters
 */
class Database {
    // Default database settings (matched to .env.example)
    private $host = 'localhost';
    private $db_name = 'khoders_db';
    private $username = 'root';              // For development only; use dedicated user in production
    private $password = '';                   // Empty for development; use strong password in production
    public $conn;
    private static $instance = null;
    private $error = '';

    public function __construct() {
        // Load credentials from environment variables if available
        // Environment variables take precedence over defaults
        $this->host = getenv('DB_HOST') ?: $this->host;
        $this->db_name = getenv('DB_NAME') ?: $this->db_name;
        $this->username = getenv('DB_USER') ?: $this->username;
        $this->password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : $this->password;
    }

    /**
     * Singleton pattern for database connection
     * Ensures only one database connection is created per request
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the error message if connection failed
     */
    public function getError() {
        return $this->error;
    }
    
    /**
     * Get database connection with improved error handling
     * 
     * Features:
     * - Automatic database creation if missing
     * - Proper error handling and logging
     * - Security-focused PDO options
     * - UTF-8 support
     */
    public function getConnection() {
        // Return existing connection if available
        if ($this->conn !== null) {
            return $this->conn;
        }

        $dsnWithDb = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
        $dsnNoDb   = "mysql:host={$this->host};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false, // Use real prepared statements for better security
            PDO::ATTR_PERSISTENT => false, // Disable persistent connections for better security
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];

        try {
            // Try connecting directly to the target database
            $this->conn = new PDO($dsnWithDb, $this->username, $this->password, $options);
            $this->logInfo('Database connection established successfully');
        } catch (PDOException $e) {
            // Create database if it doesn't exist, then reconnect
            if (stripos($e->getMessage(), 'Unknown database') !== false) {
                try {
                    $tmpPdo = new PDO($dsnNoDb, $this->username, $this->password, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]);
                    $dbNameEsc = str_replace('`', '``', $this->db_name);
                    $tmpPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbNameEsc}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $this->logInfo("Database '{$this->db_name}' created successfully");
                    $tmpPdo = null;

                    $this->conn = new PDO($dsnWithDb, $this->username, $this->password, $options);
                } catch (PDOException $inner) {
                    $this->logError('Database creation/connection failed', $inner);
                    return null;
                }
            } else {
                $this->logError('Database connection error', $e);
                return null;
            }
        }

        return $this->conn;
    }

    /**
     * Create required tables using the schema.sql file
     */
    public function createTables() {
        $db = $this->getConnection();
        if (!$db) { 
            $this->logError('Cannot create tables: No database connection');
            return false; 
        }

        $schemaPath = __DIR__ . '/../database/schema.sql';
        if (!file_exists($schemaPath)) { 
            $this->logError('Schema file not found: ' . $schemaPath);
            return false; 
        }

        $sql = file_get_contents($schemaPath);
        if ($sql === false) { 
            $this->logError('Failed to read schema file');
            return false; 
        }

        // Remove statements that are not needed when already connected to the DB
        $sql = preg_replace('/^\s*CREATE\s+DATABASE.*?;\s*$/mi', '', $sql);
        $sql = preg_replace('/^\s*USE\s+.+?;\s*$/mi', '', $sql);

        // Split into individual statements on semicolons at line ends
        $statements = preg_split('/;\s*\n/', $sql);

        try {
            $db->beginTransaction();
            foreach ($statements as $stmt) {
                $trimmed = trim($stmt);
                if ($trimmed === '') { continue; }
                $db->exec($trimmed);
            }
            $db->commit();
            $this->logInfo('Database tables created successfully');
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            $this->logError('Schema creation failed', $e);
            return false;
        }
    }

    /**
     * Log informational messages (suppressed in production)
     */
    private function logInfo($message) {
        $app_env = getenv('APP_ENV');
        if ($app_env !== 'production') {
            error_log('[INFO] ' . $message);
        }
    }

    /**
     * Log errors with exception details
     */
    private function logError($message, $exception = null) {
        $logMessage = '[ERROR] ' . $message;
        if ($exception instanceof Exception) {
            $logMessage .= ': ' . $exception->getMessage();
            $logMessage .= ' in ' . $exception->getFile() . ':' . $exception->getLine();
        }
        error_log($logMessage);
    }

    /**
     * Test database connection
     * Useful for diagnostic and monitoring purposes
     */
    public function testConnection() {
        try {
            $db = $this->getConnection();
            if ($db === null) {
                return ['success' => false, 'message' => 'Failed to establish connection'];
            }
            $stmt = $db->query('SELECT 1');
            return ['success' => true, 'message' => 'Database connection successful'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>
