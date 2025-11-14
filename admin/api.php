<?php
/**
 * KHODERS WORLD Admin API Endpoint
 * This file serves as the entry point for all API requests
 */

// Start session
session_start();

// Include required files
require_once '../config/auth.php';
require_once '../config/security.php';
require_once './includes/api.php';

// Check if user is authenticated
if (!Auth::check()) {
    // Return unauthorized error
    header('HTTP/1.1 401 Unauthorized');
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required',
        'data' => null
    ]);
    exit;
}

// Get the requested endpoint
$endpoint = $_GET['endpoint'] ?? '';

// Create API handler
$api = new AdminAPI();

// Process the request
$api->process($endpoint);
