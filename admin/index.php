<?php
/**
 * KHODERS WORLD Admin Panel Main Entry Point
 * This file acts as the router dispatcher for the admin panel
 * All requests are routed through here via index.php?route=page_name
 */

session_start();

// Load configuration files
require_once '../config/auth.php';
require_once '../config/database.php';
require_once '../config/security.php';
require_once __DIR__ . '/includes/admin_helpers.php';

// Require authentication
Auth::requireAuth('login.php');

// Load the admin router
require_once __DIR__ . '/routes.php';

// Load and display the admin template which will dispatch the router internally
require_once __DIR__ . '/template.php';
