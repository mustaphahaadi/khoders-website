<?php
/**
 * KHODERS WORLD Admin Course Editor
 * Add or edit courses/programs
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Course Editor - KHODERS WORLD Admin');
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

// Initialize variables
$currentPage = 'courses';
$action = $_GET['action'] ?? 'add';
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';
$course = [
    'title' => '',
    'description' => '',
    'duration' => '',
    'level' => 'Beginner',
    'instructor' => '',
    'image_url' => '',
    'price' => 0,
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

// Check if editing existing course
if ($db && $action === 'edit' && $course_id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $existingCourse = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingCourse) {
            $course = $existingCourse;
        } else {
            $error = 'Course not found.';
        }
    } catch (PDOException $e) {
        $error = 'Error loading course: ' . $e->getMessage();
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_course'])) {
    // Validate CSRF token
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } elseif (!$db) {
        $error = 'Database connection is not available. Cannot save course.';
    } else {
        // Get form data
        $course['title'] = $_POST['title'] ?? '';
        $course['description'] = $_POST['description'] ?? '';
        $course['duration'] = $_POST['duration'] ?? '';
        $course['level'] = $_POST['level'] ?? 'Beginner';
        $course['instructor'] = $_POST['instructor'] ?? '';
        $course['image_url'] = $_POST['image_url'] ?? '';
        $course['price'] = floatval($_POST['price'] ?? 0);
        $course['status'] = $_POST['status'] ?? 'active';
        
        // Basic validation
        if (empty($course['title'])) {
            $error = 'Course title is required.';
        } else {
            try {
                if ($action === 'edit' && $course_id > 0) {
                    // Update existing course
                    $stmt = $db->prepare("UPDATE courses SET title = ?, description = ?, duration = ?, level = ?, instructor = ?, image_url = ?, price = ?, status = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([
                        $course['title'],
                        $course['description'],
                        $course['duration'],
                        $course['level'],
                        $course['instructor'],
                        $course['image_url'],
                        $course['price'],
                        $course['status'],
                        $course_id
                    ]);
                    $message = 'Course updated successfully.';
                } else {
                    // Insert new course
                    $stmt = $db->prepare("INSERT INTO courses (title, description, duration, level, instructor, image_url, price, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    $stmt->execute([
                        $course['title'],
                        $course['description'],
                        $course['duration'],
                        $course['level'],
                        $course['instructor'],
                        $course['image_url'],
                        $course['price'],
                        $course['status']
                    ]);
                    $message = 'Course created successfully.';
                    $action = 'add';
                    // Reset form
                    $course = [
                        'title' => '',
                        'description' => '',
                        'duration' => '',
                        'level' => 'Beginner',
                        'instructor' => '',
                        'image_url' => '',
                        'price' => 0,
                        'status' => 'active'
                    ];
                }
            } catch (PDOException $e) {
                $error = 'Error saving course: ' . $e->getMessage();
            }
        }
    }
}

// Generate CSRF token
$csrfToken = Security::generateCSRFToken();

// Determine page title based on action
$pageTitle = ($action === 'edit') ? 'Edit Course' : 'Add New Course';
?>

<div class="content-wrapper">
  <div class="row">
    <div class="col-lg-8 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title"><?php echo $pageTitle; ?></h4>
          <p class="card-subtitle">Create or edit a course/program</p>
          
          <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="mdi mdi-check-circle-outline"></i> <?php echo htmlspecialchars($message); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          
          <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="mdi mdi-alert-circle"></i> <?php echo htmlspecialchars($error); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          
          <form method="POST" class="forms-sample">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <input type="hidden" name="save_course" value="1">
            
            <div class="mb-3">
              <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="title" name="title" 
                     value="<?php echo htmlspecialchars($course['title']); ?>" 
                     placeholder="Enter course title" required>
            </div>
            
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description" name="description" 
                        rows="4" placeholder="Enter course description"><?php echo htmlspecialchars($course['description']); ?></textarea>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="duration" class="form-label">Duration</label>
                  <input type="text" class="form-control" id="duration" name="duration" 
                         value="<?php echo htmlspecialchars($course['duration']); ?>" 
                         placeholder="e.g., 4 weeks">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="level" class="form-label">Level</label>
                  <select class="form-select" id="level" name="level">
                    <option value="Beginner" <?php echo $course['level'] === 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                    <option value="Intermediate" <?php echo $course['level'] === 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                    <option value="Advanced" <?php echo $course['level'] === 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="instructor" class="form-label">Instructor</label>
                  <input type="text" class="form-control" id="instructor" name="instructor" 
                         value="<?php echo htmlspecialchars($course['instructor']); ?>" 
                         placeholder="Instructor name">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="price" class="form-label">Price</label>
                  <input type="number" class="form-control" id="price" name="price" 
                         value="<?php echo htmlspecialchars($course['price']); ?>" 
                         placeholder="0.00" step="0.01" min="0">
                </div>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="image_url" class="form-label">Image URL</label>
              <input type="url" class="form-control" id="image_url" name="image_url" 
                     value="<?php echo htmlspecialchars($course['image_url']); ?>" 
                     placeholder="https://example.com/image.jpg">
            </div>
            
            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" id="status" name="status">
                <option value="active" <?php echo $course['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $course['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                <option value="draft" <?php echo $course['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
              </select>
            </div>
            
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="mdi mdi-content-save"></i> <?php echo ($action === 'edit') ? 'Update' : 'Create'; ?> Course
              </button>
              <a href="?route=courses" class="btn btn-outline-secondary">
                <i class="mdi mdi-arrow-left"></i> Back to Courses
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
