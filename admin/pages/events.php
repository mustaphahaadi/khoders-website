<?php
/**
 * KHODERS WORLD Admin Events Page
 * Manage events listing
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Events - KHODERS WORLD Admin');
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

// Initialize variables
$currentPage = 'events';
$action = $_GET['action'] ?? 'list';
$message = $_GET['message'] ?? '';
$error = '';
$events = [];
$statusFilter = $_GET['status'] ?? 'all';

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

// Handle delete action
if ($db && $action === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = 'Event deleted successfully.';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Error deleting event: ' . $e->getMessage();
    }
}

// Fetch events with optional status filter
$events = [];
if ($db) {
    try {
        $sql = "SELECT * FROM events";
        $params = [];
        
        if ($statusFilter !== 'all') {
            $sql .= " WHERE status = ?";
            $params[] = $statusFilter;
        }
        
        $sql .= " ORDER BY event_date DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Error fetching events: ' . $e->getMessage();
    }
}

// Create table if it doesn't exist
$tableExists = $db ? admin_table_exists($db, 'events') : false;
if ($db && !$tableExists) {
    try {
        $db->exec("CREATE TABLE events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            description TEXT,
            event_date DATETIME,
            location VARCHAR(100),
            image_url VARCHAR(255),
            registration_url VARCHAR(255),
            is_featured TINYINT(1) DEFAULT 0,
            status VARCHAR(20) DEFAULT 'upcoming',
            created_at DATETIME,
            updated_at DATETIME
        )");
        $message = 'Events table created successfully.';
    } catch (PDOException $e) {
        $error = 'Error creating events table: ' . $e->getMessage();
    }
}

// Count events by status
$statusCounts = [
    'all' => 0,
    'upcoming' => 0,
    'ongoing' => 0,
    'completed' => 0,
    'cancelled' => 0
];

foreach ($events as $event) {
    $statusCounts['all']++;
    $status = $event['status'] ?? 'upcoming';
    if (isset($statusCounts[$status])) {
        $statusCounts[$status]++;
    }
}

// Generate CSRF token
$csrfToken = Security::generateCSRFToken();
?>

<div class="content-wrapper">
  <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-sm-flex justify-content-between align-items-start mb-4">
            <div>
              <h4 class="card-title card-title-dash">Events Management</h4>
              <p class="card-subtitle card-subtitle-dash">Manage KHODERS WORLD events</p>
            </div>
            <div>
              <a href="?route=event-editor" class="btn btn-primary btn-lg text-white mb-0 me-0">
                <i class="mdi mdi-plus"></i> Add New Event
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
          
          <?php if (!$tableExists): ?>
            <div class="alert alert-warning" role="alert">
              <i class="mdi mdi-database-alert"></i> The events table doesn't exist yet. Click the button below to create it.
              <form method="post" action="?route=events" class="mt-3">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <button type="submit" name="create_table" class="btn btn-warning">Create Events Table</button>
              </form>
            </div>
          <?php else: ?>
            <!-- Status filter tabs -->
            <ul class="nav nav-tabs mb-4">
              <li class="nav-item">
                <a class="nav-link <?php echo $statusFilter === 'all' ? 'active' : ''; ?>" href="?route=events&status=all">
                  All <span class="badge bg-secondary"><?php echo $statusCounts['all']; ?></span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $statusFilter === 'upcoming' ? 'active' : ''; ?>" href="?route=events&status=upcoming">
                  Upcoming <span class="badge bg-primary"><?php echo $statusCounts['upcoming']; ?></span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $statusFilter === 'ongoing' ? 'active' : ''; ?>" href="?route=events&status=ongoing">
                  Ongoing <span class="badge bg-success"><?php echo $statusCounts['ongoing']; ?></span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $statusFilter === 'completed' ? 'active' : ''; ?>" href="?route=events&status=completed">
                  Completed <span class="badge bg-info"><?php echo $statusCounts['completed']; ?></span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $statusFilter === 'cancelled' ? 'active' : ''; ?>" href="?route=events&status=cancelled">
                  Cancelled <span class="badge bg-danger"><?php echo $statusCounts['cancelled']; ?></span>
                </a>
              </li>
            </ul>
            
            <?php if (empty($events)): ?>
              <div class="text-center py-5 bg-light rounded">
                <i class="mdi mdi-calendar-text mdi-48px text-muted"></i>
                <p class="mt-3 mb-0 text-muted">No events found.</p>
                <?php if ($statusFilter !== 'all'): ?>
                  <p class="text-muted">Try changing the filter or <a href="?route=event-editor" class="text-primary">add a new event</a>.</p>
                <?php else: ?>
                  <p class="text-muted">Get started by <a href="?route=event-editor" class="text-primary">adding your first event</a>.</p>
                <?php endif; ?>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Event</th>
                      <th>Date</th>
                      <th>Location</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($events as $event): ?>
                      <tr>
                        <td>
                          <div class="d-flex align-items-center">
                            <?php if (!empty($event['image_url'])): ?>
                              <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="Event Image" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                            <?php else: ?>
                              <div class="rounded bg-primary d-flex align-items-center justify-content-center text-white me-3" style="width: 60px; height: 60px;">
                                <i class="mdi mdi-calendar-text mdi-24px"></i>
                              </div>
                            <?php endif; ?>
                            
                            <div>
                              <h6 class="mb-0"><?php echo htmlspecialchars($event['title']); ?></h6>
                              <p class="text-muted mb-0"><?php echo admin_excerpt($event['description'] ?? '', 60); ?></p>
                              <?php if ($event['is_featured']): ?>
                                <span class="badge bg-primary">Featured</span>
                              <?php endif; ?>
                            </div>
                          </div>
                        </td>
                        <td>
                          <?php if (!empty($event['event_date'])): ?>
                            <div class="d-flex flex-column">
                              <span><?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                              <small class="text-muted"><?php echo date('h:i A', strtotime($event['event_date'])); ?></small>
                            </div>
                          <?php else: ?>
                            <span class="text-muted">Not set</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php echo !empty($event['location']) ? htmlspecialchars($event['location']) : '<span class="text-muted">Not specified</span>'; ?>
                        </td>
                        <td>
                          <?php 
                            $statusClass = 'secondary';
                            switch ($event['status']) {
                                case 'upcoming': $statusClass = 'primary'; break;
                                case 'ongoing': $statusClass = 'success'; break;
                                case 'completed': $statusClass = 'info'; break;
                                case 'cancelled': $statusClass = 'danger'; break;
                            }
                          ?>
                          <span class="badge bg-<?php echo $statusClass; ?>"><?php echo ucfirst($event['status']); ?></span>
                        </td>
                        <td>
                          <div class="d-flex">
                            <a href="?route=event-editor&action=edit&id=<?php echo (int)$event['id']; ?>" class="btn btn-outline-primary btn-sm me-2" title="Edit">
                              <i class="mdi mdi-pencil"></i>
                            </a>
                            <a href="?route=events&action=delete&id=<?php echo (int)$event['id']; ?>" 
                               class="btn btn-outline-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this event?');" title="Delete">
                              <i class="mdi mdi-delete"></i>
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Initialize DataTable
  if ($.fn.DataTable) {
    $('.table').DataTable({
      "order": [[1, "asc"]],  // Sort by date
      "pageLength": 10,
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });
  }
});
</script>
