<?php
/**
 * KHODERS WORLD Admin Event Editor
 * Add or edit events
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Event Editor - KHODERS WORLD Admin');
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../config/file-upload.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

// Initialize variables
$currentPage = 'events';
$action = $_GET['action'] ?? 'add';
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';
$event = [
    'title' => '',
    'description' => '',
    'event_date' => '',
    'event_time' => '',
    'location' => '',
    'image_url' => '',
    'registration_url' => '',
    'is_featured' => 0,
    'status' => 'upcoming'
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

// Check if editing existing event
if ($db && $action === 'edit' && $event_id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $existingEvent = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingEvent) {
            $event = $existingEvent;
            
            // Split datetime into date and time for form fields
            if (!empty($event['event_date'])) {
                $datetime = new DateTime($event['event_date']);
                $event['event_date'] = $datetime->format('Y-m-d');
                $event['event_time'] = $datetime->format('H:i');
            }
        } else {
            $error = 'Event not found.';
        }
    } catch (PDOException $e) {
        $error = 'Error loading event: ' . $e->getMessage();
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_event'])) {
    // Validate CSRF token
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } elseif (!$db) {
        $error = 'Database connection is not available. Cannot save event.';
    } else {
        // Get form data
        $event['title'] = $_POST['title'] ?? '';
        $event['description'] = $_POST['description'] ?? '';
        $event['event_date'] = $_POST['event_date'] ?? '';
        $event['event_time'] = $_POST['event_time'] ?? '';
        $event['location'] = $_POST['location'] ?? '';
        $event['image_url'] = $_POST['image_url'] ?? '';
        $event['registration_url'] = $_POST['registration_url'] ?? '';
        $event['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
        $event['status'] = $_POST['status'] ?? 'upcoming';
        
        // Handle image upload if file is provided
        if (!empty($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $uploader = new FileUploader('events', 5 * 1024 * 1024); // 5MB limit
            $uploadResult = $uploader->upload($_FILES['image_file']);
            
            if ($uploadResult['success']) {
                // Delete old image if editing
                if ($action === 'edit' && !empty($event['image_url'])) {
                    $uploader->delete($event['image_url']);
                }
                $event['image_url'] = $uploadResult['path'];
            } else {
                $error = 'Image upload failed: ' . $uploadResult['error'];
            }
        }
        
        // Basic validation
        if (empty($event['title'])) {
            $error = 'Event title is required.';
        } elseif (empty($event['event_date'])) {
            $error = 'Event date is required.';
        } else {
            try {
                // Combine date and time
                $eventDateTime = $event['event_date'];
                if (!empty($event['event_time'])) {
                    $eventDateTime .= ' ' . $event['event_time'] . ':00';
                } else {
                    $eventDateTime .= ' 00:00:00';
                }
                
                // Check which columns exist
                $hasEventDate = admin_table_has_column($db, 'events', 'event_date');
                $hasDateAndTime = admin_table_has_column($db, 'events', 'date') && admin_table_has_column($db, 'events', 'time');
                
                if ($action === 'edit' && $event_id > 0) {
                    // Update existing event
                    if ($hasEventDate) {
                        $stmt = $db->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, location = ?, image_url = ?, registration_url = ?, is_featured = ?, status = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->execute([$event['title'], $event['description'], $eventDateTime, $event['location'], $event['image_url'], $event['registration_url'], $event['is_featured'], $event['status'], $event_id]);
                    } elseif ($hasDateAndTime) {
                        $stmt = $db->prepare("UPDATE events SET title = ?, description = ?, date = ?, time = ?, location = ?, image_url = ?, registration_url = ?, is_featured = ?, status = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->execute([$event['title'], $event['description'], $event['event_date'], $event['event_time'], $event['location'], $event['image_url'], $event['registration_url'], $event['is_featured'], $event['status'], $event_id]);
                    } else {
                        $stmt = $db->prepare("UPDATE events SET title = ?, description = ?, location = ?, image_url = ?, registration_url = ?, is_featured = ?, status = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->execute([$event['title'], $event['description'], $event['location'], $event['image_url'], $event['registration_url'], $event['is_featured'], $event['status'], $event_id]);
                    }
                    $message = 'Event updated successfully.';
                } else {
                    // Add new event
                    if ($hasEventDate) {
                        $stmt = $db->prepare("INSERT INTO events (title, description, event_date, location, image_url, registration_url, is_featured, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                        $stmt->execute([$event['title'], $event['description'], $eventDateTime, $event['location'], $event['image_url'], $event['registration_url'], $event['is_featured'], $event['status']]);
                    } elseif ($hasDateAndTime) {
                        $stmt = $db->prepare("INSERT INTO events (title, description, date, time, location, image_url, registration_url, is_featured, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                        $stmt->execute([$event['title'], $event['description'], $event['event_date'], $event['event_time'], $event['location'], $event['image_url'], $event['registration_url'], $event['is_featured'], $event['status']]);
                    } else {
                        $stmt = $db->prepare("INSERT INTO events (title, description, location, image_url, registration_url, is_featured, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                        $stmt->execute([$event['title'], $event['description'], $event['location'], $event['image_url'], $event['registration_url'], $event['is_featured'], $event['status']]);
                    }
                    $event_id = $db->lastInsertId();
                    $message = 'Event created successfully.';
                }
                
                // Redirect to events list after successful save
                if (empty($error)) {
                    header('Location: ?route=events&message=' . urlencode($message));
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Error saving event: ' . $e->getMessage();
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
                <?php echo $action === 'edit' ? 'Edit Event' : 'Add New Event'; ?>
              </h4>
              <p class="card-subtitle card-subtitle-dash">
                <?php echo $action === 'edit' ? 'Update event details' : 'Create a new event'; ?>
              </p>
            </div>
            <div>
              <a href="?route=events" class="btn btn-secondary text-white mb-0 me-0">
                <i class="mdi mdi-arrow-left"></i> Back to Events
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
          
          <form class="forms-sample" method="post" action="?route=event-editor&action=<?php echo $action; ?><?php echo $event_id ? '&id=' . $event_id : ''; ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="row mb-4">
              <div class="col-md-8">
                <div class="card">
                  <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="mdi mdi-calendar-text"></i> Event Details</h5>
                  </div>
                  <div class="card-body">
                    <div class="form-group mb-3">
                      <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="title" name="title" 
                             value="<?php echo htmlspecialchars($event['title']); ?>" required>
                    </div>
                    
                    <div class="form-group mb-3">
                      <label for="description" class="form-label">Description</label>
                      <textarea class="form-control" id="description" name="description" rows="6"><?php echo htmlspecialchars($event['description']); ?></textarea>
                    </div>
                    
                    <div class="row mb-3">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="event_date" class="form-label">Event Date <span class="text-danger">*</span></label>
                          <input type="date" class="form-control" id="event_date" name="event_date" 
                                 value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="event_time" class="form-label">Event Time</label>
                          <input type="time" class="form-control" id="event_time" name="event_time" 
                                 value="<?php echo htmlspecialchars($event['event_time'] ?? ''); ?>">
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group mb-3">
                      <label for="location" class="form-label">Location</label>
                      <input type="text" class="form-control" id="location" name="location" 
                             value="<?php echo htmlspecialchars($event['location']); ?>">
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="card mb-4">
                  <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="mdi mdi-image"></i> Media & Links</h5>
                  </div>
                  <div class="card-body">
                    <div class="form-group mb-3">
                      <label for="image_file" class="form-label">Event Image</label>
                      <input type="file" class="form-control" id="image_file" name="image_file" 
                             accept="image/jpeg,image/png,image/webp,image/gif"
                             onchange="previewImage(this, 'image_preview')">
                      <small class="form-text text-muted">Supported formats: JPEG, PNG, WebP, GIF (Max 5MB)</small>
                      
                      <div id="image_preview" class="mt-2">
                        <?php if (!empty($event['image_url'])): ?>
                          <?php 
                          $imgPath = $event['image_url'];
                          if (strpos($imgPath, '/public/') === 0) {
                              $imgPath = '..' . $imgPath;
                          }
                          ?>
                          <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Event image" style="max-width: 100%; max-height: 200px;">
                          <p><small class="text-muted">Current image</small></p>
                        <?php endif; ?>
                      </div>
                    </div>
                    
                    <div class="form-group mb-3">
                      <label for="registration_url" class="form-label">Registration URL</label>
                      <input type="url" class="form-control" id="registration_url" name="registration_url" 
                             value="<?php echo htmlspecialchars($event['registration_url']); ?>">
                      <small class="form-text text-muted">URL for event registration</small>
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
                        <option value="upcoming" <?php echo $event['status'] === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                        <option value="ongoing" <?php echo $event['status'] === 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                        <option value="completed" <?php echo $event['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $event['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                      </select>
                    </div>
                    
                    <div class="form-check form-check-flat form-check-primary mb-3">
                      <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="is_featured" 
                               <?php echo $event['is_featured'] ? 'checked' : ''; ?>>
                        Featured Event
                        <i class="input-helper"></i>
                      </label>
                      <small class="form-text text-muted d-block">Featured events appear on the homepage</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <button type="submit" name="save_event" class="btn btn-primary me-2">
              <?php echo $action === 'edit' ? 'Update Event' : 'Create Event'; ?>
            </button>
            <a href="?route=events" class="btn btn-light">Cancel</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- TinyMCE WYSIWYG Editor -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 100%; max-height: 200px;" alt="Preview">';
            }
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
});
</script>
