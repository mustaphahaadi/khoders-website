<?php
/**
 * Database Migration Script
 * Run this to set up or update the database schema
 */

require_once __DIR__ . '/../config/database.php';

echo "KHODERS Database Migration\n";
echo "==========================\n\n";

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("ERROR: Could not connect to database\n");
}

echo "✓ Database connection established\n";

// Check if schema.sql exists
$schemaFile = __DIR__ . '/schema.sql';
if (!file_exists($schemaFile)) {
    die("ERROR: schema.sql not found\n");
}

echo "✓ Schema file found\n";

// Read schema
$sql = file_get_contents($schemaFile);

// Remove database creation and USE statements (already connected)
$sql = preg_replace('/^CREATE DATABASE.*?;/mi', '', $sql);
$sql = preg_replace('/^USE .*?;/mi', '', $sql);

// Split into individual statements
$statements = array_filter(
    array_map('trim', preg_split('/;[\r\n]+/', $sql)),
    function($stmt) { return !empty($stmt); }
);

echo "\nExecuting " . count($statements) . " SQL statements...\n\n";

$success = 0;
$failed = 0;

try {
    $db->beginTransaction();
    
    foreach ($statements as $index => $statement) {
        if (empty($statement)) continue;
        
        try {
            $db->exec($statement);
            
            // Extract table name for display
            if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                echo "✓ Created table: {$matches[1]}\n";
            } elseif (preg_match('/ALTER TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                echo "✓ Altered table: {$matches[1]}\n";
            } else {
                echo "✓ Executed statement " . ($index + 1) . "\n";
            }
            
            $success++;
        } catch (PDOException $e) {
            // Ignore "table already exists" errors
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "⊘ Table already exists (skipped)\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
                $failed++;
            }
        }
    }
    
    $db->commit();
    
    echo "\n==========================\n";
    echo "Migration Complete!\n";
    echo "Success: $success\n";
    echo "Failed: $failed\n";
    echo "==========================\n";
    
} catch (Exception $e) {
    $db->rollBack();
    echo "\nERROR: Migration failed - " . $e->getMessage() . "\n";
    exit(1);
}

// Verify tables exist
echo "\nVerifying tables...\n";
$tables = ['users', 'members', 'contacts', 'newsletter', 'form_logs', 'events', 'projects', 'team_members'];

foreach ($tables as $table) {
    try {
        $stmt = $db->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "✓ $table: $count records\n";
    } catch (PDOException $e) {
        echo "✗ $table: NOT FOUND\n";
    }
}

echo "\nMigration script completed successfully!\n";
