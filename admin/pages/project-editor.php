<?php
/**
 * KHODERS WORLD Admin Project Editor
 * Add or edit projects
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Project Editor - KHODERS WORLD Admin');
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../config/file-upload.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

// Initialize variables
$currentPage = 'projects';
$action = $_GET['action'] ?? 'add';
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';
$project = [
    'title' => '',
    'description' => '',
    'image_url' => '',
    'tech_stack' => '[]',
    'github_url' => '',
    'demo_url' => '',
    'is_featured' => 0,
    'status' => 'active'
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

// Check if editing existing project
if ($db && $action === 'edit' && $project_id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $existingProject = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingProject) {
            $project = $existingProject;
        } else {
            $error = 'Project not found.';
        }
    } catch (PDOException $e) {
        $error = 'Error loading project: ' . $e->getMessage();
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_project'])) {
    // Validate CSRF token
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } elseif (!$db) {
        $error = 'Database connection is not available. Cannot save project.';
    } else {
        // Get form data
        $project['title'] = $_POST['title'] ?? '';
        $project['description'] = $_POST['description'] ?? '';
        $project['image_url'] = $_POST['image_url'] ?? '';
        $project['github_url'] = $_POST['github_url'] ?? '';
        $project['demo_url'] = $_POST['demo_url'] ?? '';
        $project['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
        $project['status'] = $_POST['status'] ?? 'active';
        
        // Process tech stack as JSON
        $techStackItems = isset($_POST['tech_stack']) ? explode(',', $_POST['tech_stack']) : [];
        $techStackItems = array_map('trim', $techStackItems);
        $techStackItems = array_filter($techStackItems);
        $project['tech_stack'] = json_encode($techStackItems);
        
        // Handle image upload if file is provided
        if (!empty($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $uploader = new FileUploader('projects', 5 * 1024 * 1024); // 5MB limit
            $uploadResult = $uploader->upload($_FILES['image_file']);
            
            if ($uploadResult['success']) {
                // Delete old image if editing
                if ($action === 'edit' && !empty($project['image_url'])) {
                    $uploader->delete($project['image_url']);
                }
                $project['image_url'] = $uploadResult['path'];
            } else {
                $error = 'Image upload failed: ' . $uploadResult['error'];
            }
        }
        
        // Basic validation
        if (empty($project['title'])) {
            $error = 'Project title is required.';
        } else {
            try {
                if ($action === 'edit' && $project_id > 0) {
                    // Update existing project
                    $stmt = $db->prepare("UPDATE projects SET 
                        title = ?, 
                        description = ?, 
                        image_url = ?, 
                        tech_stack = ?, 
                        github_url = ?, 
                        demo_url = ?, 
                        is_featured = ?, 
                        status = ?, 
                        updated_at = NOW() 
                        WHERE id = ?");
                    
                    $stmt->execute([
                        $project['title'],
                        $project['description'],
                        $project['image_url'],
                        $project['tech_stack'],
                        $project['github_url'],
                        $project['demo_url'],
                        $project['is_featured'],
                        $project['status'],
                        $project_id
                    ]);
                    
                    $message = 'Project updated successfully.';
                } else {
                    // Add new project
                    $stmt = $db->prepare("INSERT INTO projects (
                        title, description, image_url, tech_stack, 
                        github_url, demo_url, is_featured, status, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    
                    $stmt->execute([
                        $project['title'],
                        $project['description'],
                        $project['image_url'],
                        $project['tech_stack'],
                        $project['github_url'],
                        $project['demo_url'],
                        $project['is_featured'],
                        $project['status']
                    ]);
                    
                    $project_id = $db->lastInsertId();
                    $message = 'Project created successfully.';
                }
                
                // Redirect to projects list after successful save
                if (empty($error)) {
                    header('Location: ?route=projects&message=' . urlencode($message));
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Error saving project: ' . $e->getMessage();
            }
        }
    }
}

// Parse tech stack for display
$techStack = [];
if (!empty($project['tech_stack'])) {
    $techStack = json_decode($project['tech_stack'], true);
    if (!is_array($techStack)) {
        $techStack = [];
    }
}
$techStackString = implode(', ', $techStack);

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
                <?php echo $action === 'edit' ? 'Edit Project' : 'Add New Project'; ?>
              </h4>
              <p class="card-subtitle card-subtitle-dash">
                <?php echo $action === 'edit' ? 'Update project details' : 'Create a new project'; ?>
              </p>
            </div>
            <div>
              <a href="?route=projects" class="btn btn-secondary text-white mb-0 me-0">
                <i class="mdi mdi-arrow-left"></i> Back to Projects
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
          
          <form class="forms-sample" method="post" action="?route=project-editor&action=<?php echo $action; ?><?php echo $project_id ? '&id=' . $project_id : ''; ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="row mb-4">
              <div class="col-md-8">
                <div class="card">
                  <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="mdi mdi-laptop"></i> Project Details</h5>
                  </div>
                  <div class="card-body">
                    <div class="form-group mb-3">
                      <label for="title" class="form-label">Project Title <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="title" name="title" 
                             value="<?php echo htmlspecialchars($project['title']); ?>" required>
                    </div>
                    
                    <div class="form-group mb-3">
                      <label for="description" class="form-label">Description</label>
                      <textarea class="form-control" id="description" name="description" rows="6"><?php echo htmlspecialchars($project['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                      <label for="tech_stack" class="form-label">Technologies Used</label>
                      <input type="text" class="form-control" id="tech_stack" name="tech_stack" 
                             value="<?php echo htmlspecialchars($techStackString); ?>" 
                             placeholder="React, Node.js, MongoDB, etc. (comma-separated)">
                      <small class="form-text text-muted">Enter technologies separated by commas</small>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="card mb-4">
                  <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="mdi mdi-link"></i> Links & Media</h5>
                  </div>
                  <div class="card-body">
                    <div class="form-group mb-3">
                      <label for="image_file" class="form-label">Project Image</label>
                      <input type="file" class="form-control" id="image_file" name="image_file" accept="image/*" onchange="previewImage(this, 'image_preview')">
                      <small class="form-text text-muted">JPG, PNG, WebP or GIF (Max 5MB)</small>
                      <?php if (!empty($project['image_url'])): ?>
                        <div class="mt-2">
                          <label class="form-text text-muted d-block">Current Image:</label>
                          <img id="image_preview" src="<?php echo htmlspecialchars($project['image_url']); ?>" alt="Project image" style="max-width: 150px; max-height: 150px;" class="img-thumbnail">
                        </div>
                      <?php else: ?>
                        <div class="mt-2">
                          <img id="image_preview" style="display: none; max-width: 150px; max-height: 150px;" class="img-thumbnail">
                        </div>
                      <?php endif; ?>
                    </div>
                    
                    <div class="form-group mb-3">
                      <label for="github_url" class="form-label">GitHub URL</label>
                      <input type="url" class="form-control" id="github_url" name="github_url" 
                             value="<?php echo htmlspecialchars($project['github_url']); ?>">
                      <small class="form-text text-muted">Link to the GitHub repository</small>
                    </div>
                    
                    <div class="form-group mb-3">
                      <label for="demo_url" class="form-label">Demo URL</label>
                      <input type="url" class="form-control" id="demo_url" name="demo_url" 
                             value="<?php echo htmlspecialchars($project['demo_url']); ?>">
                      <small class="form-text text-muted">Link to a live demo of the project</small>
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
                        <option value="active" <?php echo $project['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="completed" <?php echo $project['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="in-progress" <?php echo $project['status'] === 'in-progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="archived" <?php echo $project['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                      </select>
                    </div>
                    
                    <div class="form-check form-check-flat form-check-primary mb-3">
                      <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="is_featured" 
                               <?php echo $project['is_featured'] ? 'checked' : ''; ?>>
                        Featured Project
                        <i class="input-helper"></i>
                      </label>
                      <small class="form-text text-muted d-block">Featured projects appear on the homepage</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <button type="submit" name="save_project" class="btn btn-primary me-2">
              <?php echo $action === 'edit' ? 'Update Project' : 'Create Project'; ?>
            </button>
            <a href="?route=projects" class="btn btn-light">Cancel</a>
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
  // Initialize rich text editor for description
  if (typeof tinymce !== 'undefined') {
    tinymce.init({
      selector: '#description',
      height: 300,
      menubar: false,
      plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table paste code help wordcount'
      ],
      toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
    });
  }
  
  // Initialize tag input for tech stack
  if ($.fn.tagsinput) {
    $('#tech_stack').tagsinput({
      trimValue: true,
      confirmKeys: [13, 44], // Enter and comma
      tagClass: 'badge bg-primary'
    });
  }
});
</script>
