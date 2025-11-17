<?php
/**
 * Security Fixes - Utility Functions
 * Provides helper functions for common security issues
 */

/**
 * Safely include files with whitelist validation
 * @param string $file File path to include
 * @param string $baseDir Base directory for validation
 * @param array $allowedFiles Optional whitelist of allowed files
 * @return bool Success status
 */
function safeInclude($file, $baseDir, $allowedFiles = []) {
    $realPath = realpath($file);
    $realBase = realpath($baseDir);
    
    if (!$realPath || !$realBase) {
        return false;
    }
    
    // Check if file is within base directory
    if (strpos($realPath, $realBase) !== 0) {
        return false;
    }
    
    // Check whitelist if provided
    if (!empty($allowedFiles) && !in_array(basename($file), $allowedFiles)) {
        return false;
    }
    
    if (file_exists($realPath)) {
        include $realPath;
        return true;
    }
    
    return false;
}

/**
 * Validate file upload
 * @param array $file $_FILES array element
 * @param int $maxSize Maximum file size in bytes
 * @param array $allowedMimes Allowed MIME types
 * @param array $allowedExts Allowed file extensions
 * @return array ['valid' => bool, 'error' => string]
 */
function validateFileUpload($file, $maxSize = 5242880, $allowedMimes = [], $allowedExts = []) {
    if (!isset($file['tmp_name']) || !isset($file['size'])) {
        return ['valid' => false, 'error' => 'Invalid file'];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        return ['valid' => false, 'error' => 'File too large'];
    }
    
    // Check MIME type
    if (!empty($allowedMimes)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowedMimes)) {
            return ['valid' => false, 'error' => 'Invalid file type'];
        }
    }
    
    // Check extension
    if (!empty($allowedExts)) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts)) {
            return ['valid' => false, 'error' => 'Invalid file extension'];
        }
    }
    
    return ['valid' => true];
}

/**
 * Safely output user data with XSS protection
 * @param mixed $data Data to output
 * @param string $context Context (html, attr, js, url, css)
 * @return string Escaped data
 */
function safeOutput($data, $context = 'html') {
    if (is_array($data)) {
        return htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
    }
    
    switch ($context) {
        case 'attr':
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        case 'js':
            return json_encode($data);
        case 'url':
            return urlencode($data);
        case 'css':
            return preg_replace('/[^a-zA-Z0-9_-]/', '', $data);
        case 'html':
        default:
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Validate CORS origin
 * @param array $allowedOrigins List of allowed origins
 * @return bool True if origin is allowed
 */
function validateCORSOrigin($allowedOrigins) {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    if (empty($origin)) {
        return false;
    }
    
    // Never allow wildcard
    if (in_array('*', $allowedOrigins)) {
        return false;
    }
    
    return in_array($origin, $allowedOrigins);
}

/**
 * Set secure CORS headers
 * @param array $allowedOrigins List of allowed origins
 * @param array $allowedMethods HTTP methods
 * @param array $allowedHeaders HTTP headers
 */
function setSecureCORSHeaders($allowedOrigins, $allowedMethods = ['GET', 'POST'], $allowedHeaders = []) {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    if (validateCORSOrigin($allowedOrigins)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: ' . implode(', ', $allowedMethods));
        header('Access-Control-Allow-Headers: ' . implode(', ', $allowedHeaders));
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
    } else {
        // Reject non-whitelisted origins
        http_response_code(403);
        exit('CORS policy violation');
    }
}

/**
 * Sanitize database column name (for dynamic queries)
 * @param string $column Column name
 * @param array $allowedColumns Whitelist of allowed columns
 * @return string|null Sanitized column name or null if not allowed
 */
function sanitizeColumnName($column, $allowedColumns) {
    if (!in_array($column, $allowedColumns)) {
        return null;
    }
    
    // Additional validation: only alphanumeric and underscore
    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
        return null;
    }
    
    return $column;
}

/**
 * Validate file path is within allowed directory
 * @param string $filePath File path to validate
 * @param string $baseDir Base directory
 * @return bool True if path is valid
 */
function isPathWithinDirectory($filePath, $baseDir) {
    $realPath = realpath($filePath);
    $realBase = realpath($baseDir);
    
    if (!$realPath || !$realBase) {
        return false;
    }
    
    return strpos($realPath, $realBase) === 0;
}

?>
