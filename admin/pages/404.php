<?php
/**
 * KHODERS WORLD Admin 404 Page
 * Displayed when a page is not found
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', '404 Not Found - KHODERS WORLD Admin');
}

// Get the current route for debugging
$currentRoute = Router::getCurrentRoute();
$availableRoutes = array_keys(Router::getRoutes());

// For debugging
$debugInfo = [
    'Current Route' => $currentRoute,
    'Available Routes' => implode(', ', $availableRoutes),
    'Request URI' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
    'Script Name' => $_SERVER['SCRIPT_NAME'] ?? 'Unknown',
    'Route Parameter' => $_GET['route'] ?? 'Not set'
];
?>

<div class="content-wrapper d-flex align-items-center text-center error-page bg-light">
  <div class="row flex-grow">
    <div class="col-lg-8 mx-auto text-center">
      <div class="row align-items-center d-flex flex-row">
        <div class="col-lg-6 text-lg-right pr-lg-4">
          <h1 class="display-1 mb-0 text-danger">404</h1>
        </div>
        <div class="col-lg-6 error-page-divider text-lg-left pl-lg-4">
          <h2>SORRY!</h2>
          <h3 class="font-weight-light">The page you're looking for was not found.</h3>
        </div>
      </div>
      <div class="row mt-5">
        <div class="col-12 mt-xl-2">
          <p class="text-muted font-weight-medium mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
          <a class="btn btn-primary" href="index.php">
            <i class="mdi mdi-home me-2"></i>Back to Dashboard
          </a>
          
          <?php if (Auth::hasRole('admin')): ?>
          <div class="card mt-4">
            <div class="card-header bg-light">
              <h5 class="mb-0">Debug Information</h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <tbody>
                    <?php foreach ($debugInfo as $key => $value): ?>
                      <tr>
                        <th><?php echo htmlspecialchars($key); ?></th>
                        <td><?php echo htmlspecialchars($value); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <div class="alert alert-info mt-3">
                <p class="mb-0">Try using one of these routes: <strong>index</strong>, <strong>members</strong>, <strong>contacts</strong>, <strong>newsletter</strong>, <strong>events</strong>, <strong>projects</strong>, <strong>form-logs</strong>, <strong>settings</strong>, <strong>profile</strong></p>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Update the page title
document.title = '404 Not Found - KHODERS WORLD Admin';
</script>
