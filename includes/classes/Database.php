<?php
/**
 * KHODERS WORLD Database Class
 * 
 * Handles database connections and queries with PDO
 */

class Database {
    private static $instance = null;
    private $pdo;
    private $isConnected = false;
    private $error = null;

    /**
     * Private constructor to prevent direct instantiation
     * 
     * @param array $config Database configuration
     */
    private function __construct($config) {
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);
            $this->isConnected = true;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    /**
     * Get database instance (singleton)
     * 
     * @param array $config Database configuration
     * @return Database|null Database instance or null on error
     */
    public static function getInstance($config = null) {
        if (self::$instance === null) {
            // If no config provided, try to load from config file
            if ($config === null) {
                if (file_exists(__DIR__ . '/../../config/database.php')) {
                    $config = include __DIR__ . '/../../config/database.php';
                } else {
                    throw new Exception('Database configuration not found');
                }
            }
            
            self::$instance = new self($config);
        }
        
        return self::$instance;
    }

    /**
     * Check if connected to database
     * 
     * @return bool True if connected, false otherwise
     */
    public function isConnected() {
        return $this->isConnected;
    }

    /**
     * Get the last error
     * 
     * @return string|null Error message or null if no error
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Get PDO instance
     * 
     * @return PDO|null PDO instance or null if not connected
     */
    public function getPdo() {
        return $this->pdo;
    }

    /**
     * Execute a query
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for the query
     * @return PDOStatement|false PDOStatement or false on error
     */
    public function query($sql, $params = []) {
        if (!$this->isConnected) {
            return false;
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * Fetch a single row
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for the query
     * @return array|false Row data or false on error
     */
    public function fetchRow($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        
        if ($stmt) {
            return $stmt->fetch();
        }
        
        return false;
    }

    /**
     * Fetch all rows
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for the query
     * @return array|false Rows data or false on error
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        
        if ($stmt) {
            return $stmt->fetchAll();
        }
        
        return false;
    }

    /**
     * Insert data into a table
     * 
     * @param string $table Table name
     * @param array $data Associative array of data to insert
     * @return int|false Last insert ID or false on error
     */
    public function insert($table, $data) {
        if (!$this->isConnected || empty($data)) {
            return false;
        }
        
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->query($sql, array_values($data));
        
        if ($stmt) {
            return $this->pdo->lastInsertId();
        }
        
        return false;
    }

    /**
     * Update data in a table
     * 
     * @param string $table Table name
     * @param array $data Associative array of data to update
     * @param string $where Where clause
     * @param array $whereParams Parameters for the where clause
     * @return int|false Number of affected rows or false on error
     */
    public function update($table, $data, $where, $whereParams = []) {
        if (!$this->isConnected || empty($data)) {
            return false;
        }
        
        $set = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $set[] = "{$column} = ?";
            $params[] = $value;
        }
        
        $sql = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE {$where}";
        
        $stmt = $this->query($sql, array_merge($params, $whereParams));
        
        if ($stmt) {
            return $stmt->rowCount();
        }
        
        return false;
    }

    /**
     * Delete data from a table
     * 
     * @param string $table Table name
     * @param string $where Where clause
     * @param array $params Parameters for the where clause
     * @return int|false Number of affected rows or false on error
     */
    public function delete($table, $where, $params = []) {
        if (!$this->isConnected) {
            return false;
        }
        
        $sql = "DELETE FROM {$table} WHERE {$where}";
        
        $stmt = $this->query($sql, $params);
        
        if ($stmt) {
            return $stmt->rowCount();
        }
        
        return false;
    }

    /**
     * Begin a transaction
     * 
     * @return bool True on success, false on error
     */
    public function beginTransaction() {
        if (!$this->isConnected) {
            return false;
        }
        
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction
     * 
     * @return bool True on success, false on error
     */
    public function commit() {
        if (!$this->isConnected) {
            return false;
        }
        
        return $this->pdo->commit();
    }

    /**
     * Rollback a transaction
     * 
     * @return bool True on success, false on error
     */
    public function rollback() {
        if (!$this->isConnected) {
            return false;
        }
        
        return $this->pdo->rollBack();
    }
}
