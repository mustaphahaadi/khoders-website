<?php
/**
 * About page
 */

// Include the template system
require_once 'includes/template.php';

// Get the HTML content from the file
$html_file = 'pages/about.html';
$html_content = '';

if (file_exists($html_file)) {
    // Get the HTML content
    $html_content = file_get_contents($html_file);
    
    // Extract the body content
    preg_match('/<body.*?>(.*?)<\/body>/s', $html_content, $matches);
    $html_content = $matches[1] ?? $html_content;
} else {
    // Fallback content
    $html_content = '<div class="container mt-5"><h1>About KHODERS</h1><p>Information about our organization.</p></div>';
}

// Define meta data
$meta_data = [
    'description' => 'Learn about KHODERS, our mission, vision, and the story behind our campus coding club.',
    'keywords' => 'coding club, about khoders, programming community, tech education'
];

// Render the page
echo render_page($html_content, 'About - KHODERS WORLD', $meta_data);
?>
