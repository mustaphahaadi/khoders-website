<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../config/security.php';

// Require admin authentication
Auth::requireAuth('login.php');

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    $error = 'Unable to connect to the database. Please verify database credentials and try again.';
}

// Initialize variables
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';
$form_type_filter = $_GET['form_type'] ?? '';
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;

// Only proceed with database operations when a connection is available
$logs = [];
$total_logs = 0;

if ($db) {
    // Handle delete action with admin role check
    if ($action === 'delete' && isset($_GET['id'])) {
        if (!Auth::hasRole('admin')) {
            $error = 'You do not have permission to delete logs';
        } else {
            try {
                $stmt = $db->prepare("DELETE FROM form_logs WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $message = 'Log entry deleted successfully';
                $action = 'list'; // Return to list view
            } catch(PDOException $e) {
                $error = 'Failed to delete log entry: ' . $e->getMessage();
            }
        }
    }

    // Handle clear logs action with admin role check
    if ($action === 'clear' && isset($_POST['confirm_clear']) && $_POST['confirm_clear'] === 'yes') {
        if (!Auth::hasRole('admin')) {
            $error = 'You do not have permission to clear logs';
        } else {
            try {
                // Build conditions for targeted clearing
                $conditions = [];
                $params = [];
                
                if (!empty($form_type_filter)) {
                    $conditions[] = "form_type = ?";
                    $params[] = $form_type_filter;
                }
                
                if (!empty($status_filter)) {
                    $conditions[] = "status = ?";
                    $params[] = $status_filter;
                }
                
                if (!empty($date_from)) {
                    $conditions[] = "created_at >= ?";
                    $params[] = $date_from . ' 00:00:00';
                }
                
                if (!empty($date_to)) {
                    $conditions[] = "created_at <= ?";
                    $params[] = $date_to . ' 23:59:59';
                }
                
                $sql = "DELETE FROM form_logs";
                if (!empty($conditions)) {
                    $sql .= " WHERE " . implode(' AND ', $conditions);
                }
                
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                
                $message = 'Log entries cleared successfully';
                $action = 'list';
            } catch(PDOException $e) {
                $error = 'Failed to clear logs: ' . $e->getMessage();
            }
        }
    }

    // Fetch form logs with filters and pagination
    try {
        // Build the query with filters
        $conditions = [];
        $params = [];
        
        if (!empty($form_type_filter)) {
            $conditions[] = "form_type = ?";
            $params[] = $form_type_filter;
        }
        
        if (!empty($status_filter)) {
            $conditions[] = "status = ?";
            $params[] = $status_filter;
        }
        
        if (!empty($date_from)) {
            $conditions[] = "created_at >= ?";
            $params[] = $date_from . ' 00:00:00';
        }
        
        if (!empty($date_to)) {
            $conditions[] = "created_at <= ?";
            $params[] = $date_to . ' 23:59:59';
        }
        
        // Count total for pagination
        $count_sql = "SELECT COUNT(*) AS total FROM form_logs";
        if (!empty($conditions)) {
            $count_sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $count_stmt = $db->prepare($count_sql);
        $count_stmt->execute($params);
        $total_logs = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Calculate pagination
        $total_pages = $total_logs > 0 ? ceil($total_logs / $per_page) : 1;
        $page = max(1, min($page, $total_pages));
        $offset = ($page - 1) * $per_page;
        
        // Get filtered logs with pagination
        $sql = "SELECT * FROM form_logs";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $db->prepare($sql);
        
        // Add pagination parameters
        $params[] = $per_page;
        $params[] = $offset;
        
        $stmt->execute($params);
        $logs = $stmt->fetchAll();
        
    } catch(PDOException $e) {
        $error = 'Error retrieving logs: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Logs - KHODERS Admin</title>
    <link href="https:/cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https:/cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .status-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .status-error {
            background-color: #f8d7da;
            color: #842029;
        }
        .status-spam {
            background-color: #fff3cd;
            color: #664d03;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-xl-2 px-0 bg-dark sidebar">
                <div class="d-flex flex-column p-3 text-white">
                    <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-4">KHODERS Admin</span>
                    </a>
                    <hr>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link text-white">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="members.php" class="nav-link text-white">
                                <i class="bi bi-people me-2"></i>Members
                            </a>
                        </li>
                        <li>
                            <a href="form-logs.php" class="nav-link active">
                                <i class="bi bi-journal-text me-2"></i>Form Logs
                            </a>
                        </li>
                        <li>
                            <a href="logout.php" class="nav-link text-white">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-xl-10 ms-sm-auto px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Form Submission Logs</h1>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Filter Form -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Filter Logs</h5>
                    </div>
                    <div class="card-body">
                        <form action="form-logs.php" method="get" class="row g-3">
                            <div class="col-md-3">
                                <label for="form_type" class="form-label">Form Type</label>
                                <select name="form_type" id="form_type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="contact" <?= $form_type_filter === 'contact' ? 'selected' : '' ?>>Contact</option>
                                    <option value="register" <?= $form_type_filter === 'register' ? 'selected' : '' ?>>Registration</option>
                                    <option value="newsletter" <?= $form_type_filter === 'newsletter' ? 'selected' : '' ?>>Newsletter</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="success" <?= $status_filter === 'success' ? 'selected' : '' ?>>Success</option>
                                    <option value="error" <?= $status_filter === 'error' ? 'selected' : '' ?>>Error</option>
                                    <option value="spam" <?= $status_filter === 'spam' ? 'selected' : '' ?>>Spam</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <a href="form-logs.php" class="btn btn-secondary">Reset</a>
                                <?php if (Auth::hasRole('admin')): ?>
                                <button type="button" class="btn btn-danger float-end" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                                    Clear Logs
                                </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Logs Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Form Submission Logs</h5>
                        <span class="badge bg-secondary"><?= $total_logs ?> entries</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($logs)): ?>
                            <div class="alert alert-info">No logs found matching your criteria.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Form Type</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>IP Address</th>
                                            <th>Timestamp</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logs as $log): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($log['id']) ?></td>
                                                <td>
                                                    <span class="badge <?php 
                                                        switch($log['form_type']) {
                                                            case 'contact': echo 'bg-primary'; break;
                                                            case 'register': echo 'bg-success'; break;
                                                            case 'newsletter': echo 'bg-info'; break;
                                                            default: echo 'bg-secondary';
                                                        }
                                                    ?>">
                                                        <?= htmlspecialchars($log['form_type']) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($log['email']) ?></td>
                                                <td>
                                                    <span class="badge <?php 
                                                        switch($log['status']) {
                                                            case 'success': echo 'bg-success'; break;
                                                            case 'error': echo 'bg-danger'; break;
                                                            case 'spam': echo 'bg-warning text-dark'; break;
                                                            default: echo 'bg-secondary';
                                                        }
                                                    ?>">
                                                        <?= htmlspecialchars($log['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($log['ip_address']) ?></td>
                                                <td><?= htmlspecialchars($log['created_at']) ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal<?= $log['id'] ?>">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <?php if (Auth::hasRole('admin')): ?>
                                                    <a href="form-logs.php?action=delete&id=<?= $log['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this log?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            
                                            <!-- View Modal for each log -->
                                            <div class="modal fade" id="viewModal<?= $log['id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $log['id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="viewModalLabel<?= $log['id'] ?>">Log Details #<?= $log['id'] ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <h6>Basic Information</h6>
                                                                    <table class="table table-sm table-bordered">
                                                                        <tr>
                                                                            <th>Form Type:</th>
                                                                            <td><?= htmlspecialchars($log['form_type']) ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Email:</th>
                                                                            <td><?= htmlspecialchars($log['email']) ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Status:</th>
                                                                            <td><?= htmlspecialchars($log['status']) ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Timestamp:</th>
                                                                            <td><?= htmlspecialchars($log['created_at']) ?></td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h6>Technical Details</h6>
                                                                    <table class="table table-sm table-bordered">
                                                                        <tr>
                                                                            <th>IP Address:</th>
                                                                            <td><?= htmlspecialchars($log['ip_address']) ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>User Agent:</th>
                                                                            <td class="small" style="word-break: break-all;"><?= htmlspecialchars($log['user_agent']) ?></td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            
                                                            <?php if (!empty($log['error_message'])): ?>
                                                            <div class="alert alert-danger">
                                                                <h6>Error Message:</h6>
                                                                <pre class="mb-0"><?= htmlspecialchars($log['error_message']) ?></pre>
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page-1 ?>&form_type=<?= $form_type_filter ?>&status=<?= $status_filter ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>">Previous</a>
                                    </li>
                                    
                                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>&form_type=<?= $form_type_filter ?>&status=<?= $status_filter ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page+1 ?>&form_type=<?= $form_type_filter ?>&status=<?= $status_filter ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Clear Logs Modal -->
    <div class="modal fade" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="clearLogsModalLabel">Clear Form Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Warning: This action cannot be undone!
                    </div>
                    <p>You are about to delete the following log entries:</p>
                    <ul>
                        <li><strong>Form Type:</strong> <?= !empty($form_type_filter) ? htmlspecialchars($form_type_filter) : 'All types' ?></li>
                        <li><strong>Status:</strong> <?= !empty($status_filter) ? htmlspecialchars($status_filter) : 'All statuses' ?></li>
                        <li><strong>Date Range:</strong> <?= !empty($date_from) || !empty($date_to) ? htmlspecialchars($date_from) . ' to ' . htmlspecialchars($date_to) : 'All dates' ?></li>
                    </ul>
                    <p>Total entries to be deleted: <strong><?= $total_logs ?></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="form-logs.php?action=clear" method="post">
                        <input type="hidden" name="confirm_clear" value="yes">
                        <input type="hidden" name="form_type" value="<?= htmlspecialchars($form_type_filter) ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                        <input type="hidden" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
                        <input type="hidden" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
                        <button type="submit" class="btn btn-danger">Yes, Clear Logs</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https:/cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

