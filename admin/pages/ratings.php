<?php
/**
 * KHODERS WORLD Admin - Ratings & Reviews Management
 * Moderate and manage user ratings
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Ratings Management - KHODERS WORLD Admin');
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

$currentPage = 'ratings';
$message = '';
$error = '';

// Get filters
$status_filter = $_GET['status'] ?? 'pending';
$type_filter = $_GET['type'] ?? 'all';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Get current user
$user = Auth::user();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Handle actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $error = 'Invalid security token';
        } else {
            $action = $_POST['action'];
            $rating_id = (int)$_POST['rating_id'];
            $admin_notes = trim($_POST['admin_notes'] ?? '');
            
            if ($action === 'approve') {
                $stmt = $db->prepare("
                    UPDATE ratings 
                    SET status = 'approved', reviewed_by = ?, reviewed_at = NOW(), admin_notes = ?
                    WHERE id = ?
                ");
                $stmt->execute([$user['id'], $admin_notes, $rating_id]);
                
                // Update average rating
                $rating_info = $db->query("SELECT rateable_type, rateable_id FROM ratings WHERE id = $rating_id")->fetch();
                $db->query("CALL update_average_rating('{$rating_info['rateable_type']}', {$rating_info['rateable_id']})");
                
                $message = 'Rating approved successfully';
            } elseif ($action === 'reject') {
                $stmt = $db->prepare("
                    UPDATE ratings 
                    SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW(), admin_notes = ?
                    WHERE id = ?
                ");
                $stmt->execute([$user['id'], $admin_notes, $rating_id]);
                $message = 'Rating rejected';
            } elseif ($action === 'delete') {
                $db->query("DELETE FROM ratings WHERE id = $rating_id");
                $message = 'Rating deleted';
            }
        }
    }
    
    // Build query
    $where_conditions = [];
    $params = [];
    
    if ($status_filter !== 'all') {
        $where_conditions[] = "r.status = ?";
        $params[] = $status_filter;
    }
    
    if ($type_filter !== 'all') {
        $where_conditions[] = "r.rateable_type = ?";
        $params[] = $type_filter;
    }
    
    $where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Get total count
    $count_sql = "SELECT COUNT(*) as total FROM ratings r $where_clause";
    $count_stmt = $db->prepare($count_sql);
    $count_stmt->execute($params);
    $total = $count_stmt->fetch()['total'];
    
    // Get ratings
    $sql = "
        SELECT 
            r.*,
            m.name as member_name,
            m.email as member_email,
            a.username as reviewed_by_name
        FROM ratings r
        LEFT JOIN members m ON r.member_id = m.id
        LEFT JOIN admin_users a ON r.reviewed_by = a.id
        $where_clause
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $stats = $db->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
        FROM ratings
    ")->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = 'Error loading ratings: ' . $e->getMessage();
    $ratings = [];
    $stats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
}

$csrfToken = Security::generateCSRFToken();
?>

<div class="content-wrapper">
  <div class="row">
    <div class="col-md-12 grid-margin">
      <div class="row">
        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
          <h3 class="font-weight-bold">Ratings & Reviews Management</h3>
          <h6 class="font-weight-normal mb-0">Moderate user ratings and reviews</h6>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-md-3 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-title text-md-center text-xl-left">Total Ratings</p>
          <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
            <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?php echo number_format($stats['total']); ?></h3>
            <i class="ti-star icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
          </div>  
        </div>
      </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-title text-md-center text-xl-left">Pending</p>
          <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
            <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0 text-warning"><?php echo $stats['pending']; ?></h3>
            <i class="ti-time icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
          </div>  
        </div>
      </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-title text-md-center text-xl-left">Approved</p>
          <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
            <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0 text-success"><?php echo $stats['approved']; ?></h3>
            <i class="ti-check icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
          </div>  
        </div>
      </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-title text-md-center text-xl-left">Rejected</p>
          <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
            <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0 text-danger"><?php echo $stats['rejected']; ?></h3>
            <i class="ti-close icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
          </div>  
        </div>
      </div>
    </div>
  </div>
  
  <!-- Filters and List -->
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="mdi mdi-check-circle"></i> <?php echo htmlspecialchars($message); ?>
              <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
          <?php endif; ?>
          
          <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="mdi mdi-alert-circle"></i> <?php echo htmlspecialchars($error); ?>
              <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
          <?php endif; ?>
          
          <!-- Filters -->
          <form method="GET" class="form-inline mb-4">
            <input type="hidden" name="route" value="ratings">
            <div class="form-group mr-3">
              <label class="mr-2">Status:</label>
              <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="all" <?php echo $status_filter==='all'?'selected':''; ?>>All</option>
                <option value="pending" <?php echo $status_filter==='pending'?'selected':''; ?>>Pending</option>
                <option value="approved" <?php echo $status_filter==='approved'?'selected':''; ?>>Approved</option>
                <option value="rejected" <?php echo $status_filter==='rejected'?'selected':''; ?>>Rejected</option>
              </select>
            </div>
            <div class="form-group mr-3">
              <label class="mr-2">Type:</label>
              <select name="type" class="form-control" onchange="this.form.submit()">
                <option value="all" <?php echo $type_filter==='all'?'selected':''; ?>>All</option>
                <option value="course" <?php echo $type_filter==='course'?'selected':''; ?>>Courses</option>
                <option value="event" <?php echo $type_filter==='event'?'selected':''; ?>>Events</option>
                <option value="resource" <?php echo $type_filter==='resource'?'selected':''; ?>>Resources</option>
                <option value="instructor" <?php echo $type_filter==='instructor'?'selected':''; ?>>Instructors</option>
                <option value="project" <?php echo $type_filter==='project'?'selected':''; ?>>Projects</option>
              </select>
            </div>
          </form>
          
          <!-- Ratings Table -->
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Member</th>
                  <th>Type</th>
                  <th>Rating</th>
                  <th>Review</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($ratings)): ?>
                  <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                      <i class="mdi mdi-comment-alert mdi-48px d-block mb-2"></i>
                      No ratings found
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($ratings as $rating): ?>
                  <tr>
                    <td>
                      <?php if ($rating['is_anonymous']): ?>
                        <span class="text-muted">Anonymous</span>
                      <?php else: ?>
                        <strong><?php echo htmlspecialchars($rating['member_name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($rating['member_email']); ?></small>
                      <?php endif; ?>
                    </td>
                    <td><span class="badge badge-info"><?php echo ucfirst($rating['rateable_type']); ?></span></td>
                    <td>
                      <div class="rating-stars">
                        <?php for($i=1; $i<=5; $i++): ?>
                          <span class="<?php echo $i<=$rating['rating']?'text-warning':'text-muted'; ?>">★</span>
                        <?php endfor; ?>
                      </div>
                    </td>
                    <td>
                      <?php if ($rating['review']): ?>
                        <div style="max-width:300px;">
                          <?php echo nl2br(htmlspecialchars(strlen($rating['review'])>100 ? substr($rating['review'],0,100).'...' : $rating['review'])); ?>
                        </div>
                      <?php else: ?>
                        <span class="text-muted">No review</span>
                      <?php endif; ?>
                    </td>
                    <td><?php echo date('Y-m-d H:i', strtotime($rating['created_at'])); ?></td>
                    <td>
                      <?php 
                        $badge_class = ['pending'=>'warning','approved'=>'success','rejected'=>'danger'][$rating['status']];
                      ?>
                      <span class="badge badge-<?php echo $badge_class; ?>"><?php echo ucfirst($rating['status']); ?></span>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-success" onclick="moderateRating(<?php echo $rating['id']; ?>, 'approve')">
                        <i class="mdi mdi-check"></i> Approve
                      </button>
                      <button class="btn btn-sm btn-warning" onclick="moderateRating(<?php echo $rating['id']; ?>, 'reject')">
                        <i class="mdi mdi-close"></i> Reject
                      </button>
                      <button class="btn btn-sm btn-danger" onclick="if(confirm('Delete this rating permanently?')) moderateRating(<?php echo $rating['id']; ?>, 'delete')">
                        <i class="mdi mdi-delete"></i>
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <?php if ($total > $limit): ?>
            <nav class="mt-4">
              <ul class="pagination justify-content-center">
                <?php 
                $total_pages = ceil($total / $limit);
                for ($i = 1; $i <= $total_pages; $i++): 
                ?>
                  <li class="page-item <?php echo $i===$page?'active':''; ?>">
                    <a class="page-link" href="?route=ratings&status=<?php echo $status_filter; ?>&type=<?php echo $type_filter; ?>&page=<?php echo $i; ?>">
                      <?php echo $i; ?>
                    </a>
                  </li>
                <?php endfor; ?>
              </ul>
            </nav>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Hidden form for moderation -->
<form id="moderationForm" method="POST" style="display:none;">
  <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
  <input type="hidden" name="action" id="moderationAction">
  <input type="hidden" name="rating_id" id="moderationRatingId">
  <input type="hidden" name="admin_notes" id="moderationNotes">
</form>

<script>
function moderateRating(ratingId, action) {
  const notes = action === 'approve' || action === 'reject' 
    ? prompt(`Optional notes for ${action}ing this rating:`, '') 
    : '';
  
  if (action === 'delete' || notes !== null) {
    document.getElementById('moderationAction').value = action;
    document.getElementById('moderationRatingId').value = ratingId;
    document.getElementById('moderationNotes').value = notes || '';
    document.getElementById('moderationForm').submit();
  }
}
</script>
