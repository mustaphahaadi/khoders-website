<?php
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Enrollments - KHODERS WORLD Admin');
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

$currentPage = 'enrollments';
$message = $_GET['message'] ?? '';
$error = '';
$enrollments = [];

$user = Auth::user();

try {
    $database = new Database();
    $db = $database->getConnection();
    if (!$db) $error = 'Unable to connect to the database.';
} catch (Exception $e) {
    $error = 'Database connection error: ' . $e->getMessage();
    $db = null;
}

if ($db && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM enrollments WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = 'Enrollment deleted successfully.';
    } catch (PDOException $e) {
        $error = 'Error deleting enrollment: ' . $e->getMessage();
    }
}

if ($db) {
    try {
        $stmt = $db->query("SELECT * FROM enrollments ORDER BY created_at DESC");
        $enrollments = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (PDOException $e) {
        $error = 'Error fetching enrollments: ' . $e->getMessage();
    }
}

$csrfToken = Security::generateCSRFToken();
?>

<div class="content-wrapper">
  <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-sm-flex justify-content-between align-items-start mb-4">
            <div>
              <h4 class="card-title card-title-dash">Enrollments</h4>
              <p class="card-subtitle card-subtitle-dash">Manage course, program, event, and project enrollments</p>
            </div>
          </div>
          
          <?php if ($message): ?>
            <div class="alert alert-success"><i class="mdi mdi-check-circle-outline"></i> <?php echo htmlspecialchars($message); ?></div>
          <?php endif; ?>
          <?php if ($error): ?>
            <div class="alert alert-danger"><i class="mdi mdi-alert-circle"></i> <?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>
          
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Type</th>
                  <th>Item</th>
                  <th>Level</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($enrollments)): ?>
                  <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                      <i class="mdi mdi-account-multiple mdi-48px d-block mb-2"></i>
                      No enrollments found
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($enrollments as $enrollment): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']); ?></td>
                      <td><?php echo htmlspecialchars($enrollment['email']); ?></td>
                      <td><span class="badge bg-primary"><?php echo ucfirst($enrollment['enrollment_type']); ?></span></td>
                      <td><?php echo htmlspecialchars($enrollment['item_title']); ?></td>
                      <td><?php echo ucfirst($enrollment['experience_level'] ?? 'N/A'); ?></td>
                      <td>
                        <span class="badge bg-<?php echo $enrollment['status'] === 'approved' ? 'success' : ($enrollment['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                          <?php echo ucfirst($enrollment['status']); ?>
                        </span>
                      </td>
                      <td><?php echo date('M d, Y', strtotime($enrollment['created_at'])); ?></td>
                      <td>
                        <div class="d-flex gap-1">
                          <button class="btn btn-outline-info btn-sm" onclick="viewEnrollment(<?php echo $enrollment['id']; ?>)" title="View details">
                            <i class="mdi mdi-eye"></i>
                          </button>
                          <a href="?route=enrollments&action=delete&id=<?php echo $enrollment['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this enrollment?');">
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
function viewEnrollment(id) {
  alert('View enrollment details - ID: ' + id + '\nFull details view coming soon!');
}
</script>
