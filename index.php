<?php
/**
 * KHODERS WORLD Website
 * Home page
 */

// Initialize error handling early
require_once 'config/error-handler.php';
$appEnv = getenv('APP_ENV') ?: 'development';
ErrorHandler::configure($appEnv, __DIR__ . '/logs');

// Include the router
require_once 'includes/router.php';

// Determine which page to route to (default to index)
$page = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : 'index';

// Route to the appropriate page using the centralized router
SiteRouter::route($page);
?>
