<?php
/**
 * 404 error page
 */

// Set HTTP status code
http_response_code(404);

// Include the template system
require_once 'includes/template.php';

// Define the page content
$content = <<<HTML
<section class="error-404 section d-flex align-items-center justify-content-center min-vh-100">
  <div class="container text-center">
    <div class="error-content" data-aos="fade-up">
      <h1>404</h1>
      <h2>Page Not Found</h2>
      <p>The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
      <div class="error-actions mt-4">
        <a href="index.php" class="btn-primary">Back to Home</a>
        <a href="contact.php" class="btn-outline ms-3">Contact Support</a>
      </div>
      <div class="mt-5">
        <p>Here are some helpful links:</p>
        <div class="helpful-links d-flex flex-wrap justify-content-center gap-3 mt-3">
          <a href="about.php">About Us</a>
          <a href="services.php">Services</a>
          <a href="projects.php">Projects</a>
          <a href="events.php">Events</a>
          <a href="blog.php">Blog</a>
        </div>
      </div>
    </div>
  </div>
</section>
HTML;

// Define meta data
$meta_data = [
    'description' => 'The page you are looking for could not be found. Please check the URL or navigate back to the homepage.',
    'keywords' => '404, page not found, error, khoders'
];

// Render the page
echo render_page($content, 'Page Not Found - KHODERS WORLD', $meta_data);
?>
