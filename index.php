<?php
/**
 * KHODERS WORLD Website
 * Home page
 */

// Check if we're routing to a specific page
if (isset($_GET['page']) && !empty($_GET['page'])) {
    // Include the router
    require_once 'includes/router.php';
    
    // Route to the appropriate page
    SiteRouter::route($_GET['page']);
    exit;
}

// This is the home page

// Include the template system
require_once 'includes/template.php';

// Get the HTML content from the file
$html_file = 'pages/index.html';
$html_content = '';

if (file_exists($html_file)) {
    // Get the HTML content
    $html_content = file_get_contents($html_file);
    
    // Extract the body content
    preg_match('/<body.*?>(.*?)<\/body>/s', $html_content, $matches);
    $html_content = $matches[1] ?? $html_content;
} else {
    // Fallback content
    $html_content = <<<HTML
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1>Welcome to KHODERS WORLD</h1>
                <p class="lead">The premier campus coding club dedicated to fostering programming skills and technological innovation among students.</p>
                <div class="hero-buttons">
                    <a href="about.php" class="btn btn-primary">Learn More</a>
                    <a href="register.php" class="btn btn-outline-primary">Join Now</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="assets/img/hero-image.jpg" alt="KHODERS Coding Club" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>
HTML;
}

// Define meta data
$meta_data = [
    'description' => 'KHODERS is a campus coding club dedicated to fostering programming skills and technological innovation among students',
    'keywords' => 'coding, programming, tech, campus, club, KHODERS, Ghana'
];

// Render the page
echo render_page($html_content, 'KHODERS - Campus Coding Club', $meta_data);
?>
