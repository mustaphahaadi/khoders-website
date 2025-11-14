<?php
/**
 * KHODERS WORLD Admin Settings Helper
 * Provides functions for managing admin settings
 */

class Settings {
    private $db;
    private $settings = [];
    
    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        try {
            $database = new Database();
            $this->db = $database->getConnection();
            if ($this->db) {
                $this->loadSettings();
            }
        } catch (Exception $e) {
            // Handle database connection error
            $this->db = null;
        }
    }
    
    /**
     * Load all settings from database
     */
    private function loadSettings() {
        if (!$this->db) {
            return;
        }
        
        try {
            // Check if settings table exists
            $tableExists = $this->tableExists('settings');
            
            if (!$tableExists) {
                // Create settings table
                $this->createSettingsTable();
            }
            
            // Load settings
            $stmt = $this->db->query('SELECT * FROM settings');
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $row) {
                $this->settings[$row['setting_key']] = $this->parseValue($row['setting_value'], $row['setting_type']);
            }
        } catch (PDOException $e) {
            error_log('Failed to load settings: ' . $e->getMessage());
        }
    }
    
    /**
     * Create settings table if it doesn't exist
     */
    private function createSettingsTable() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(255) NOT NULL UNIQUE,
                setting_value TEXT,
                setting_type VARCHAR(50) DEFAULT 'string',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            
            if ($this->db) {
                $this->db->exec($sql);
            } else {
                return;
            }
            
            // Add default settings
            $defaults = [
                ['site_name', 'KHODERS WORLD', 'string'],
                ['site_description', 'Campus Coding Club Admin Panel', 'string'],
                ['contact_email', 'admin@khoders.com', 'string'],
                ['enable_registration', '1', 'boolean'],
                ['maintenance_mode', '0', 'boolean'],
                ['items_per_page', '20', 'integer'],
                ['theme_color', '#4B49AC', 'string'],
                ['logo_path', '../assets/img/khoders/logo.png', 'string']
            ];
            
            if (!$this->db) {
                return;
            }
            
            $stmt = $this->db->prepare('INSERT IGNORE INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, ?)');
            
            if ($stmt) {
                foreach ($defaults as $setting) {
                    $stmt->execute($setting);
                }
            }
        } catch (PDOException $e) {
            error_log('Failed to create settings table: ' . $e->getMessage());
        }
    }
    
    /**
     * Parse setting value based on type
     */
    private function parseValue($value, $type) {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
                return json_decode($value, true) ?: [];
            case 'array':
                return explode(',', $value);
            default:
                return $value;
        }
    }
    
    /**
     * Format value for storage based on type
     */
    private function formatValue($value, $type) {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'json':
                return json_encode($value);
            case 'array':
                return is_array($value) ? implode(',', $value) : $value;
            default:
                return (string) $value;
        }
    }
    
    /**
     * Get a setting value
     */
    public function get($key, $default = null) {
        return $this->settings[$key] ?? $default;
    }
    
    /**
     * Set a setting value
     */
    public function set($key, $value, $type = 'string') {
        if (!$this->db) {
            return false;
        }
        
        try {
            $formattedValue = $this->formatValue($value, $type);
            
            $stmt = $this->db->prepare('INSERT INTO settings (setting_key, setting_value, setting_type) 
                VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = ?, setting_type = ?');
            
            $stmt->execute([$key, $formattedValue, $type, $formattedValue, $type]);
            
            // Update local cache
            $this->settings[$key] = $this->parseValue($formattedValue, $type);
            
            return true;
        } catch (PDOException $e) {
            error_log('Failed to set setting: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a setting
     */
    public function delete($key) {
        if (!$this->db) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare('DELETE FROM settings WHERE setting_key = ?');
            $stmt->execute([$key]);
            
            // Remove from local cache
            unset($this->settings[$key]);
            
            return true;
        } catch (PDOException $e) {
            error_log('Failed to delete setting: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all settings
     */
    public function getAll() {
        return $this->settings;
    }
    
    /**
     * Check if a table exists
     */
    private function tableExists($table) {
        if (!$this->db) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare('SHOW TABLES LIKE ?');
            if (!$stmt) {
                return false;
            }
            $stmt->execute([$table]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
