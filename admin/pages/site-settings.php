<?php
/**
 * KHODERS WORLD Admin Site Settings Page
 * Manage global site settings and configuration
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Site Settings - KHODERS WORLD Admin');
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../includes/admin_helpers.php';
require_once __DIR__ . '/../includes/settings.php';

// Initialize variables
$currentPage = 'site-settings';
$message = '';
$error = '';

// Get current user
$user = Auth::user();

// Check admin permissions
if (!Auth::hasRole('admin')) {
    $error = 'You do not have permission to access site settings.';
} else {
    // Create settings instance
    $settingsHelper = new Settings();
    
    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
        // Validate CSRF token
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
            $settingsHelper->set('social_links', [
                'facebook' => $_POST['facebook_url'] ?? '',
                'twitter' => $_POST['twitter_url'] ?? '',
                'instagram' => $_POST['instagram_url'] ?? '',
                'linkedin' => $_POST['linkedin_url'] ?? '',
                'github' => $_POST['github_url'] ?? ''
            ], 'json');
            
            $message = 'Settings saved successfully.';
        }
    }
    
    // Get current settings
    $settings = $settingsHelper->getAll();
    $socialLinks = isset($settings['social_links']) ? 
                  (is_array($settings['social_links']) ? 
                   $settings['social_links'] : 
                   json_decode($settings['social_links'], true)) : 
                  [];
}

// Generate CSRF token
$csrfToken = Security::generateCSRFToken();
?>

<div class="content-wrapper">
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-sm-flex justify-content-between align-items-start mb-4">
            <div>
              <h4 class="card-title card-title-dash">Site Settings</h4>
              <p class="card-subtitle card-subtitle-dash">Configure global settings for KHODERS WORLD website</p>
            </div>
          </div>
          
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
          
          <?php if (Auth::hasRole('admin')): ?>
          <form class="forms-sample" method="post" action="?route=site-settings">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="row mb-4">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="mdi mdi-web"></i> General Settings</h5>
                  </div>
                  <div class="card-body">
                    <div class="form-group row mb-3">
                      <label for="site_name" class="col-sm-3 col-form-label">Site Name</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="site_name" name="site_name" 
                               value="<?php echo htmlspecialchars($settings['site_name'] ?? 'KHODERS WORLD'); ?>" required>
                      </div>
                    </div>
                    
                    <div class="form-group row mb-3">
                      <label for="site_description" class="col-sm-3 col-form-label">Site Description</label>
                      <div class="col-sm-9">
                        <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars($settings['site_description'] ?? 'Campus Coding Club'); ?></textarea>
                      </div>
                    </div>
                    
                    <div class="form-group row mb-3">
                      <label for="contact_email" class="col-sm-3 col-form-label">Contact Email</label>
                      <div class="col-sm-9">
                        <input type="email" class="form-control" id="contact_email" name="contact_email" 
                               value="<?php echo htmlspecialchars($settings['contact_email'] ?? 'admin@khoders.com'); ?>">
                      </div>
                    </div>
                    
                    <div class="form-group row mb-3">
                      <label for="theme_color" class="col-sm-3 col-form-label">Theme Color</label>
                      <div class="col-sm-9">
                        <div class="input-group">
                          <input type="color" class="form-control" id="theme_color" name="theme_color" 
                                 value="<?php echo htmlspecialchars($settings['theme_color'] ?? '#4B49AC'); ?>" style="max-width: 100px;">
                          <input type="text" class="form-control" id="theme_color_text" 
                                 value="<?php echo htmlspecialchars($settings['theme_color'] ?? '#4B49AC'); ?>" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row mb-4">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="mdi mdi-share-variant"></i> Social Media Links</h5>
                  </div>
                  <div class="card-body">
                    <div class="form-group row mb-3">
                      <label for="facebook_url" class="col-sm-3 col-form-label">
                        <i class="mdi mdi-facebook"></i> Facebook URL
                      </label>
                      <div class="col-sm-9">
                        <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                               value="<?php echo htmlspecialchars($socialLinks['facebook'] ?? ''); ?>">
                      </div>
                    </div>
                    
                    <div class="form-group row mb-3">
                      <label for="twitter_url" class="col-sm-3 col-form-label">
                        <i class="mdi mdi-twitter"></i> Twitter URL
                      </label>
                      <div class="col-sm-9">
                        <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                               value="<?php echo htmlspecialchars($socialLinks['twitter'] ?? ''); ?>">
                      </div>
                    </div>
                    
                    <div class="form-group row mb-3">
                      <label for="instagram_url" class="col-sm-3 col-form-label">
                        <i class="mdi mdi-instagram"></i> Instagram URL
                      </label>
                      <div class="col-sm-9">
                        <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                               value="<?php echo htmlspecialchars($socialLinks['instagram'] ?? ''); ?>">
                      </div>
                    </div>
                    
                    <div class="form-group row mb-3">
                      <label for="linkedin_url" class="col-sm-3 col-form-label">
                        <i class="mdi mdi-linkedin"></i> LinkedIn URL
                      </label>
                      <div class="col-sm-9">
                        <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                               value="<?php echo htmlspecialchars($socialLinks['linkedin'] ?? ''); ?>">
                      </div>
                    </div>
                    
                    <div class="form-group row mb-3">
                      <label for="github_url" class="col-sm-3 col-form-label">
                        <i class="mdi mdi-github"></i> GitHub URL
                      </label>
                      <div class="col-sm-9">
                        <input type="url" class="form-control" id="github_url" name="github_url" 
                               value="<?php echo htmlspecialchars($socialLinks['github'] ?? ''); ?>">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row mb-4">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="mdi mdi-cog"></i> System Settings</h5>
                  </div>
                  <div class="card-body">
                    <div class="form-group row mb-3">
                      <label for="items_per_page" class="col-sm-3 col-form-label">Items Per Page</label>
                      <div class="col-sm-9">
                        <input type="number" class="form-control" id="items_per_page" name="items_per_page" 
                               value="<?php echo (int) ($settings['items_per_page'] ?? 20); ?>" min="5" max="100">
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
                  </div>
                </div>
              </div>
            </div>
            
            <button type="submit" name="save_settings" class="btn btn-primary me-2">Save Settings</button>
            <button type="reset" class="btn btn-light">Reset</button>
          </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Update text input when color picker changes
  $('#theme_color').on('input', function() {
    $('#theme_color_text').val($(this).val());
  });
});
</script>
