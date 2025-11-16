<?php
/**
 * KHODERS WORLD Admin Team Member Editor
 * Add or edit team members
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Team Member Editor - KHODERS WORLD Admin');
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../config/file-upload.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

// Initialize variables
$currentPage = 'team';
$action = $_GET['action'] ?? 'add';
$member_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';
$member = [
    'name' => '',
    'position' => '',
    'bio' => '',
    'photo_url' => '',
    'email' => '',
    'linkedin_url' => '',
    'github_url' => '',
    'twitter_url' => '',
    'personal_website' => '',
    'is_featured' => 0,
    'status' => 'active',
    'order_index' => 0
];

// Get current user
$user = Auth::user();

// Database connection
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        $error = 'Unable to connect to the database. Please verify database credentials and try again.';
    }
} catch (Exception $e) {
    $error = 'Database connection error: ' . $e->getMessage();
    $db = null;
}

// Check if editing existing team member
if ($db && $action === 'edit' && $member_id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM team_members WHERE id = ?");
        $stmt->execute([$member_id]);
        $existingMember = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingMember) {
            $member = $existingMember;
        } else {
            $error = 'Team member not found.';
        }
    } catch (PDOException $e) {
        $error = 'Error loading team member: ' . $e->getMessage();
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_member'])) {
    // Validate CSRF token
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } elseif (!$db) {
        $error = 'Database connection is not available. Cannot save team member.';
    } else {
        // Get form data
        $member['name'] = $_POST['name'] ?? '';
        $member['position'] = $_POST['position'] ?? '';
        $member['bio'] = $_POST['bio'] ?? '';
        $member['photo_url'] = $_POST['photo_url'] ?? '';
        $member['email'] = $_POST['email'] ?? '';
        $member['linkedin_url'] = $_POST['linkedin_url'] ?? '';
        $member['github_url'] = $_POST['github_url'] ?? '';
        $member['twitter_url'] = $_POST['twitter_url'] ?? '';
        $member['personal_website'] = $_POST['personal_website'] ?? '';
        $member['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
        $member['status'] = $_POST['status'] ?? 'active';
        $member['order_index'] = (int)($_POST['order_index'] ?? 0);
        
        // Handle photo upload if file is provided
        if (!empty($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
            $uploader = new FileUploader('team', 5 * 1024 * 1024); // 5MB limit
            $uploadResult = $uploader->upload($_FILES['photo_file']);
            
            if ($uploadResult['success']) {
                // Delete old photo if editing
                if ($action === 'edit' && !empty($member['photo_url'])) {
                    $uploader->delete($member['photo_url']);
                }
                $member['photo_url'] = $uploadResult['path'];
            } else {
                $error = 'Photo upload failed: ' . $uploadResult['error'];
            }
        }
        
        // Basic validation
        if (empty($member['name'])) {
            $error = 'Member name is required.';
        } elseif (!empty($member['email']) && !filter_var($member['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            try {
                if ($action === 'edit' && $member_id > 0) {
                    // Update existing team member
                    $stmt = $db->prepare("UPDATE team_members SET 
                        name = ?, 
                        position = ?, 
                        bio = ?, 
                        photo_url = ?, 
                        email = ?, 
                        linkedin_url = ?, 
                        github_url = ?, 
                        twitter_url = ?, 
                        personal_website = ?, 
                        is_featured = ?, 
                        status = ?, 
                        order_index = ?, 
                        updated_at = NOW() 
                        WHERE id = ?");
                    
                    $stmt->execute([
                        $member['name'],
                        $member['position'],
                        $member['bio'],
                        $member['photo_url'],
                        $member['email'],
                        $member['linkedin_url'],
                        $member['github_url'],
                        $member['twitter_url'],
                        $member['personal_website'],
                        $member['is_featured'],
                        $member['status'],
                        $member['order_index'],
                        $member_id
                    ]);
                    
                    $message = 'Team member updated successfully.';
                } else {
                    // Add new team member
                    $stmt = $db->prepare("INSERT INTO team_members (
                        name, position, bio, photo_url, email, linkedin_url, github_url, 
                        twitter_url, personal_website, is_featured, status, order_index, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    
                    $stmt->execute([
                        $member['name'],
                        $member['position'],
                        $member['bio'],
                        $member['photo_url'],
                        $member['email'],
                        $member['linkedin_url'],
                        $member['github_url'],
                        $member['twitter_url'],
                        $member['personal_website'],
                        $member['is_featured'],
                        $member['status'],
                        $member['order_index']
                    ]);
                    
                    $member_id = $db->lastInsertId();
                    $message = 'Team member created successfully.';
                }
                
                // Redirect to team list after successful save
                if (empty($error)) {
                    header('Location: ?route=team&message=' . urlencode($message));
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Error saving team member: ' . $e->getMessage();
            }
        }
    }
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
              <h4 class="card-title card-title-dash">
                <?php echo $action === 'edit' ? 'Edit Team Member' : 'Add New Team Member'; ?>
              </h4>
              <p class="card-subtitle card-subtitle-dash">
                <?php echo $action === 'edit' ? 'Update team member details' : 'Add a new team member'; ?>
              </p>
            </div>
            <div>
              <a href="?route=team" class="btn btn-secondary text-white mb-0 me-0">
                <i class="mdi mdi-arrow-left"></i> Back to Team
              </a>
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
          
          <form class="forms-sample" method="post" action="?route=team-editor&action=<?php echo $action; ?><?php echo $member_id ? '&id=' . $member_id : ''; ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="row mb-4">
              <div class="col-md-8">
                <div class="card">
                  <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="mdi mdi-account"></i> Member Information</h5>
                  </div>
                  <div class="card-body">
                    <div class="row mb-3">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" id="name" name="name" 
                                 value="<?php echo htmlspecialchars($member['name']); ?>" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="position" class="form-label">Position/Role</label>
                          <input type="text" class="form-control" id="position" name="position" 
                                 value="<?php echo htmlspecialchars($member['position']); ?>">
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group mb-3">
                      <label for="bio" class="form-label">Biography</label>
                      <textarea class="form-control" id="bio" name="bio" rows="5"><?php echo htmlspecialchars($member['bio']); ?></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                      <label for="email" class="form-label">Email Address</label>
                      <input type="email" class="form-control" id="email" name="email" 
                             value="<?php echo htmlspecialchars($member['email']); ?>">
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="card mb-4">
                  <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="mdi mdi-image"></i> Photo</h5>
                  </div>
                  <div class="card-body">
                    <div class="form-group mb-3">
                      <label for="photo_file" class="form-label">Member Photo</label>
                      <input type="file" class="form-control" id="photo_file" name="photo_file" accept="image/*" onchange="previewImage(this, 'photo_preview')">
                      <small class="form-text text-muted">JPG, PNG, WebP or GIF (Max 5MB)</small>
                      <?php if (!empty($member['photo_url'])): ?>
                        <div class="mt-2">
                          <label class="form-text text-muted d-block">Current Photo:</label>
                          <img id="photo_preview" src="<?php echo htmlspecialchars($member['photo_url']); ?>" alt="Member photo" style="max-width: 150px; max-height: 150px; border-radius: 50%;" class="img-thumbnail">
                        </div>
                      <?php else: ?>
                        <div class="mt-2">
                          <img id="photo_preview" style="display: none; max-width: 150px; max-height: 150px; border-radius: 50%;" class="img-thumbnail">
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                
                <div class="card mb-4">
                  <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="mdi mdi-share-variant"></i> Social Media</h5>
                  </div>
                  <div class="card-body">
                    <div class="form-group mb-3">
                      <label for="linkedin_url" class="form-label">
                        <i class="mdi mdi-linkedin"></i> LinkedIn URL
                      </label>
                      <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                             value="<?php echo htmlspecialchars($member['linkedin_url']); ?>">
                    </div>
                    
                    <div class="form-group mb-3">
                      <label for="github_url" class="form-label">
                        <i class="mdi mdi-github"></i> GitHub URL
                      </label>
                      <input type="url" class="form-control" id="github_url" name="github_url" 
                             value="<?php echo htmlspecialchars($member['github_url']); ?>">
                    </div>
                    
                    <div class="form-group mb-3">
                      <label for="twitter_url" class="form-label">
                        <i class="mdi mdi-twitter"></i> Twitter URL
                      </label>
                      <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                             value="<?php echo htmlspecialchars($member['twitter_url']); ?>">
                    </div>
                    
                    <div class="form-group mb-3">
                      <label for="personal_website" class="form-label">
                        <i class="mdi mdi-web"></i> Personal Website
                      </label>
                      <input type="url" class="form-control" id="personal_website" name="personal_website" 
                             value="<?php echo htmlspecialchars($member['personal_website']); ?>">
                    </div>
                  </div>
                </div>
                
                <div class="card">
                  <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="mdi mdi-cog"></i> Settings</h5>
                  </div>
                  <div class="card-body">
                    <div class="form-group mb-3">
                      <label for="status" class="form-label">Status</label>
                      <select class="form-select" id="status" name="status">
                        <option value="active" <?php echo $member['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $member['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="alumni" <?php echo $member['status'] === 'alumni' ? 'selected' : ''; ?>>Alumni</option>
                      </select>
                    </div>
                    
                    <div class="form-group mb-3">
                      <label for="order_index" class="form-label">Display Order</label>
                      <input type="number" class="form-control" id="order_index" name="order_index" 
                             value="<?php echo (int)($member['order_index']); ?>" min="0">
                      <small class="form-text text-muted">Lower numbers appear first</small>
                    </div>
                    
                    <div class="form-check form-check-flat form-check-primary mb-3">
                      <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="is_featured" 
                               <?php echo $member['is_featured'] ? 'checked' : ''; ?>>
                        Featured Member
                        <i class="input-helper"></i>
                      </label>
                      <small class="form-text text-muted d-block">Featured members appear on the homepage</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <button type="submit" name="save_member" class="btn btn-primary me-2">
              <?php echo $action === 'edit' ? 'Update Member' : 'Add Member'; ?>
            </button>
            <a href="?route=team" class="btn btn-light">Cancel</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function previewImage(input, previewId) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview = document.getElementById(previewId);
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

$(document).ready(function() {
  // Initialize rich text editor for bio
  if (typeof tinymce !== 'undefined') {
    tinymce.init({
      selector: '#bio',
      height: 200,
      menubar: false,
      plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table paste code help wordcount'
      ],
      toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
    });
  }
});
</script>
