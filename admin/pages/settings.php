<?php
/**
 * KHODERS WORLD Admin Settings Page
 * Allows administrators to configure system settings
 */

// Check if directly accessed
if (!defined('PAGE_TITLE')) {
    require_once '../includes/router.php';
    Router::notFound(function() {
        echo '<h1>404 Not Found</h1>';
        echo '<p>Direct access to this page is not allowed.</p>';
    });
    Router::execute404();
    exit;
}

// Include settings helper
require_once __DIR__ . '/../includes/settings.php';

// Create settings instance
$settingsHelper = new Settings();

// Process form submission
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    // Validate CSRF token
    require_once __DIR__ . '/../../config/security.php';
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        // Process each setting
        $settingsHelper->set('site_name', $_POST['site_name'] ?? 'KHODERS WORLD', 'string');
        $settingsHelper->set('site_description', $_POST['site_description'] ?? '', 'string');
        $settingsHelper->set('contact_email', $_POST['contact_email'] ?? '', 'string');
        $settingsHelper->set('enable_registration', isset($_POST['enable_registration']) ? 1 : 0, 'boolean');
        $settingsHelper->set('maintenance_mode', isset($_POST['maintenance_mode']) ? 1 : 0, 'boolean');
        $settingsHelper->set('items_per_page', (int) ($_POST['items_per_page'] ?? 20), 'integer');
        $settingsHelper->set('theme_color', $_POST['theme_color'] ?? '#4B49AC', 'string');
        
        $message = 'Settings saved successfully.';
    }
}

// Get current settings
$settings = $settingsHelper->getAll();

// Generate CSRF token
require_once __DIR__ . '/../../config/security.php';
$csrfToken = Security::generateCSRFToken();
?>

<div class="content-wrapper">
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">System Settings</h4>
          <p class="card-description">Configure KHODERS WORLD admin panel settings</p>
          
          <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
              <i class="mdi mdi-check-circle-outline"></i> <?php echo htmlspecialchars($message); ?>
            </div>
          <?php endif; ?>
          
          <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
              <i class="mdi mdi-alert-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
          <?php endif; ?>
          
          <form class="forms-sample" method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="form-group row">
              <label for="site_name" class="col-sm-3 col-form-label">Site Name</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="site_name" name="site_name" 
                       value="<?php echo htmlspecialchars($settings['site_name'] ?? 'KHODERS WORLD'); ?>" required>
              </div>
            </div>
            
            <div class="form-group row">
              <label for="site_description" class="col-sm-3 col-form-label">Site Description</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="site_description" name="site_description" 
                       value="<?php echo htmlspecialchars($settings['site_description'] ?? 'Campus Coding Club Admin Panel'); ?>">
              </div>
            </div>
            
            <div class="form-group row">
              <label for="contact_email" class="col-sm-3 col-form-label">Contact Email</label>
              <div class="col-sm-9">
                <input type="email" class="form-control" id="contact_email" name="contact_email" 
                       value="<?php echo htmlspecialchars($settings['contact_email'] ?? 'admin@khoders.com'); ?>">
              </div>
            </div>
            
            <div class="form-group row">
              <label for="items_per_page" class="col-sm-3 col-form-label">Items Per Page</label>
              <div class="col-sm-9">
                <input type="number" class="form-control" id="items_per_page" name="items_per_page" 
                       value="<?php echo (int) ($settings['items_per_page'] ?? 20); ?>" min="5" max="100">
              </div>
            </div>
            
            <div class="form-group row">
              <label for="theme_color" class="col-sm-3 col-form-label">Theme Color</label>
              <div class="col-sm-9">
                <input type="color" class="form-control" id="theme_color" name="theme_color" 
                       value="<?php echo htmlspecialchars($settings['theme_color'] ?? '#4B49AC'); ?>">
              </div>
            </div>
            
            <div class="form-check form-check-flat form-check-primary mb-3">
              <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="enable_registration" 
                       <?php echo ($settings['enable_registration'] ?? true) ? 'checked' : ''; ?>>
                Enable User Registration
                <i class="input-helper"></i>
              </label>
            </div>
            
            <div class="form-check form-check-flat form-check-primary mb-3">
              <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="maintenance_mode" 
                       <?php echo ($settings['maintenance_mode'] ?? false) ? 'checked' : ''; ?>>
                Maintenance Mode
                <i class="input-helper"></i>
              </label>
            </div>
            
            <button type="submit" name="save_settings" class="btn btn-primary me-2">Save Settings</button>
            <button type="reset" class="btn btn-light">Cancel</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
