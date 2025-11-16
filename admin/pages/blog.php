<?php
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Blog Posts - KHODERS WORLD Admin');
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

$currentPage = 'blog';
$action = $_GET['action'] ?? 'list';
$message = $_GET['message'] ?? '';
$error = '';
$posts = [];

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
        $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = 'Blog post deleted successfully.';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Error deleting post: ' . $e->getMessage();
    }
}

$posts = [];
if ($db) {
    try {
        $stmt = $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
        $posts = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (PDOException $e) {
        $error = 'Error fetching posts: ' . $e->getMessage();
    }
}

$tableExists = $db ? admin_table_exists($db, 'blog_posts') : false;
if ($db && !$tableExists) {
    try {
        $db->exec("CREATE TABLE blog_posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            content LONGTEXT,
            excerpt VARCHAR(500),
            featured_image VARCHAR(255),
            author VARCHAR(100),
            status VARCHAR(20) DEFAULT 'draft',
            created_at DATETIME,
            updated_at DATETIME
        )");
        $message = 'Blog posts table created successfully.';
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
              <h4 class="card-title card-title-dash">Blog Posts</h4>
              <p class="card-subtitle card-subtitle-dash">Manage blog posts</p>
            </div>
            <div>
              <a href="?route=blog-editor" class="btn btn-primary btn-lg text-white mb-0 me-0">
                <i class="mdi mdi-plus"></i> Add New Post
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
              <i class="mdi mdi-database-alert"></i> Blog posts table doesn't exist yet.
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($posts)): ?>
                    <tr>
                      <td colspan="5" class="text-center py-4 text-muted">
                        <i class="mdi mdi-file-document mdi-48px d-block mb-2"></i>
                        No blog posts found
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                      <tr>
                        <td>
                          <h6 class="mb-0"><?php echo htmlspecialchars($post['title']); ?></h6>
                          <p class="text-muted mb-0"><?php echo admin_excerpt($post['excerpt'] ?? '', 60); ?></p>
                        </td>
                        <td><?php echo htmlspecialchars($post['author'] ?? 'Unknown'); ?></td>
                        <td>
                          <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($post['status']); ?>
                          </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                        <td>
                          <div class="d-flex">
                            <a href="?route=blog-editor&action=edit&id=<?php echo (int)$post['id']; ?>" class="btn btn-outline-primary btn-sm me-2">
                              <i class="mdi mdi-pencil"></i>
                            </a>
                            <a href="?route=blog&action=delete&id=<?php echo (int)$post['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this post?');">
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
