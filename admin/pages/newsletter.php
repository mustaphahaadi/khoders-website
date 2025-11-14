<?php
/**
 * KHODERS WORLD Admin Newsletter Page
 * Displayed when accessing the newsletter route
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Newsletter Subscribers - KHODERS WORLD Admin');
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

// Initialize variables
$currentPage = 'newsletter';
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';
$entries = [];
$columns = [];

// Get current user
$user = Auth::user();

// Database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    $error = 'Unable to connect to the database. Please verify database credentials and try again.';
}

$tableExists = $db ? admin_table_exists($db, 'newsletter') : false;
$hasIdColumn = $tableExists ? admin_table_has_column($db, 'newsletter', 'id') : false;

if ($db && $tableExists && $action === 'delete' && $hasIdColumn && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare('DELETE FROM newsletter WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $message = 'Subscriber removed successfully';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Failed to remove subscriber';
    }
}

$columnLabels = [
    'id' => 'ID',
    'email' => 'Email',
    'source' => 'Source',
    'ip_address' => 'IP Address',
    'created_at' => 'Subscribed',
    'updated_at' => 'Updated',
];

if ($db && $tableExists) {
    $columns = admin_filter_columns(
        $db,
        'newsletter',
        ['id', 'email', 'source', 'ip_address', 'created_at', 'updated_at'],
        ['id', 'email', 'created_at']
    );

    if (empty($columns)) {
        $columns = admin_get_columns($db, 'newsletter');
    }

    if (empty($columns)) {
        $columns = ['email'];
    }

    $selectClause = implode(', ', array_map(fn ($col) => "`$col`", $columns));
    $orderField = admin_preferred_order_field($columns, ['created_at', 'updated_at', 'id']);

    try {
        $stmt = $db->query("SELECT $selectClause FROM newsletter ORDER BY `$orderField` DESC");
        $entries = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (PDOException $e) {
        $error = 'Failed to load subscribers list';
    }
}

function format_newsletter_value(string $column, $value): string
{
    switch ($column) {
        case 'created_at':
        case 'updated_at':
            return admin_safe(admin_format_date($value, 'M d, Y H:i'));
        default:
            return admin_safe($value ?? '');
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
              <h4 class="card-title card-title-dash">Newsletter Subscribers</h4>
              <p class="card-subtitle card-subtitle-dash">Manage all newsletter subscribers</p>
            </div>
            <div>
              <a href="?route=newsletter&action=export" class="btn btn-primary btn-lg text-white mb-0 me-0"><i class="mdi mdi-download"></i>Export Subscribers</a>
            </div>
          </div>
          
          <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
              <i class="mdi mdi-check-circle-outline"></i> <?php echo admin_safe($message); ?>
            </div>
          <?php endif; ?>
          
          <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
              <i class="mdi mdi-alert-circle"></i> <?php echo admin_safe($error); ?>
            </div>
          <?php endif; ?>
          
          <?php if (!$tableExists): ?>
            <div class="alert alert-warning" role="alert">
              <i class="mdi mdi-database-alert"></i> The newsletter table is missing. Run the latest database migrations from <code>database/schema_updates.sql</code>.
            </div>
          <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <?php foreach ($columns as $column): ?>
                            <th><?php echo admin_safe($columnLabels[$column] ?? ucfirst(str_replace('_', ' ', $column))); ?></th>
                        <?php endforeach; ?>
                        <?php if ($hasIdColumn): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                  <?php if (empty($entries)): ?>
                    <tr>
                      <td colspan="<?php echo count($columns) + ($hasIdColumn ? 1 : 0); ?>" class="text-center py-4 text-muted">
                        <i class="mdi mdi-email-newsletter mdi-48px d-block mb-2"></i>
                        No subscribers found
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($entries as $row): ?>
                      <tr>
                        <?php foreach ($columns as $column): ?>
                          <?php if ($column === 'email'): ?>
                            <td>
                              <div class="d-flex align-items-center">
                                <div class="rounded-circle profile-image-small bg-primary d-flex align-items-center justify-content-center text-white">
                                  <i class="mdi mdi-email"></i>
                                </div>
                                <div class="ms-3">
                                  <p class="mb-0 fw-medium"><?php echo format_newsletter_value($column, $row[$column] ?? ''); ?></p>
                                </div>
                              </div>
                            </td>
                          <?php else: ?>
                            <td><?php echo format_newsletter_value($column, $row[$column] ?? ''); ?></td>
                          <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($hasIdColumn): ?>
                          <td>
                            <div class="d-flex">
                              <a href="?route=newsletter&action=delete&id=<?php echo admin_safe($row['id']); ?>" 
                                 class="btn btn-outline-danger btn-sm"
                                 onclick="return confirm('Are you sure you want to remove this subscriber?')">
                                <i class="mdi mdi-delete"></i>
                              </a>
                            </div>
                          </td>
                        <?php endif; ?>
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
  $('.dataTables_filter input').attr("placeholder", "Search subscribers...");
});
</script>
