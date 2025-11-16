<?php
/**
 * KHODERS WORLD Routing Migration Tool
 * Converts all .html files to use unified PHP routing (index.php?page=xxx)
 * 
 * This tool:
 * 1. Updates all hardcoded .html links to use PHP routing
 * 2. Creates backup of original .html files before modification
 * 3. Maintains full backward compatibility
 * 4. Validates all changes before applying
 * 
 * Usage: php tools/migrate-routing.php [--force] [--restore]
 */

// Configuration
define('PAGES_DIR', __DIR__ . '/../pages');
define('BACKUP_DIR', __DIR__ . '/../backups/routing-migration');
define('FORCE_MIGRATE', in_array('--force', $argv));
define('RESTORE_BACKUP', in_array('--restore', $argv));

// Initialize
$log = [];
$errors = [];

/**
 * Log a message
 */
function log_msg($msg, $type = 'info') {
    global $log;
    $timestamp = date('Y-m-d H:i:s');
    $log[] = "[$timestamp] [$type] $msg";
    echo "[" . strtoupper($type) . "] $msg\n";
}

/**
 * Add an error
 */
function add_error($msg) {
    global $errors;
    $errors[] = $msg;
    log_msg($msg, 'error');
}

/**
 * Get the page name from a filename
 */
function get_page_name($filename) {
    return preg_replace('/\.(html|php)$/', '', basename($filename));
}

/**
 * Create backup directory if it doesn't exist
 */
function ensure_backup_dir() {
    if (!is_dir(BACKUP_DIR)) {
        if (!mkdir(BACKUP_DIR, 0755, true)) {
            add_error("Failed to create backup directory: " . BACKUP_DIR);
            return false;
        }
        log_msg("Created backup directory: " . BACKUP_DIR);
    }
    return true;
}

/**
 * Backup original HTML file
 */
function backup_file($filepath) {
    if (!ensure_backup_dir()) {
        return false;
    }
    
    $filename = basename($filepath);
    $backup_path = BACKUP_DIR . '/' . $filename . '.backup';
    
    if (!copy($filepath, $backup_path)) {
        add_error("Failed to backup file: $filepath");
        return false;
    }
    
    log_msg("Backed up: $filename -> $backup_path", 'success');
    return $backup_path;
}

/**
 * Convert .html links to PHP routing format
 */
function convert_links($content) {
    $replacements = 0;
    
    // Pattern: href="something.html" or href='something.html'
    // Captures: href=("|')([^"']+\.html)(?:\?([^"']*))?(["|'])
    $pattern = '/href=(["\'])([^"\']+\.html)(?:\?([^"\']*))?(["\'])/i';
    
    $converted = preg_replace_callback($pattern, function($matches) use (&$replacements) {
        $quote = $matches[1];
        $href = $matches[2];
        $query = $matches[3] ?? '';
        
        // Skip external links and anchors
        if (strpos($href, '://') !== false || strpos($href, '#') === 0) {
            return $matches[0];
        }
        
        // Get page name from filename
        $page_name = get_page_name($href);
        
        // Skip if it's just index.html or index.php
        if ($page_name === 'index') {
            $new_href = 'index.php';
        } else {
            // Build new URL
            if (!empty($query)) {
                $new_href = 'index.php?page=' . urlencode($page_name) . '&' . $query;
            } else {
                $new_href = 'index.php?page=' . urlencode($page_name);
            }
        }
        
        $replacements++;
        return 'href=' . $quote . $new_href . $quote;
    }, $content);
    
    // Also handle document.location and window.location patterns
    $converted = preg_replace_callback(
        '/(?:document\.location|window\.location(?:\.href)?)\s*=\s*(["\'])([^"\']+\.html)(["\'])/i',
        function($matches) use (&$replacements) {
            $quote = $matches[1];
            $href = $matches[2];
            $page_name = get_page_name($href);
            
            if ($page_name === 'index') {
                $new_href = 'index.php';
            } else {
                $new_href = 'index.php?page=' . urlencode($page_name);
            }
            
            $replacements++;
            return $matches[0][0] . " = " . $quote . $new_href . $quote;
        },
        $converted
    );
    
    return ['content' => $converted, 'replacements' => $replacements];
}

/**
 * Validate converted content
 */
function validate_content($original, $converted) {
    // Check that structure is preserved
    if (strlen($converted) < strlen($original) * 0.9) {
        return false;
    }
    
    // Check that HTML tags are still balanced (basic check)
    if (substr_count($converted, '<') !== substr_count($converted, '>')) {
        return false;
    }
    
    return true;
}

