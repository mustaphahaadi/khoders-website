<?php
/**
 * KHODERS WORLD Admin Form Logs Page
 * Displayed when accessing the form-logs route
 */

if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Form Logs - KHODERS WORLD Admin');
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

$currentPage = 'form-logs';
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';
$form_type_filter = $_GET['form_type'] ?? '';
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$logs = [];
$total_logs = 0;
$total_pages = 1;
$hasAdminRole = Auth::hasRole('admin');

$user = Auth::user();

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    $error = 'Unable to connect to the database. Please verify database credentials and try again.';
}

if ($db) {
    if ($action === 'delete' && isset($_GET['id'])) {
        if (!Auth::hasRole('admin')) {
            $error = 'You do not have permission to delete logs';
        } else {
            try {
                $stmt = $db->prepare("DELETE FROM form_logs WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $message = 'Log entry deleted successfully';
                $action = 'list';
            } catch(PDOException $e) {
                $error = 'Failed to delete log entry: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'clear' && isset($_POST['confirm_clear'])) {
        if (!Auth::hasRole('admin')) {
            $error = 'You do not have permission to clear logs';
        } else {
            try {
                $sql = "DELETE FROM form_logs WHERE 1=1";
                $params = [];
                
                if (!empty($form_type_filter)) {
                    $sql .= " AND form_type = ?";
                    $params[] = $form_type_filter;
                }
                
                if (!empty($status_filter)) {
                    $sql .= " AND status = ?";
                    $params[] = $status_filter;
                }
                
                if (!empty($date_from)) {
                    $sql .= " AND created_at >= ?";
                    $params[] = $date_from . ' 00:00:00';
                }
                
                if (!empty($date_to)) {
                    $sql .= " AND created_at <= ?";
                    $params[] = $date_to . ' 23:59:59';
                }
                
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                
                $count = $stmt->rowCount();
                $message = "Successfully cleared $count log entries";
                
                $form_type_filter = '';
                $status_filter = '';
                $date_from = '';
                $date_to = '';
            } catch(PDOException $e) {
                $error = 'Failed to clear logs: ' . $e->getMessage();
            }
        }
    }

    try {
        $sql = "SELECT COUNT(*) FROM form_logs WHERE 1=1";
        $params = [];
        
        if (!empty($form_type_filter)) {
            $sql .= " AND form_type = ?";
            $params[] = $form_type_filter;
        }
        
        if (!empty($status_filter)) {
            $sql .= " AND status = ?";
            $params[] = $status_filter;
        }
        
        if (!empty($date_from)) {
            $sql .= " AND created_at >= ?";
            $params[] = $date_from . ' 00:00:00';
        }
        
        if (!empty($date_to)) {
            $sql .= " AND created_at <= ?";
            $params[] = $date_to . ' 23:59:59';
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $total_logs = (int) $stmt->fetchColumn();
        $total_pages = ceil($total_logs / $per_page);
        
        if ($page < 1) $page = 1;
        if ($page > $total_pages && $total_pages > 0) $page = $total_pages;
        
        $offset = ($page - 1) * $per_page;
        
        $sql = "SELECT * FROM form_logs WHERE 1=1";
        
        if (!empty($form_type_filter)) {
            $sql .= " AND form_type = ?";
        }
        
        if (!empty($status_filter)) {
            $sql .= " AND status = ?";
        }
        
        if (!empty($date_from)) {
            $sql .= " AND created_at >= ?";
        }
        
        if (!empty($date_to)) {
            $sql .= " AND created_at <= ?";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ?, ?";
        
        $stmt = $db->prepare($sql);
        $index = 1;
        
        if (!empty($form_type_filter)) {
            $stmt->bindValue($index++, $form_type_filter);
        }
        
        if (!empty($status_filter)) {
            $stmt->bindValue($index++, $status_filter);
        }
        
        if (!empty($date_from)) {
            $stmt->bindValue($index++, $date_from . ' 00:00:00');
        }
        
        if (!empty($date_to)) {
            $stmt->bindValue($index++, $date_to . ' 23:59:59');
        }
        
        $stmt->bindValue($index++, $offset, PDO::PARAM_INT);
        $stmt->bindValue($index++, $per_page, PDO::PARAM_INT);
        
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = 'Failed to fetch logs: ' . $e->getMessage();
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
              <h4 class="card-title card-title-dash">Form Logs</h4>
              <p class="card-subtitle card-subtitle-dash">Monitor form submissions and activity</p>
            </div>
            <?php if ($hasAdminRole): ?>
            <div>
              <button type="button" class="btn btn-danger btn-lg text-white mb-0 me-0" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                <i class="mdi mdi-delete-sweep"></i> Clear Logs
              </button>
            </div>
            <?php endif; ?>
          </div>
          
          <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
              <i class="mdi mdi-check-circle-outline"></i> <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
          <?php endif; ?>
          
          <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
              <i class="mdi mdi-alert-circle"></i> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
          <?php endif; ?>
          
          <!-- Filter Form -->
          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title"><i class="mdi mdi-filter"></i> Filter Logs</h5>
              <form action="" method="get" class="row g-3">
                <input type="hidden" name="route" value="form-logs">
                <div class="col-md-3">
                  <label for="form_type" class="form-label">Form Type</label>
                  <select class="form-select" id="form_type" name="form_type">
                    <option value="">All Types</option>
                    <option value="contact" <?php echo $form_type_filter === 'contact' ? 'selected' : ''; ?>>Contact</option>
                    <option value="newsletter" <?php echo $form_type_filter === 'newsletter' ? 'selected' : ''; ?>>Newsletter</option>
                    <option value="registration" <?php echo $form_type_filter === 'registration' ? 'selected' : ''; ?>>Registration</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="success" <?php echo $status_filter === 'success' ? 'selected' : ''; ?>>Success</option>
                    <option value="error" <?php echo $status_filter === 'error' ? 'selected' : ''; ?>>Error</option>
                    <option value="spam" <?php echo $status_filter === 'spam' ? 'selected' : ''; ?>>Spam</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label for="date_from" class="form-label">Date From</label>
                  <div class="input-group">
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo htmlspecialchars($date_from, ENT_QUOTES, 'UTF-8'); ?>">
                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                  </div>
                </div>
                <div class="col-md-2">
                  <label for="date_to" class="form-label">Date To</label>
                  <div class="input-group">
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo htmlspecialchars($date_to, ENT_QUOTES, 'UTF-8'); ?>">
                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                  </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                  <div class="d-grid gap-2 w-100">
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-filter-outline"></i> Apply Filters</button>
                    <a href="?route=form-logs" class="btn btn-outline-secondary"><i class="mdi mdi-refresh"></i> Reset</a>
                  </div>
                </div>
              </form>
            </div>
          </div>
          
          <!-- Logs Table -->
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Form Type</th>
                  <th>Status</th>
                  <th>Email</th>
                  <th>Date/Time</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($logs)): ?>
                  <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                      <i class="mdi mdi-file-document-outline mdi-48px d-block mb-2"></i>
                      No logs found
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($logs as $log): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($log['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                      <td>
                        <div class="badge bg-primary">
                          <i class="mdi mdi-<?php 
                            $form_type = strtolower($log['form_type'] ?? '');
                            if ($form_type === 'contact') echo 'message-text';
                            elseif ($form_type === 'newsletter') echo 'email-outline';
                            elseif ($form_type === 'registration') echo 'account-plus';
                            else echo 'file-document';
                          ?>"></i>
                          <?php echo htmlspecialchars(ucfirst($log['form_type'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                      </td>
                      <td>
                        <div class="badge bg-<?php 
                          $status = strtolower($log['status'] ?? '');
                          if ($status === 'success') echo 'success';
                          elseif ($status === 'error') echo 'danger';
                          elseif ($status === 'spam') echo 'warning';
                          else echo 'secondary';
                        ?>">
                          <i class="mdi mdi-<?php 
                            $status = strtolower($log['status'] ?? '');
                            if ($status === 'success') echo 'check-circle';
                            elseif ($status === 'error') echo 'alert-circle';
                            elseif ($status === 'spam') echo 'alert-octagon';
                            else echo 'information';
                          ?>"></i>
                          <?php echo htmlspecialchars(ucfirst($log['status'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="rounded-circle profile-image-small bg-info d-flex align-items-center justify-content-center text-white">
                            <i class="mdi mdi-email-outline"></i>
                          </div>
                          <div class="ms-2">
                            <?php echo htmlspecialchars($log['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                          </div>
                        </div>
                      </td>
                      <td><?php echo admin_format_date($log['created_at'] ?? null, 'M d, Y H:i:s'); ?></td>
                      <td>
                        <div class="d-flex">
                          <button type="button" class="btn btn-outline-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#viewLogModal" 
                                  data-id="<?php echo htmlspecialchars($log['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                  data-form-type="<?php echo htmlspecialchars($log['form_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                  data-status="<?php echo htmlspecialchars($log['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                  data-email="<?php echo htmlspecialchars($log['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                  data-data="<?php echo htmlspecialchars($log['data'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                  data-error="<?php echo htmlspecialchars($log['error_message'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                  data-ip="<?php echo htmlspecialchars($log['ip_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                  data-date="<?php echo admin_format_date($log['created_at'] ?? null, 'M d, Y H:i:s'); ?>">
                            <i class="mdi mdi-eye"></i>
                          </button>
                          <?php if ($hasAdminRole): ?>
                          <a href="?route=form-logs&action=delete&id=<?php echo htmlspecialchars($log['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                             class="btn btn-outline-danger btn-sm"
                             onclick="return confirm('Are you sure you want to delete this log entry?')">
                            <i class="mdi mdi-delete"></i>
                          </a>
                          <?php endif; ?>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-center mt-4">
              <nav>
                <ul class="pagination">
                  <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?route=form-logs&page=1<?php echo !empty($form_type_filter) ? '&form_type=' . urlencode($form_type_filter) : ''; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($date_from) ? '&date_from=' . urlencode($date_from) : ''; ?><?php echo !empty($date_to) ? '&date_to=' . urlencode($date_to) : ''; ?>">
                      <i class="mdi mdi-chevron-double-left"></i>
                    </a>
                  </li>
                  <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?route=form-logs&page=<?php echo $page - 1; ?><?php echo !empty($form_type_filter) ? '&form_type=' . urlencode($form_type_filter) : ''; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($date_from) ? '&date_from=' . urlencode($date_from) : ''; ?><?php echo !empty($date_to) ? '&date_to=' . urlencode($date_to) : ''; ?>">
                      <i class="mdi mdi-chevron-left"></i>
                    </a>
                  </li>
                  
                  <?php
                  $start_page = max(1, $page - 2);
                  $end_page = min($total_pages, $page + 2);
                  
                  for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                      <a class="page-link" href="?route=form-logs&page=<?php echo $i; ?><?php echo !empty($form_type_filter) ? '&form_type=' . urlencode($form_type_filter) : ''; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($date_from) ? '&date_from=' . urlencode($date_from) : ''; ?><?php echo !empty($date_to) ? '&date_to=' . urlencode($date_to) : ''; ?>">
                        <?php echo $i; ?>
                      </a>
                    </li>
                  <?php endfor; ?>
                  
                  <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?route=form-logs&page=<?php echo $page + 1; ?><?php echo !empty($form_type_filter) ? '&form_type=' . urlencode($form_type_filter) : ''; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($date_from) ? '&date_from=' . urlencode($date_from) : ''; ?><?php echo !empty($date_to) ? '&date_to=' . urlencode($date_to) : ''; ?>">
                      <i class="mdi mdi-chevron-right"></i>
                    </a>
                  </li>
                  <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?route=form-logs&page=<?php echo $total_pages; ?><?php echo !empty($form_type_filter) ? '&form_type=' . urlencode($form_type_filter) : ''; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($date_from) ? '&date_from=' . urlencode($date_from) : ''; ?><?php echo !empty($date_to) ? '&date_to=' . urlencode($date_to) : ''; ?>">
                      <i class="mdi mdi-chevron-double-right"></i>
                    </a>
                  </li>
                </ul>
              </nav>
            </div>
          <?php endif; ?>
          
        </div>
      </div>
    </div>
  </div>
</div>

<!-- View Log Modal -->
<div class="modal fade" id="viewLogModal" tabindex="-1" aria-labelledby="viewLogModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewLogModalLabel"><i class="mdi mdi-file-document-outline"></i> Log Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="card mb-3">
              <div class="card-header bg-light">
                <h6 class="mb-0">Basic Information</h6>
              </div>
              <div class="card-body">
                <p><strong>ID:</strong> <span id="log-id"></span></p>
                <p><strong>Form Type:</strong> <span id="log-form-type"></span></p>
                <p><strong>Status:</strong> <span id="log-status"></span></p>
                <p><strong>Email:</strong> <span id="log-email"></span></p>
                <p><strong>Date/Time:</strong> <span id="log-date"></span></p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card mb-3">
              <div class="card-header bg-light">
                <h6 class="mb-0">Technical Information</h6>
              </div>
              <div class="card-body">
                <p><strong>IP Address:</strong> <span id="log-ip"></span></p>
                <div id="log-error-container">
                  <p><strong>Error Message:</strong></p>
                  <pre id="log-error" class="bg-light p-2 rounded"></pre>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header bg-light">
            <h6 class="mb-0">Form Data</h6>
          </div>
          <div class="card-body">
            <pre id="log-data" class="bg-light p-2 rounded"></pre>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Clear Logs Modal -->
<?php if ($hasAdminRole): ?>
<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="clearLogsModalLabel"><i class="mdi mdi-delete-sweep"></i> Clear Logs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning">
          <i class="mdi mdi-alert"></i> Warning: This action cannot be undone. All logs matching the current filters will be permanently deleted.
        </div>
        
        <h6 class="mb-3">Current Filters:</h6>
        <ul class="list-group mb-3">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Form Type
            <span class="badge bg-primary rounded-pill"><?php echo !empty($form_type_filter) ? htmlspecialchars(ucfirst($form_type_filter), ENT_QUOTES, 'UTF-8') : 'All'; ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Status
            <span class="badge bg-primary rounded-pill"><?php echo !empty($status_filter) ? htmlspecialchars(ucfirst($status_filter), ENT_QUOTES, 'UTF-8') : 'All'; ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Date Range
            <span class="badge bg-primary rounded-pill"><?php echo (!empty($date_from) || !empty($date_to)) ? (htmlspecialchars($date_from, ENT_QUOTES, 'UTF-8') ?: 'Any') . ' to ' . (htmlspecialchars($date_to, ENT_QUOTES, 'UTF-8') ?: 'Any') : 'All Dates'; ?></span>
          </li>
        </ul>
        
        <div class="alert alert-info">
          <i class="mdi mdi-information"></i> Total entries to be deleted: <strong><?php echo $total_logs; ?></strong>
        </div>
        
        <form action="?route=form-logs&action=clear" method="post">
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="confirm_clear" name="confirm_clear" value="1" required>
            <label class="form-check-label" for="confirm_clear">
              I confirm that I want to permanently delete these logs
            </label>
          </div>
          
          <input type="hidden" name="form_type" value="<?php echo htmlspecialchars($form_type_filter, ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter, ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="date_from" value="<?php echo htmlspecialchars($date_from, ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="date_to" value="<?php echo htmlspecialchars($date_to, ENT_QUOTES, 'UTF-8'); ?>">
          
          <div class="d-grid">
            <button type="submit" class="btn btn-danger" id="clearLogsBtn" disabled>Clear Logs</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
$(document).ready(function() {
  $('.table').DataTable({
    "aLengthMenu": [[10, 30, 50, -1], [10, 30, 50, "All"]],
    "iDisplayLength": 10,
    "language": { search: "" },
    "paging": false
  });
  $('.dataTables_filter input').attr("placeholder", "Search logs...");
  
  $('#viewLogModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var formType = button.data('form-type');
    var status = button.data('status');
    var email = button.data('email');
    var data = button.data('data');
    var error = button.data('error');
    var ip = button.data('ip');
    var date = button.data('date');
    
    $('#log-id').text(id);
    $('#log-form-type').text(formType);
    $('#log-status').text(status);
    $('#log-email').text(email);
    $('#log-ip').text(ip);
    $('#log-date').text(date);
    
    try {
      var jsonData = JSON.parse(data);
      $('#log-data').text(JSON.stringify(jsonData, null, 2));
    } catch (e) {
      $('#log-data').text(data || 'No data available');
    }
    
    if (error) {
      $('#log-error').text(error);
      $('#log-error-container').show();
    } else {
      $('#log-error-container').hide();
    }
  });
  
  $('#confirm_clear').change(function() {
    $('#clearLogsBtn').prop('disabled', !this.checked);
  });
});
</script>
