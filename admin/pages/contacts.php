<?php
/**
 * KHODERS WORLD Admin Contacts Page
 * Displayed when accessing the contacts route
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Contact Messages - KHODERS WORLD Admin');
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

// Initialize variables
$currentPage = 'contacts';
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';
$contacts = [];
$columns = [];

// Get current user
$user = Auth::user();

// Database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    $error = 'Unable to connect to the database. Please verify database credentials and try again.';
}

$hasIdColumn = $db ? admin_table_has_column($db, 'contacts', 'id') : false;

if ($db && $action === 'delete' && $hasIdColumn && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare('DELETE FROM contacts WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $message = 'Contact message deleted successfully';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Failed to delete contact message';
    }
}

$columnLabels = [
    'id' => 'ID',
    'name' => 'Name',
    'email' => 'Email',
    'phone' => 'Phone',
    'subject' => 'Subject',
    'message' => 'Message',
    'ip_address' => 'IP Address',
    'created_at' => 'Received',
    'updated_at' => 'Updated'
];

if ($db) {
    $columns = admin_filter_columns(
        $db,
        'contacts',
        ['id', 'name', 'email', 'phone', 'subject', 'message', 'ip_address', 'created_at', 'updated_at'],
        ['id', 'name', 'email', 'subject', 'message', 'created_at']
    );

    if (empty($columns)) {
        $columns = ['id', 'name', 'email'];
    }

    $selectClause = implode(', ', array_map(fn ($col) => "`$col`", $columns));
    $orderField = in_array('created_at', $columns, true)
        ? '`created_at`'
        : (in_array('updated_at', $columns, true)
            ? '`updated_at`'
            : '`' . $columns[0] . '`');

    try {
        $stmt = $db->query("SELECT $selectClause FROM contacts ORDER BY $orderField DESC");
        $contacts = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (PDOException $e) {
        $error = 'Failed to fetch contact messages';
    }
}

function format_contact_value(string $column, $value): string
{
    switch ($column) {
        case 'message':
        case 'subject':
            return admin_excerpt($value, 80);
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
              <h4 class="card-title card-title-dash">Contact Messages</h4>
              <p class="card-subtitle card-subtitle-dash">Manage all contact form submissions</p>
            </div>
            <div>
              <a href="?route=contacts&action=export" class="btn btn-primary btn-lg text-white mb-0 me-0"><i class="mdi mdi-download"></i>Export Messages</a>
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
                <?php if (empty($contacts)): ?>
                  <tr>
                    <td colspan="<?php echo count($columns) + ($hasIdColumn ? 1 : 0); ?>" class="text-center py-4 text-muted">
                      <i class="mdi mdi-email-outline mdi-48px d-block mb-2"></i>
                      No contact messages found
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($contacts as $contact): ?>
                    <tr>
                      <?php foreach ($columns as $column): ?>
                        <td>
                          <?php if ($column === 'message'): ?>
                            <details>
                              <summary><?php echo format_contact_value($column, $contact[$column] ?? ''); ?></summary>
                              <div class="message-full"><?php echo admin_safe($contact[$column] ?? ''); ?></div>
                            </details>
                          <?php elseif ($column === 'name'): ?>
                            <div class="d-flex align-items-center">
                              <div class="rounded-circle profile-image-small bg-info d-flex align-items-center justify-content-center text-white">
                                <?php echo strtoupper(substr(($contact[$column] ?? 'U'), 0, 1)); ?>
                              </div>
                              <div class="ms-3">
                                <p class="mb-0 fw-medium"><?php echo format_contact_value($column, $contact[$column] ?? ''); ?></p>
                              </div>
                            </div>
                          <?php else: ?>
                            <?php echo format_contact_value($column, $contact[$column] ?? ''); ?>
                          <?php endif; ?>
                        </td>
                      <?php endforeach; ?>
                      <?php if ($hasIdColumn): ?>
                        <td>
                          <div class="d-flex">
                            <a href="?route=contacts&action=view&id=<?php echo admin_safe($contact['id']); ?>" class="btn btn-outline-primary btn-sm me-2">
                              <i class="mdi mdi-eye"></i>
                            </a>
                            <a href="?route=contacts&action=delete&id=<?php echo admin_safe($contact['id']); ?>" 
                              class="btn btn-outline-danger btn-sm"
                              onclick="return confirm('Are you sure you want to delete this message?')">
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
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .message-full { margin-top: 0.5rem; padding: 0.75rem; background-color: #f9fafb; border-radius: 0.5rem; color: #4B5563; }
  details summary { cursor: pointer; color: #4B49AC; font-weight: 500; }
</style>

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
  $('.dataTables_filter input').attr("placeholder", "Search messages...");
});
</script>
