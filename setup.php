<?php
/**
 * Khoders World - Database Setup Script
 * 
 * This script initializes the database schema and applies any necessary updates.
 * It consolidates previous migration scripts into a single setup process.
 */

// Enable error reporting for setup
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';

echo "============================================\n";
echo "KHODERS WORLD - DATABASE SETUP\n";
echo "============================================\n\n";

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("[ERROR] Database connection failed. Check your configuration.\n");
}

echo "[INFO] Database connection established.\n";
echo "[INFO] Applying schema from database/schema.sql...\n";

if ($database->createTables()) {
    echo "[SUCCESS] Database schema applied successfully.\n";
} else {
    echo "[ERROR] Failed to apply database schema. Check logs for details.\n";
    exit(1);
}

echo "\n============================================\n";
echo "SETUP COMPLETED SUCCESSFULLY\n";
echo "============================================\n";
?>
