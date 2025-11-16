<?php
/**
 * Database Configuration
 * 
 * This file contains database connection settings
 */

// Load environment variables
require_once __DIR__ . '/../config/env.php';

// Database credentials from environment
define('DB_SERVER', getenv('DB_HOST') ?: 'localhost');
define('DB_USERNAME', getenv('DB_USER') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'khoders_db');

// Create database connection
$conn = null;

/**
 * Get database connection
 * 
 * @return mysqli|null Returns database connection or null on failure
 */
function getDBConnection() {
    global $conn;
    
    // If connection already exists, return it
    if ($conn !== null) {
        return $conn;
    }
    
    // Create a new connection
    try {
        $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        // Set UTF-8 character set
        $conn->set_charset("utf8mb4");
        
        return $conn;
    } catch (Exception $e) {
        // Log error (production would use proper logging)
        error_log("Database connection error: " . $e->getMessage());
        return null;
    }
}

/**
 * Close database connection
 */
function closeDBConnection() {
    global $conn;
    
    if ($conn !== null) {
        $conn->close();
        $conn = null;
    }
}

