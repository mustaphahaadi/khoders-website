<?php
/**
 * KHODERS WORLD Admin Projects Page
 * Displayed when accessing the projects route
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Projects - KHODERS WORLD Admin');
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

// Initialize variables
$currentPage = 'projects';
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';
$projects = [];
$columns = [];

// Get current user
$user = Auth::user();

// Database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    $error = 'Unable to connect to the database. Please verify database credentials and try again.';
}

$tableExists = $db ? admin_table_exists($db, 'projects') : false;
$hasIdColumn = $tableExists ? admin_table_has_column($db, 'projects', 'id') : false;

if ($db && $tableExists && $action === 'delete' && $hasIdColumn && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare('DELETE FROM projects WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $message = 'Project deleted successfully';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Failed to delete project';
    }
}

$columnLabels = [
    'id' => 'ID',
    'title' => 'Title',
    'description' => 'Description',
    'image_url' => 'Image URL',
    'tech_stack' => 'Tech Stack',
    'github_url' => 'GitHub',
    'demo_url' => 'Demo',
    'created_at' => 'Created',
    'updated_at' => 'Updated',
];

if ($db && $tableExists) {
    $columns = admin_filter_columns(
        $db,
        'projects',
        ['id', 'title', 'description', 'image_url', 'tech_stack', 'github_url', 'demo_url', 'created_at', 'updated_at'],
        ['id', 'title', 'description', 'created_at']
    );

    if (empty($columns)) {
        $columns = admin_get_columns($db, 'projects');
    }

    if (empty($columns)) {
        $columns = ['title'];
    }

    $selectClause = implode(', ', array_map(fn ($col) => "`$col`", $columns));
    $orderField = admin_preferred_order_field($columns, ['created_at', 'updated_at', 'id']);

    try {
        $stmt = $db->query("SELECT $selectClause FROM projects ORDER BY `$orderField` DESC");
        $projects = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (PDOException $e) {
        $error = 'Failed to load projects';
    }
}

function format_project_value(string $column, $value): string
{
    switch ($column) {
        case 'description':
            return admin_excerpt($value, 120);
        case 'tech_stack':
            $items = $value;
            if (is_string($value)) {
                $items = admin_decode_json($value);
            }
            if (is_array($items)) {
                $badges = array_map(fn ($item) => '<span class="tech-badge">' . admin_safe($item) . '</span>', $items);
                return implode(' ', $badges);
            }
            return admin_safe($value ?? '');
        case 'github_url':
        case 'demo_url':
            if (empty($value)) {
                return '—';
            }
            $url = admin_safe($value);
            return '<a href="' . $url . '" target="_blank" rel="noopener" class="link">' . $url . '</a>';
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
              <h4 class="card-title card-title-dash">Projects</h4>
              <p class="card-subtitle card-subtitle-dash">Manage KHODERS WORLD projects</p>
            </div>
            <div>
              <a href="?route=projects&action=add" class="btn btn-primary btn-lg text-white mb-0 me-0"><i class="mdi mdi-plus"></i>Add Project</a>
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
              <i class="mdi mdi-database-alert"></i> The projects table is missing. Run the latest database migrations from <code>database/schema_updates.sql</code>.
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
                <?php if (empty($projects)): ?>
                  <tr>
                    <td colspan="<?php echo count($columns) + ($hasIdColumn ? 1 : 0); ?>" class="text-center py-4 text-muted">
                      <i class="mdi mdi-laptop mdi-48px d-block mb-2"></i>
                      No projects found
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($projects as $project): ?>
                    <tr>
                      <?php foreach ($columns as $column): ?>
                        <td>
                          <?php if ($column === 'description'): ?>
                            <details>
                              <summary><?php echo format_project_value($column, $project[$column] ?? ''); ?></summary>
                              <div class="description-full"><?php echo admin_safe($project[$column] ?? ''); ?></div>
                            </details>
                          <?php elseif ($column === 'title'): ?>
                            <div class="d-flex align-items-center">
                              <div class="rounded-circle profile-image-small bg-primary d-flex align-items-center justify-content-center text-white">
                                <i class="mdi mdi-laptop"></i>
                              </div>
                              <div class="ms-3">
                                <p class="mb-0 fw-bold"><?php echo format_project_value($column, $project[$column] ?? ''); ?></p>
                              </div>
                            </div>
                          <?php else: ?>
                            <?php echo format_project_value($column, $project[$column] ?? ''); ?>
                          <?php endif; ?>
                        </td>
                      <?php endforeach; ?>
                      <?php if ($hasIdColumn): ?>
                        <td>
                          <div class="d-flex">
                            <a href="?route=projects&action=edit&id=<?php echo admin_safe($project['id']); ?>" class="btn btn-outline-primary btn-sm me-2">
                              <i class="mdi mdi-pencil"></i>
                            </a>
                            <a href="?route=projects&action=delete&id=<?php echo admin_safe($project['id']); ?>" 
                              class="btn btn-outline-danger btn-sm"
                              onclick="return confirm('Are you sure you want to delete this project?')">
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

<style>
  .description-full { margin-top: 0.5rem; padding: 0.75rem; background-color: #f9fafb; border-radius: 0.5rem; color: #4B5563; }
  details summary { cursor: pointer; color: #4B49AC; font-weight: 500; }
  .tech-badge { display: inline-block; background-color: #E0E7FF; color: #3730A3; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; margin: 0.125rem; }
  .link { color: #4B49AC; text-decoration: none; }
  .link:hover { text-decoration: underline; }
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
  $('.dataTables_filter input').attr("placeholder", "Search projects...");
});
</script>
