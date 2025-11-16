<?php
/**
 * Database Schema Update Runner
 * Run this file once to apply all schema updates
 */

require_once __DIR__ . '/../config/database.php';

echo "Starting database schema updates...\n\n";

try {
    $database = Database::getInstance();
    $db = $database->getConnection();
    
    if (!$db) {
        die("ERROR: Could not connect to database\n");
    }
    
    // Read SQL file (use clean version)
    $sqlFile = __DIR__ . '/schema_updates_clean.sql';
    if (!file_exists($sqlFile)) {
        $sqlFile = __DIR__ . '/schema_updates.sql';
    }
    $sql = file_get_contents($sqlFile);
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $success = 0;
    $failed = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) continue;
        
        try {
            $db->exec($statement);
            $success++;
            echo "✓ Executed successfully\n";
        } catch (PDOException $e) {
            // Ignore "table already exists" errors
            if (strpos($e->getMessage(), '1050') !== false || strpos($e->getMessage(), 'already exists') !== false) {
                $success++;
                echo "✓ Already exists (skipped)\n";
            } else {
                $failed++;
                echo "✗ Failed: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n=================================\n";
    echo "Schema updates complete!\n";
    echo "Success: $success\n";
    echo "Failed: $failed\n";
    echo "=================================\n";
    
    if ($failed === 0) {
        echo "\n✅ All updates applied successfully!\n";
        echo "\nYou can now login to admin panel:\n";
        echo "URL: /admin/\n";
        echo "Username: admin\n";
        echo "Password: Admin@2024!\n";
        echo "\n⚠️  IMPORTANT: Change password after first login!\n";
    } else {
        echo "\n⚠️  Some updates failed. Check errors above.\n";
    }
    
} catch (Exception $e) {
    die("ERROR: " . $e->getMessage() . "\n");
}
