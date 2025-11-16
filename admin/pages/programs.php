<?php
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Programs - KHODERS WORLD Admin');
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

$currentPage = 'programs';
$action = $_GET['action'] ?? 'list';
$message = $_GET['message'] ?? '';
$error = '';
$courses = [];

$user = Auth::user();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        $error = 'Unable to connect to the database.';
    }
} catch (Exception $e) {
    $error = 'Database connection error: ' . $e->getMessage();
    $db = null;
}

if ($db && $action === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM programs WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = 'Course deleted successfully.';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Error deleting course: ' . $e->getMessage();
    }
}

$courses = [];
if ($db) {
    try {
        $stmt = $db->query("SELECT * FROM programs ORDER BY created_at DESC");
        $courses = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (PDOException $e) {
        $error = 'Error fetching courses: ' . $e->getMessage();
    }
}

$tableExists = $db ? admin_table_exists($db, 'programs') : false;
if ($db && !$tableExists) {
    try {
        $db->exec("CREATE TABLE courses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            description LONGTEXT,
            duration VARCHAR(50),
            level VARCHAR(50),
            instructor VARCHAR(100),
            image_url VARCHAR(255),
            price DECIMAL(10,2),
            status VARCHAR(20) DEFAULT 'active',
            created_at DATETIME,
            updated_at DATETIME
        )");
        $message = 'Courses table created successfully.';
    } catch (PDOException $e) {
        $error = 'Error creating table: ' . $e->getMessage();
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
              <h4 class="card-title card-title-dash">Programs</h4>
              <p class="card-subtitle card-subtitle-dash">Manage detailed programs</p>
            </div>
            <a href="?route=program-editor" class="btn btn-primary me-1">
              <i class="mdi mdi-plus"></i> Add New Program
            </a>
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
              <i class="mdi mdi-database-alert"></i> Courses table doesn't exist yet.
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Program Title</th>
                    <th>Level</th>
                    <th>Instructor</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($courses)): ?>
                    <tr>
                      <td colspan="6" class="text-center py-4 text-muted">
                        <i class="mdi mdi-book-open mdi-48px d-block mb-2"></i>
                        No programs found
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($courses as $course): ?>
                      <tr>
                        <td>
                          <h6 class="mb-0"><?php echo htmlspecialchars($course['title']); ?></h6>
                          <p class="text-muted mb-0"><?php echo admin_excerpt($course['description'] ?? '', 60); ?></p>
                        </td>
                        <td><?php echo htmlspecialchars($course['level'] ?? 'Beginner'); ?></td>
                        <td><?php echo htmlspecialchars($course['instructor_name'] ?? 'TBA'); ?></td>
                        <td><?php echo htmlspecialchars($course['duration'] ?? 'N/A'); ?></td>
                        <td>
                          <span class="badge bg-<?php echo $course['status'] === 'active' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($course['status']); ?>
                          </span>
                        </td>
                        <td>
                          <div class="d-flex gap-1">
                            <a href="?route=program-editor&action=edit&id=<?php echo (int)$course['id']; ?>" class="btn btn-outline-primary btn-sm" title="Edit program">
                              <i class="mdi mdi-pencil"></i>
                            </a>
                            <a href="?route=programs&action=delete&id=<?php echo (int)$course['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this program?');">
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
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
