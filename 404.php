<?php
/**
 * 404 error page
 */

// Set HTTP status code
http_response_code(404);

// Include the router
require_once 'includes/router.php';

// Delegate rendering to the centralized router
SiteRouter::route('404');
?>
