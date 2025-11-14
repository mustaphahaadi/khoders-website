<?php
/**
 * KHODERS WORLD Admin Team Members Page
 * Manage team members
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Team Members - KHODERS WORLD Admin');
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

// Initialize variables
$currentPage = 'team';
$action = $_GET['action'] ?? 'list';
$message = $_GET['message'] ?? '';
$error = '';
$members = [];
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
        $stmt = $db->prepare("DELETE FROM team_members WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = 'Team member deleted successfully.';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Error deleting team member: ' . $e->getMessage();
    }
}

// Fetch team members with optional status filter
$members = [];
if ($db) {
    try {
        $sql = "SELECT * FROM team_members";
        $params = [];
        
        if ($statusFilter !== 'all') {
            $sql .= " WHERE status = ?";
            $params[] = $statusFilter;
        }
        
        $sql .= " ORDER BY order_index ASC, name ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Error fetching team members: ' . $e->getMessage();
    }
}

// Create table if it doesn't exist
$tableExists = $db ? admin_table_exists($db, 'team_members') : false;
if ($db && !$tableExists) {
    try {
        $db->exec("CREATE TABLE team_members (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            position VARCHAR(100),
            bio TEXT,
            photo_url VARCHAR(255),
            email VARCHAR(100),
            linkedin_url VARCHAR(255),
            github_url VARCHAR(255),
            twitter_url VARCHAR(255),
            personal_website VARCHAR(255),
            is_featured TINYINT(1) DEFAULT 0,
            status VARCHAR(20) DEFAULT 'active',
            order_index INT DEFAULT 0,
            created_at DATETIME,
            updated_at DATETIME
        )");
        $message = 'Team members table created successfully.';
    } catch (PDOException $e) {
        $error = 'Error creating team members table: ' . $e->getMessage();
    }
}

// Count members by status
$statusCounts = [
    'all' => 0,
    'active' => 0,
    'inactive' => 0,
    'alumni' => 0
];

foreach ($members as $member) {
    $statusCounts['all']++;
    $status = $member['status'] ?? 'active';
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
              <h4 class="card-title card-title-dash">Team Members</h4>
              <p class="card-subtitle card-subtitle-dash">Manage KHODERS WORLD team members</p>
            </div>
            <div>
              <a href="?route=team-editor" class="btn btn-primary btn-lg text-white mb-0 me-0">
                <i class="mdi mdi-account-plus"></i> Add New Member
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
              <i class="mdi mdi-database-alert"></i> The team_members table doesn't exist yet. Click the button below to create it.
              <form method="post" action="?route=team" class="mt-3">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <button type="submit" name="create_table" class="btn btn-warning">Create Team Members Table</button>
              </form>
            </div>
          <?php else: ?>
            <!-- Status filter tabs -->
            <ul class="nav nav-tabs mb-4">
              <li class="nav-item">
                <a class="nav-link <?php echo $statusFilter === 'all' ? 'active' : ''; ?>" href="?route=team&status=all">
                  All <span class="badge bg-secondary"><?php echo $statusCounts['all']; ?></span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $statusFilter === 'active' ? 'active' : ''; ?>" href="?route=team&status=active">
                  Active <span class="badge bg-success"><?php echo $statusCounts['active']; ?></span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $statusFilter === 'inactive' ? 'active' : ''; ?>" href="?route=team&status=inactive">
                  Inactive <span class="badge bg-warning"><?php echo $statusCounts['inactive']; ?></span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $statusFilter === 'alumni' ? 'active' : ''; ?>" href="?route=team&status=alumni">
                  Alumni <span class="badge bg-info"><?php echo $statusCounts['alumni']; ?></span>
                </a>
              </li>
            </ul>
            
            <?php if (empty($members)): ?>
              <div class="text-center py-5 bg-light rounded">
                <i class="mdi mdi-account-group mdi-48px text-muted"></i>
                <p class="mt-3 mb-0 text-muted">No team members found.</p>
                <?php if ($statusFilter !== 'all'): ?>
                  <p class="text-muted">Try changing the filter or <a href="?route=team-editor" class="text-primary">add a new member</a>.</p>
                <?php else: ?>
                  <p class="text-muted">Get started by <a href="?route=team-editor" class="text-primary">adding your first team member</a>.</p>
                <?php endif; ?>
              </div>
            <?php else: ?>
              <div class="row">
                <?php foreach ($members as $member): ?>
                  <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php echo htmlspecialchars($member['name']); ?></h5>
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton<?php echo (int)$member['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo (int)$member['id']; ?>">
                            <li><a class="dropdown-item" href="?route=team-editor&action=edit&id=<?php echo (int)$member['id']; ?>"><i class="mdi mdi-pencil"></i> Edit</a></li>
                            <li><a class="dropdown-item text-danger" href="?route=team&action=delete&id=<?php echo (int)$member['id']; ?>" onclick="return confirm('Are you sure you want to delete this team member?');"><i class="mdi mdi-delete"></i> Delete</a></li>
                          </ul>
                        </div>
                      </div>
                      
                      <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                          <?php if (!empty($member['photo_url'])): ?>
                            <img src="<?php echo htmlspecialchars($member['photo_url']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                          <?php else: ?>
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white me-3" style="width: 60px; height: 60px;">
                              <?php echo strtoupper(substr($member['name'], 0, 1)); ?>
                            </div>
                          <?php endif; ?>
                          
                          <div>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($member['position'] ?? ''); ?></p>
                            <?php if (!empty($member['email'])): ?>
                              <p class="mb-0"><a href="mailto:<?php echo htmlspecialchars($member['email']); ?>"><?php echo htmlspecialchars($member['email']); ?></a></p>
                            <?php endif; ?>
                          </div>
                        </div>
                        
                        <?php if (!empty($member['bio'])): ?>
                          <p class="card-text mb-3"><?php echo admin_excerpt($member['bio'], 100); ?></p>
                        <?php endif; ?>
                        
                        <!-- Status badges -->
                        <div class="mb-3">
                          <?php if ($member['is_featured']): ?>
                            <span class="badge bg-primary me-1">Featured</span>
                          <?php endif; ?>
                          
                          <?php 
                            $statusClass = 'secondary';
                            switch ($member['status']) {
                                case 'active': $statusClass = 'success'; break;
                                case 'inactive': $statusClass = 'warning'; break;
                                case 'alumni': $statusClass = 'info'; break;
                            }
                          ?>
                          <span class="badge bg-<?php echo $statusClass; ?>"><?php echo ucfirst($member['status']); ?></span>
                          
                          <?php if ($member['order_index'] > 0): ?>
                            <span class="badge bg-secondary">Order: <?php echo (int)$member['order_index']; ?></span>
                          <?php endif; ?>
                        </div>
                        
                        <!-- Social links -->
                        <div class="social-links">
                          <?php if (!empty($member['linkedin_url'])): ?>
                            <a href="<?php echo htmlspecialchars($member['linkedin_url']); ?>" target="_blank" class="btn btn-outline-primary btn-sm me-1" title="LinkedIn">
                              <i class="mdi mdi-linkedin"></i>
                            </a>
                          <?php endif; ?>
                          
                          <?php if (!empty($member['github_url'])): ?>
                            <a href="<?php echo htmlspecialchars($member['github_url']); ?>" target="_blank" class="btn btn-outline-dark btn-sm me-1" title="GitHub">
                              <i class="mdi mdi-github"></i>
                            </a>
                          <?php endif; ?>
                          
                          <?php if (!empty($member['twitter_url'])): ?>
                            <a href="<?php echo htmlspecialchars($member['twitter_url']); ?>" target="_blank" class="btn btn-outline-info btn-sm me-1" title="Twitter">
                              <i class="mdi mdi-twitter"></i>
                            </a>
                          <?php endif; ?>
                          
                          <?php if (!empty($member['personal_website'])): ?>
                            <a href="<?php echo htmlspecialchars($member['personal_website']); ?>" target="_blank" class="btn btn-outline-success btn-sm me-1" title="Website">
                              <i class="mdi mdi-web"></i>
                            </a>
                          <?php endif; ?>
                        </div>
                      </div>
                      
                      <div class="card-footer text-muted">
                        <small>Added: <?php echo admin_format_date($member['created_at'] ?? null); ?></small>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
