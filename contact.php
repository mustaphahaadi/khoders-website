<?php
/**
 * Contact page
 */

// Include the template system
require_once 'includes/template.php';

// Get the HTML content from the file
$html_file = 'pages/contact.html';
$html_content = '';

if (file_exists($html_file)) {
    // Get the HTML content
    $html_content = file_get_contents($html_file);
    
    // Extract the body content
    preg_match('/<body.*?>(.*?)<\/body>/s', $html_content, $matches);
    $html_content = $matches[1] ?? $html_content;
} else {
    // Fallback content
    $html_content = '<div class="container mt-5"><h1>Contact KHODERS</h1><p>Get in touch with our team.</p></div>';
}

// Define meta data
$meta_data = [
    'description' => 'Contact KHODERS coding club. Get in touch with our team for inquiries, collaborations, or to join our community.',
    'keywords' => 'contact khoders, coding club contact, programming community, tech education'
];

// Render the page
echo render_page($html_content, 'Contact - KHODERS WORLD', $meta_data);
?>
