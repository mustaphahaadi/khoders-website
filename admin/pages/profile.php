<?php
/**
 * KHODERS WORLD Admin Profile Page
 * Allows users to view and edit their profile
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

// Get user info
$user = Auth::user();

// Process form submission
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Validate CSRF token
    require_once __DIR__ . '/../../config/security.php';
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        // Get database connection
        require_once __DIR__ . '/../../config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        if (!$db) {
            $error = 'Database connection failed.';
        } else {
            try {
                // Update user profile
                $stmt = $db->prepare('UPDATE admins SET email = ?, display_name = ? WHERE id = ?');
                $stmt->execute([
                    $_POST['email'] ?? '',
                    $_POST['display_name'] ?? '',
                    $user['id']
                ]);
                
                // Check if password should be updated
                if (!empty($_POST['new_password'])) {
                    if (empty($_POST['current_password'])) {
                        $error = 'Current password is required to set a new password.';
                    } else {
                        // Verify current password
                        $stmt = $db->prepare('SELECT password_hash FROM admins WHERE id = ?');
                        $stmt->execute([$user['id']]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($result && Security::verifyPassword($_POST['current_password'], $result['password_hash'])) {
                            // Update password
                            $newPasswordHash = Security::hashPassword($_POST['new_password']);
                            $stmt = $db->prepare('UPDATE admins SET password_hash = ? WHERE id = ?');
                            $stmt->execute([$newPasswordHash, $user['id']]);
                            
                            $message = 'Profile and password updated successfully.';
                        } else {
                            $error = 'Current password is incorrect.';
                        }
                    }
                } else {
                    $message = 'Profile updated successfully.';
                }
            } catch (PDOException $e) {
                $error = 'Failed to update profile: ' . $e->getMessage();
            }
        }
    }
}

// Get updated user info
require_once __DIR__ . '/../../config/database.php';
$database = new Database();
$db = $database->getConnection();

if ($db) {
    try {
        $stmt = $db->prepare('SELECT username, email, display_name, role, last_login FROM admins WHERE id = ?');
        $stmt->execute([$user['id']]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Failed to load user data: ' . $e->getMessage();
    }
}

// Generate CSRF token
require_once __DIR__ . '/../../config/security.php';
$csrfToken = Security::generateCSRFToken();
?>

<div class="content-wrapper">
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Profile Information</h4>
          <p class="card-description">Update your personal information</p>
          
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
            
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>" readonly>
              <small class="form-text text-muted">Username cannot be changed.</small>
            </div>
            
            <div class="form-group">
              <label for="display_name">Display Name</label>
              <input type="text" class="form-control" id="display_name" name="display_name" value="<?php echo htmlspecialchars($userData['display_name'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
              <label for="email">Email address</label>
              <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
              <label for="role">Role</label>
              <input type="text" class="form-control" id="role" value="<?php echo htmlspecialchars(ucfirst($userData['role'] ?? '')); ?>" readonly>
            </div>
            
            <div class="form-group">
              <label for="last_login">Last Login</label>
              <input type="text" class="form-control" id="last_login" value="<?php echo htmlspecialchars($userData['last_login'] ?? ''); ?>" readonly>
            </div>
            
            <button type="submit" name="update_profile" class="btn btn-primary me-2">Save Changes</button>
            <button type="reset" class="btn btn-light">Cancel</button>
          </form>
        </div>
      </div>
    </div>
    
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Change Password</h4>
          <p class="card-description">Update your password</p>
          
          <form class="forms-sample" method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="form-group">
              <label for="current_password">Current Password</label>
              <input type="password" class="form-control" id="current_password" name="current_password">
            </div>
            
            <div class="form-group">
              <label for="new_password">New Password</label>
              <input type="password" class="form-control" id="new_password" name="new_password">
            </div>
            
            <div class="form-group">
              <label for="confirm_password">Confirm New Password</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password">
            </div>
            
            <button type="submit" name="update_profile" class="btn btn-primary me-2">Change Password</button>
            <button type="reset" class="btn btn-light">Cancel</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Password confirmation validation
  const newPasswordField = document.getElementById('new_password');
  const confirmPasswordField = document.getElementById('confirm_password');
  
  confirmPasswordField.addEventListener('input', function() {
    if (newPasswordField.value !== confirmPasswordField.value) {
      confirmPasswordField.setCustomValidity('Passwords do not match');
    } else {
      confirmPasswordField.setCustomValidity('');
    }
  });
  
  newPasswordField.addEventListener('input', function() {
    if (confirmPasswordField.value !== '') {
      if (newPasswordField.value !== confirmPasswordField.value) {
        confirmPasswordField.setCustomValidity('Passwords do not match');
      } else {
        confirmPasswordField.setCustomValidity('');
      }
    }
  });
});
</script>
