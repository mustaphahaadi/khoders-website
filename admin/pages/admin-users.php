<?php
if (!defined('PAGE_TITLE')) define('PAGE_TITLE', 'Admin Users - KHODERS WORLD Admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

$database = Database::getInstance();
$db = $database->getConnection();
$message = $_GET['message'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $db) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $username = Security::sanitizeInput($_POST['username'] ?? '');
        $email = Security::sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'editor';
        
        if ($username && $email && $password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt = $db->prepare("INSERT INTO admins (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hash, $role]);
                $message = 'Admin user created successfully';
            } catch (PDOException $e) {
                $error = 'Error creating user: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'delete' && isset($_POST['id'])) {
        try {
            $stmt = $db->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = 'Admin user deleted';
        } catch (PDOException $e) {
            $error = 'Error deleting user: ' . $e->getMessage();
        }
    }
}

$admins = [];
if ($db) {
    try {
        $stmt = $db->query("SELECT id, username, email, role, last_login, created_at FROM admins ORDER BY created_at DESC");
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Error fetching admins: ' . $e->getMessage();
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
              <h4 class="card-title">Admin Users</h4>
              <p class="card-subtitle">Manage administrator accounts</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
              <i class="mdi mdi-plus"></i> Add Admin
            </button>
          </div>
          
          <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
          <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
          
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Last Login</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($admins as $admin): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($admin['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($admin['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><span class="badge bg-<?php echo $admin['role'] === 'admin' ? 'primary' : 'secondary'; ?>"><?php echo htmlspecialchars(ucfirst($admin['role']), ENT_QUOTES, 'UTF-8'); ?></span></td>
                    <td><?php echo $admin['last_login'] ? date('M d, Y', strtotime($admin['last_login'])) : 'Never'; ?></td>
                    <td>
                      <form method="post" style="display:inline;" onsubmit="return confirm('Delete this admin?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($admin['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="mdi mdi-delete"></i></button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Create Admin User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="create">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-control">
              <option value="editor">Editor</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create</button>
        </div>
      </form>
    </div>
  </div>
</div>
