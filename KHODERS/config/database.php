<?php
require_once __DIR__ . '/env.php';
class Database {
    private $host = 'localhost';
    private $db_name = 'khoders_db';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function __construct() {
        // Load credentials from environment variables if available
        $this->host = getenv('DB_HOST') ?: $this->host;
        $this->db_name = getenv('DB_NAME') ?: $this->db_name;
        $this->username = getenv('DB_USER') ?: $this->username;
        $this->password = getenv('DB_PASS') ?: $this->password;
    }

    public function getConnection() {
        $this->conn = null;
        $dsnWithDb = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
        $dsnNoDb   = "mysql:host={$this->host};charset=utf8mb4";

        try {
            // Try connecting directly to the target database
            $this->conn = new PDO($dsnWithDb, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            // Create database if it doesn't exist, then reconnect
            if (stripos($e->getMessage(), 'Unknown database') !== false) {
                try {
                    $tmpPdo = new PDO($dsnNoDb, $this->username, $this->password, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]);
                    $dbNameEsc = str_replace('`', '``', $this->db_name);
                    $tmpPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbNameEsc}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $tmpPdo = null;

                    $this->conn = new PDO($dsnWithDb, $this->username, $this->password, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                } catch (PDOException $inner) {
                    error_log('Database creation/connection failed: ' . $inner->getMessage());
                    return null;
                }
            } else {
                error_log('Database connection error: ' . $e->getMessage());
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
        if (!$db) { return false; }

        $schemaPath = __DIR__ . '/../database/schema.sql';
        if (!file_exists($schemaPath)) { return false; }

        $sql = file_get_contents($schemaPath);
        if ($sql === false) { return false; }

        // Remove statements that are not needed when already connected to the DB
        $sql = preg_replace('/^\s*CREATE\s+DATABASE.*?;\s*$/mi', '', $sql);
        $sql = preg_replace('/^\s*USE\s+.+?;\s*$/mi', '', $sql);

        // Split into individual statements on semicolons at line ends
        $statements = preg_split('/;\s*\n/', $sql);

        try {
            foreach ($statements as $stmt) {
                $trimmed = trim($stmt);
                if ($trimmed === '') { continue; }
                $db->exec($trimmed);
            }
            return true;
        } catch (PDOException $e) {
            error_log('Schema creation failed: ' . $e->getMessage());
            return false;
        }
    }
}
?>