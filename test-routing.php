<?php
/**
 * Test page for routing system
 */

// Include the router
require_once 'includes/router.php';

// Initialize the router
SiteRouter::init();

// Test URLs for all pages
$pages = [
    'index',
    'about',
    'blog',
    'blog-details',
    'careers',
    'code-of-conduct',
    'contact',
    'courses',
    'events',
    'faq',
    'instructors',
    'join-program',
    'membership-tiers',
    'mentor-profile',
    'privacy-policy',
    'program-details',
    'projects',
    'register',
    'resources',
    'services',
    'team',
    'terms-of-service',
    '404'
];

// Output HTML
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Routing Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        a { color: #0066cc; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>KHODERS Website Routing Test</h1>
    <p>This page tests the routing system for all pages in the website.</p>
    
    <table>
        <tr>
            <th>Page Name</th>
            <th>URL</th>
            <th>Test Link</th>
        </tr>';

// Generate table rows for each page
foreach ($pages as $page) {
    $url = SiteRouter::getUrl($page);
    echo '<tr>
            <td>' . htmlspecialchars($page) . '</td>
            <td>' . htmlspecialchars($url) . '</td>
            <td><a href="' . htmlspecialchars($url) . '" target="_blank">Test</a></td>
          </tr>';
}

echo '</table>
</body>
</html>';
?>
