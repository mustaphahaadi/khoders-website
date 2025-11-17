<?php
/**
 * 404 Page - Not Found
 */

$page_title = '404 - Page Not Found - KHODERS';
$meta_data = [
    'description' => 'The page you are looking for could not be found.',
    'keywords' => '404 error, page not found, error page'
];

ob_start();
?>

<main class="main error-404">
  <div class="container d-flex h-100 align-items-center justify-content-center">
    <div class="text-center">
      <h1 class="display-1 fw-bold mb-4">404</h1>
      <h2 class="mb-3">Page Not Found</h2>
      <p class="mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
      
      <div class="error-actions mb-4">
        <a href="<?php echo SiteRouter::getUrl('index'); ?>" class="btn btn-primary me-2">Back to Home</a>
        <a href="<?php echo SiteRouter::getUrl('contact'); ?>" class="btn btn-outline-primary">Contact Support</a>
      </div>

      <h4 class="mb-3">Popular Links</h4>
      <ul class="list-unstyled">
        <li><a href="<?php echo SiteRouter::getUrl('about'); ?>">About Us</a></li>
        <li><a href="<?php echo SiteRouter::getUrl('courses'); ?>">Courses</a></li>
        <li><a href="<?php echo SiteRouter::getUrl('events'); ?>">Events</a></li>
        <li><a href="<?php echo SiteRouter::getUrl('blog'); ?>">Blog</a></li>
        <li><a href="<?php echo SiteRouter::getUrl('contact'); ?>">Contact Us</a></li>
      </ul>
    </div>
  </div>
</main>

<?php
$html_content = ob_get_clean();

if (isset($_GET['page'])) {
    require_once __DIR__ . '/../includes/template.php';
    echo render_page($html_content, $page_title, $meta_data);
    exit;
}

echo $html_content;
?>
