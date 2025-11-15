<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../config/security.php';
require_once __DIR__ . '/includes/admin_helpers.php';

Auth::requireAuth('login.php');


$database = new Database();
$db = $database->getConnection();

$currentPage = 'members';
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';
$members = [];

if (!$db) {
    $error = 'Unable to connect to the database. Please verify database credentials and try again.';
}

// Handle delete action
if ($db) {
    if ($action === 'delete' && isset($_GET['id'])) {
        try {
            $stmt = $db->prepare("DELETE FROM members WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $message = 'Member deleted successfully';
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Failed to delete member';
        }
    }

    // Handle edit action
    if ($action === 'edit' && isset($_GET['id'])) {
        // Update existing member with enhanced fields
        $stmt = $db->prepare("UPDATE members SET first_name = ?, last_name = ?, 
                              email = ?, phone = ?, student_id = ?, program = ?, 
                              year = ?, experience = ?, interests = ?, 
                              additional_info = ?, updated_at = NOW() 
                              WHERE id = ?");
        if ($stmt->execute([$first_name, $last_name, $email, $phone, $student_id, 
                           $program, $year, $experience, $interests, 
                           $additional_info, $_GET['id']])) {
            $message = 'Member updated successfully';
            $action = 'list'; // Return to list view
        } else {
            $error = 'Failed to update member';
        }
    }

    // Get all members with enhanced fields from our updated schema
    try {
        // Query with our enhanced members table structure
        $stmt = $db->prepare("SELECT id, first_name, last_name, email, phone, student_id, 
                            program, year, experience, interests, registration_date, ip_address 
                            FROM members ORDER BY registration_date DESC");
        $stmt->execute();
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = 'Failed to fetch members';
    }
}

$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Management - KHODERS Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #F3F4F6; }
        
        /* Sidebar - Same as dashboard */
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
        
        /* Main Content */
        .main-content { margin-left: 260px; min-height: 100vh; }
        .topbar { background-color: #FFFFFF; border-bottom: 1px solid #E5E7EB; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .topbar h1 { font-size: 1.5rem; color: #1F2937; }
        .content-area { padding: 2rem; }
        
        /* Alert Messages */
        .alert { padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
        .alert-success { background-color: #D1FAE5; color: #065F46; }
        .alert-error { background-color: #FEE2E2; color: #991B1B; }
        
        /* Table */
        .table-container { background: #FFFFFF; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        .table-header { padding: 1.5rem; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; }
        .table-header h2 { font-size: 1.25rem; color: #1F2937; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #F9FAFB; padding: 0.75rem 1.5rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #6B7280; }
        td { padding: 1rem 1.5rem; border-top: 1px solid #E5E7EB; font-size: 0.875rem; color: #374151; }
        tr:hover { background-color: #F9FAFB; }
        
        /* Badges */
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
        .badge-beginner { background-color: #DBEAFE; color: #1E40AF; }
        .badge-some-experience { background-color: #D1FAE5; color: #065F46; }
        .badge-intermediate { background-color: #FEF3C7; color: #92400E; }
        .badge-advanced { background-color: #E9D5FF; color: #6B21A8; }
        
        /* Buttons */
        .btn { padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; transition: all 0.2s ease; border: none; }
        .btn-primary { background-color: #2A4E6D; color: #FFFFFF; }
        .btn-primary:hover { background-color: #1A3E5D; }
        .btn-danger { background-color: #EF4444; color: #FFFFFF; }
        .btn-danger:hover { background-color: #DC2626; }
        .btn-sm { padding: 0.375rem 0.75rem; font-size: 0.8125rem; }
        
        /* Mobile */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .content-area { padding: 1rem; }
            .topbar { padding: 1rem; }
            table { font-size: 0.8125rem; }
            th, td { padding: 0.5rem; }
        }
        
        .mobile-menu-toggle { display: none; position: fixed; bottom: 1rem; right: 1rem; width: 56px; height: 56px; background-color: #2A4E6D; color: #FFFFFF; border: none; border-radius: 50%; font-size: 1.5rem; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 999; }
        @media (max-width: 768px) {
            .mobile-menu-toggle { display: flex; align-items: center; justify-content: center; }
        }
        
        .interests-list { display: flex; flex-wrap: wrap; gap: 0.25rem; }
        .interest-tag { background-color: #E5E7EB; color: #374151; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; }
    </style>
</head>
<body>
    <!-- Sidebar -->
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
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="topbar">
            <h1>👥 Members Management</h1>
        </div>
        
        <div class="content-area">
            <?php if ($message): ?>
                <div class="alert alert-success">✓ <?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="table-container">
                <div class="table-header">
                    <h2>All Members (<?php echo count($members); ?>)</h2>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Program / Year</th>
                            <th>Experience</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem; color: #9CA3AF;">
                                    No members found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?php echo admin_safe($member['id']); ?></td>
                                    <td><strong><?php echo admin_safe(trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''))); ?></strong></td>
                                    <td><?php echo admin_safe($member['email'] ?? ''); ?></td>
                                    <td><?php echo admin_safe($member['phone'] ?? ''); ?></td>
                                    <td><?php echo admin_safe(($member['program'] ?? '') . ' / ' . ($member['year'] ?? '')); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo admin_safe(strtolower($member['experience'])); ?>">
                                            <?php echo admin_safe(ucfirst(str_replace('-', ' ', strtolower($member['experience'] ?? '')))); ?>
                                        </span>
                                    </td>
                                    <td><?php echo admin_format_date($member['registration_date'] ?? null); ?></td>
                                    <td>
                                        <a href="?action=delete&id=<?php echo admin_safe($member['id']); ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to delete this member?')">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" onclick="toggleSidebar()">☰</button>
    
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
        
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !toggle.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>