/**
 * Migrate a single HTML file
 */
function migrate_html_file($filepath) {
    $filename = basename($filepath);
    
    // Read original content
    $original_content = file_get_contents($filepath);
    if ($original_content === false) {
        add_error("Failed to read file: $filepath");
        return false;
    }
    
    // Convert links
    $result = convert_links($original_content);
    $converted_content = $result['content'];
    $replacements = $result['replacements'];
    
    // Validate conversion
    if (!validate_content($original_content, $converted_content)) {
        add_error("Validation failed for $filename - content structure may be corrupted");
        return false;
    }
    
    // Backup original
    if (!backup_file($filepath)) {
        return false;
    }
    
    // Apply changes
    if (file_put_contents($filepath, $converted_content) === false) {
        add_error("Failed to write converted content to: $filepath");
        return false;
    }
    
    log_msg("Migrated: $filename ($replacements link changes)", 'success');
    return true;
}

/**
 * Restore from backup
 */
function restore_from_backup($filepath) {
    $filename = basename($filepath);
    $backup_path = BACKUP_DIR . '/' . $filename . '.backup';
    
    if (!file_exists($backup_path)) {
        add_error("No backup found for: $filename");
        return false;
    }
    
    $backup_content = file_get_contents($backup_path);
    if ($backup_content === false) {
        add_error("Failed to read backup: $backup_path");
        return false;
    }
    
    if (file_put_contents($filepath, $backup_content) === false) {
        add_error("Failed to restore: $filepath");
        return false;
    }
    
    log_msg("Restored: $filename from backup", 'success');
    return true;
}

/**
 * Main migration process
 */
function main() {
    log_msg("=== KHODERS WORLD Routing Migration Tool ===");
    
    // Handle restore mode
    if (RESTORE_BACKUP) {
        log_msg("Restore mode enabled");
        
        if (!is_dir(BACKUP_DIR)) {
            add_error("No backup directory found");
            return false;
        }
        
        $backups = glob(BACKUP_DIR . '/*.html.backup');
        if (empty($backups)) {
            add_error("No backups found");
            return false;
        }
        
        foreach ($backups as $backup_file) {
            $original_file = PAGES_DIR . '/' . str_replace('.backup', '', basename($backup_file));
            restore_from_backup($original_file);
        }
        
        log_msg("Restore completed");
        return true;
    }
    
    // Check pages directory
    if (!is_dir(PAGES_DIR)) {
        add_error("Pages directory not found: " . PAGES_DIR);
        return false;
    }
    
    // Find all HTML files
    $html_files = glob(PAGES_DIR . '/*.html');
    if (empty($html_files)) {
        add_error("No HTML files found in " . PAGES_DIR);
        return false;
    }
    
    log_msg("Found " . count($html_files) . " HTML files to migrate");
    
    // Validate mode
    if (!FORCE_MIGRATE) {
        log_msg("DRY RUN MODE - Use --force to apply changes");
        log_msg("Analyzing links that would be converted...");
        
        $total_replacements = 0;
        foreach ($html_files as $filepath) {
            $content = file_get_contents($filepath);
            $result = convert_links($content);
            if ($result['replacements'] > 0) {
                log_msg("Would convert: " . basename($filepath) . " (" . $result['replacements'] . " links)");
                $total_replacements += $result['replacements'];
            }
        }
        
        log_msg("Total link replacements that would be made: $total_replacements");
        log_msg("Run with --force flag to apply changes: php tools/migrate-routing.php --force");
        return true;
    }
    
    // Force migration mode
    log_msg("FORCE MODE - Applying changes to all HTML files");
    
    $migrated = 0;
    foreach ($html_files as $filepath) {
        if (migrate_html_file($filepath)) {
            $migrated++;
        }
    }
    
    log_msg("=== Migration Complete ===");
    log_msg("Successfully migrated: $migrated / " . count($html_files) . " files");
    
    if (!empty($errors)) {
        log_msg("Errors encountered: " . count($errors));
        return false;
    }
    
    return true;
}

// Run the migration
$success = main();

// Summary
echo "\n";
log_msg("=== SUMMARY ===");
log_msg("Total log entries: " . count($log));
log_msg("Total errors: " . count($errors));

if (empty($errors)) {
    log_msg("Status: SUCCESS", 'success');
    exit(0);
} else {
    log_msg("Status: FAILED WITH ERRORS", 'error');
    exit(1);
}
?>
