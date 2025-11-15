<?php
session_start();

require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../config/security.php';
require_once __DIR__ . '/includes/admin_helpers.php';

Auth::requireAuth('login.php');

$database = new Database();
$db = $database->getConnection();

$currentPage = 'projects';
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';
$projects = [];
$columns = [];

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

$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - KHODERS Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #F3F4F6; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background-color: #1F2937; color: #FFFFFF; overflow-y: auto; z-index: 1000; transition: transform 0.3s ease; }
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid #374151; }
        .sidebar-header h2 { font-size: 1.25rem; color: #FFFFFF; margin-bottom: 0.25rem; }
        .sidebar-header p { font-size: 0.875rem; color: #9CA3AF; }
        .sidebar-menu { padding: 1rem 0; }
        .menu-section { margin-bottom: 1.5rem; }
        .menu-section-title { padding: 0.5rem 1.5rem; font-size: 0.75rem; font-weight: 600; color: #9CA3AF; text-transform: uppercase; }
        .menu-item { display: flex; align-items: center; padding: 0.75rem 1.5rem; color: #D1D5DB; text-decoration: none; transition: all 0.2s ease; }
        .menu-item:hover { background-color: #374151; color: #FFFFFF; }
        .menu-item.active { background-color: #2A4E6D; color: #FFFFFF; border-left: 4px solid #F1B521; }
        .menu-item-icon { margin-right: 0.75rem; font-size: 1.25rem; }
        .sidebar-footer { position: absolute; bottom: 0; left: 0; right: 0; padding: 1rem 1.5rem; border-top: 1px solid #374151; }
        .user-info { display: flex; align-items: center; margin-bottom: 0.75rem; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background-color: #2A4E6D; display: flex; align-items: center; justify-content: center; margin-right: 0.75rem; font-weight: 600; }
        .user-details { flex: 1; }
        .user-name { font-size: 0.875rem; font-weight: 600; color: #FFFFFF; }
        .user-role { font-size: 0.75rem; color: #9CA3AF; }
        .logout-btn { width: 100%; padding: 0.5rem; background-color: #374151; color: #FFFFFF; border: none; border-radius: 0.375rem; font-size: 0.875rem; cursor: pointer; }
        .logout-btn:hover { background-color: #4B5563; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .topbar { background-color: #FFFFFF; border-bottom: 1px solid #E5E7EB; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .topbar h1 { font-size: 1.5rem; color: #1F2937; }
        .content-area { padding: 2rem; }
        .alert { padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
        .alert-success { background-color: #D1FAE5; color: #065F46; }
        .alert-error { background-color: #FEE2E2; color: #991B1B; }
        .table-container { background: #FFFFFF; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        .table-header { padding: 1.5rem; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; }
        .table-header h2 { font-size: 1.25rem; color: #1F2937; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #F9FAFB; padding: 0.75rem 1.5rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #6B7280; }
        td { padding: 1rem 1.5rem; border-top: 1px solid #E5E7EB; font-size: 0.875rem; color: #374151; vertical-align: top; }
        tr:hover { background-color: #F9FAFB; }
        .actions { display: flex; gap: 0.5rem; }
        .btn { padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; transition: all 0.2s ease; border: none; }
        .btn-danger { background-color: #EF4444; color: #FFFFFF; }
        .btn-danger:hover { background-color: #DC2626; }
        .tech-badge { display: inline-block; background-color: #E0E7FF; color: #3730A3; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; margin: 0.125rem; }
        .link { color: #2563EB; text-decoration: none; }
        .link:hover { text-decoration: underline; }
        details summary { cursor: pointer; color: #2563EB; }
        .description-full { margin-top: 0.5rem; padding: 0.75rem; background-color: #F9FAFB; border-radius: 0.5rem; color: #4B5563; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .content-area { padding: 1rem; }
            table { font-size: 0.8125rem; }
            th, td { padding: 0.5rem; }
        }
        .mobile-menu-toggle { display: none; position: fixed; bottom: 1rem; right: 1rem; width: 56px; height: 56px; background-color: #2A4E6D; color: #FFFFFF; border: none; border-radius: 50%; font-size: 1.5rem; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 999; }
        @media (max-width: 768px) {
            .mobile-menu-toggle { display: flex; align-items: center; justify-content: center; }
        }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>🎓 KHODERS</h2>
            <p>Admin Dashboard</p>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title">Main</div>
                <a href="index.php" class="menu-item<?= admin_nav_active($currentPage, 'dashboard'); ?>">
                    <span class="menu-item-icon">📊</span>
                    Dashboard
                </a>
            </div>
            <div class="menu-section">
                <div class="menu-section-title">Management</div>
                <a href="members.php" class="menu-item<?= admin_nav_active($currentPage, 'members'); ?>">
                    <span class="menu-item-icon">👥</span>
                    Members
                </a>
                <a href="contacts.php" class="menu-item<?= admin_nav_active($currentPage, 'contacts'); ?>">
                    <span class="menu-item-icon">✉️</span>
                    Contact Messages
                </a>
                <a href="newsletter.php" class="menu-item<?= admin_nav_active($currentPage, 'newsletter'); ?>">
                    <span class="menu-item-icon">📧</span>
                    Newsletter
                </a>
                <a href="events.php" class="menu-item<?= admin_nav_active($currentPage, 'events'); ?>">
                    <span class="menu-item-icon">📅</span>
                    Events
                </a>
                <a href="projects.php" class="menu-item<?= admin_nav_active($currentPage, 'projects'); ?>">
                    <span class="menu-item-icon">💼</span>
                    Projects
                </a>
                <a href="form-logs.php" class="menu-item<?= admin_nav_active($currentPage, 'form-logs'); ?>">
                    <span class="menu-item-icon">📝</span>
                    Form Logs
                </a>
            </div>
            <div class="menu-section">
                <div class="menu-section-title">System</div>
                <a href="../index.php" target="_blank" class="menu-item">
                    <span class="menu-item-icon">🌐</span>
                    View Website
                </a>
            </div>
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar"><?php echo strtoupper(substr($user['username'] ?? 'A', 0, 1)); ?></div>
                <div class="user-details">
                    <div class="user-name"><?php echo admin_safe($user['username'] ?? 'Admin'); ?></div>
                    <div class="user-role"><?php echo admin_safe(ucfirst($user['role'] ?? 'administrator')); ?></div>
                </div>
            </div>
            <form action="logout.php" method="POST">
                <button type="submit" class="logout-btn">🚪 Logout</button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <h1>💼 Projects</h1>
            <div style="color:#6B7280; font-size:0.95rem;">
                <?php echo $user ? 'Logged in as ' . admin_safe($user['username']) : ''; ?>
            </div>
        </div>
        <div class="content-area">
            <?php if ($message): ?>
                <div class="alert alert-success">✓ <?php echo admin_safe($message); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error">⚠️ <?php echo admin_safe($error); ?></div>
            <?php endif; ?>

            <div class="table-container">
                <div class="table-header">
                    <h2>Projects (<?php echo count($projects); ?>)</h2>
                </div>
                <?php if (!$tableExists): ?>
                    <p style="padding: 1.5rem; color: #B91C1C;">The projects table is missing. Run the latest database migrations from <code>database/schema_updates.sql</code>.</p>
                <?php else: ?>
                    <table>
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
                                    <td colspan="<?php echo count($columns) + ($hasIdColumn ? 1 : 0); ?>" style="text-align:center; padding:2rem; color:#9CA3AF;">
                                        No projects found.
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
                                                <?php else: ?>
                                                    <?php echo format_project_value($column, $project[$column] ?? ''); ?>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <?php if ($hasIdColumn): ?>
                                            <td class="actions">
                                                <a href="?action=delete&id=<?php echo admin_safe($project['id']); ?>" class="btn btn-danger" onclick="return confirm('Delete this project?');">Delete</a>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <button class="mobile-menu-toggle" onclick="toggleSidebar()">☰</button>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            if (window.innerWidth <= 768 && !sidebar.contains(event.target) && !toggle.contains(event.target) && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>
