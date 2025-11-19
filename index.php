<?php
// Start session
session_start();

// Include router
require_once 'includes/router.php';

// Determine which page to route to (default to index)
$page = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : 'index';

// Route to the appropriate page using the centralized router
SiteRouter::route($page);
?>
