<?php
/**
 * KHODERS WORLD Admin Members Page
 * Displayed when accessing the members route
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Members - KHODERS WORLD Admin');
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

// Initialize variables
$currentPage = 'members';
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';
$members = [];

// Get current user
$user = Auth::user();

// Database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    $error = 'Unable to connect to the database. Please verify database credentials and try again.';
}

// Handle delete action
if ($db) {
    if ($action === 'delete' && isset($_GET['id'])) {
        try {
            $stmt = $db->prepare("DELETE FROM members WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $message = 'Member deleted successfully';
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Failed to delete member';
        }
    }

    // Handle edit action
    if ($action === 'edit' && isset($_GET['id'])) {
        // Update existing member with enhanced fields
        $stmt = $db->prepare("UPDATE members SET first_name = ?, last_name = ?, 
                              email = ?, phone = ?, student_id = ?, program = ?, 
                              year = ?, experience = ?, interests = ?, 
                              additional_info = ?, updated_at = NOW() 
                              WHERE id = ?");
        if ($stmt->execute([$first_name, $last_name, $email, $phone, $student_id, 
                           $program, $year, $experience, $interests, 
                           $additional_info, $_GET['id']])) {
            $message = 'Member updated successfully';
            $action = 'list'; // Return to list view
        } else {
            $error = 'Failed to update member';
        }
    }

    // Get all members with enhanced fields from our updated schema
    try {
        // Query with our enhanced members table structure
        $stmt = $db->prepare("SELECT id, first_name, last_name, email, phone, student_id, 
                            program, year, experience, interests, registration_date, ip_address 
                            FROM members ORDER BY registration_date DESC");
        $stmt->execute();
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = 'Failed to fetch members';
    }
}
?>

<div class="content-wrapper">
  <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-sm-flex justify-content-between align-items-start mb-4">
            <div>
              <h4 class="card-title card-title-dash">Members Management</h4>
              <p class="card-subtitle card-subtitle-dash">View and manage all KHODERS WORLD members</p>
            </div>
            <div>
              <a href="?route=members&action=export" class="btn btn-primary btn-lg text-white mb-0 me-0"><i class="mdi mdi-account-plus"></i>Export Members</a>
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
          
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Program / Year</th>
                  <th>Experience</th>
                  <th>Joined</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($members)): ?>
                  <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                      <i class="mdi mdi-account-multiple-outline mdi-48px d-block mb-2"></i>
                      No members found
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($members as $member): ?>
                    <tr>
                      <td><?php echo admin_safe($member['id']); ?></td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="rounded-circle profile-image-small bg-primary d-flex align-items-center justify-content-center text-white">
                            <?php echo strtoupper(substr(($member['first_name'] ?? 'U'), 0, 1)); ?>
                          </div>
                          <div class="ms-3">
                            <p class="mb-0 fw-bold"><?php echo admin_safe(trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''))); ?></p>
                          </div>
                        </div>
                      </td>
                      <td><?php echo admin_safe($member['email'] ?? ''); ?></td>
                      <td><?php echo admin_safe($member['phone'] ?? ''); ?></td>
                      <td><?php echo admin_safe(($member['program'] ?? '') . ' / ' . ($member['year'] ?? '')); ?></td>
                      <td>
                        <div class="badge bg-<?php 
                          $exp = strtolower($member['experience'] ?? ''); 
                          if ($exp == 'beginner') echo 'info'; 
                          elseif ($exp == 'some-experience') echo 'success'; 
                          elseif ($exp == 'intermediate') echo 'warning'; 
                          elseif ($exp == 'advanced') echo 'primary'; 
                          else echo 'secondary'; 
                        ?>">
                          <?php echo admin_safe(ucfirst(str_replace('-', ' ', strtolower($member['experience'] ?? '')))); ?>
                        </div>
                      </td>
                      <td><?php echo admin_format_date($member['registration_date'] ?? null); ?></td>
                      <td>
                        <div class="d-flex">
                          <a href="?route=members&action=view&id=<?php echo admin_safe($member['id']); ?>" class="btn btn-outline-primary btn-sm me-2">
                            <i class="mdi mdi-eye"></i>
                          </a>
                          <a href="?route=members&action=delete&id=<?php echo admin_safe($member['id']); ?>" 
                            class="btn btn-outline-danger btn-sm"
                            onclick="return confirm('Are you sure you want to delete this member?')">
                            <i class="mdi mdi-delete"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('.table').DataTable({
    "aLengthMenu": [
      [10, 30, 50, -1],
      [10, 30, 50, "All"]
    ],
    "iDisplayLength": 10,
    "language": {
      search: ""
    }
  });
  $('.dataTables_filter input').attr("placeholder", "Search members...");
});
</script>
