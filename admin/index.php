<?php
session_start();

require_once '../config/auth.php';
require_once '../config/database.php';
require_once '../config/security.php';
require_once __DIR__ . '/includes/admin_helpers.php';

Auth::requireAuth('login.php');

$database = new Database();
$db = $database->getConnection();

$currentPage = 'dashboard';

$stats = [
    'members_total' => 0,
    'members_week' => 0,
    'contacts_total' => 0,
    'newsletter_total' => 0,
    'form_logs_today' => 0,
];

$recentMembers = [];
$recentLogs = [];
$errors = [];

if ($db) {
    try {
        $stats['members_total'] = (int) $db->query('SELECT COUNT(*) FROM members')->fetchColumn();
        $stats['members_week'] = (int) $db->query("SELECT COUNT(*) FROM members WHERE registration_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
        $stats['contacts_total'] = (int) $db->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
        $stats['newsletter_total'] = (int) $db->query('SELECT COUNT(*) FROM newsletter')->fetchColumn();
        $stats['form_logs_today'] = (int) $db->query('SELECT COUNT(*) FROM form_logs WHERE DATE(created_at) = CURDATE()')->fetchColumn();

        $memberStmt = $db->query('SELECT first_name, last_name, email, registration_date FROM members ORDER BY registration_date DESC LIMIT 5');
        $recentMembers = $memberStmt ? $memberStmt->fetchAll(PDO::FETCH_ASSOC) : [];

        $logStmt = $db->query('SELECT form_type, status, email, created_at FROM form_logs ORDER BY created_at DESC LIMIT 5');
        $recentLogs = $logStmt ? $logStmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (PDOException $e) {
        $errors[] = 'Failed to load dashboard statistics.';
        error_log('[ADMIN] Dashboard load failed: ' . $e->getMessage());
    }
} else {
    $errors[] = 'Could not connect to the database.';
}

$user = Auth::user();

function badgeClass($status) {
    switch ($status) {
        case 'success':
            return 'badge-success';
        case 'error':
            return 'badge-error';
        case 'spam':
            return 'badge-warning';
        default:
            return 'badge-neutral';
    }
}

function formatDate(?string $value): string {
    if (!$value) {
        return '—';
    }

    $timestamp = strtotime($value);
    return $timestamp ? date('M d, Y', $timestamp) : '—';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - KHODERS Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #F3F4F6; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background-color: #1F2937; color: #FFFFFF; padding: 1.5rem 1rem; display: flex; flex-direction: column; }
        .sidebar h2 { font-size: 1.5rem; margin-bottom: 0.25rem; }
        .sidebar p { color: #9CA3AF; margin-bottom: 1.5rem; }
        .nav-section { margin-bottom: 1.5rem; }
        .nav-title { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.75rem 0.75rem; color: #9CA3AF; }
        .nav-link { display: flex; align-items: center; color: #D1D5DB; text-decoration: none; padding: 0.75rem 1rem; border-radius: 0.5rem; transition: background-color 0.2s ease; }
        .nav-link.active, .nav-link:hover { background-color: #2A4E6D; color: #FFFFFF; }
        .nav-link span { margin-right: 0.75rem; }
        .sidebar-footer { margin-top: auto; border-top: 1px solid #374151; padding-top: 1rem; }
        .user-info { display: flex; align-items: center; margin-bottom: 0.75rem; }
        .avatar { width: 40px; height: 40px; border-radius: 50%; background-color: #2A4E6D; display: flex; align-items: center; justify-content: center; font-weight: 600; margin-right: 0.75rem; }
        .logout-btn { width: 100%; padding: 0.5rem; background-color: #374151; color: #FFFFFF; border: none; border-radius: 0.375rem; cursor: pointer; font-size: 0.875rem; }
        .logout-btn:hover { background-color: #4B5563; }
        .content { flex: 1; padding: 2rem; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .topbar h1 { font-size: 1.75rem; color: #1F2937; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: #FFFFFF; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08); }
        .stat-label { font-size: 0.85rem; color: #6B7280; margin-bottom: 0.5rem; }
        .stat-value { font-size: 1.75rem; font-weight: 700; color: #111827; }
        .section { background: #FFFFFF; border-radius: 0.75rem; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08); margin-bottom: 2rem; }
        .section-header { padding: 1.5rem; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; }
        .section-title { font-size: 1.25rem; color: #111827; }
        .section-body { padding: 1.5rem; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { text-align: left; padding: 0.75rem 0.5rem; font-size: 0.9rem; border-bottom: 1px solid #E5E7EB; color: #374151; }
        .table th { color: #6B7280; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; }
        .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background-color: #D1FAE5; color: #065F46; }
        .badge-error { background-color: #FEE2E2; color: #B91C1C; }
        .badge-warning { background-color: #FEF3C7; color: #92400E; }
        .badge-neutral { background-color: #E5E7EB; color: #374151; }
        .error { background-color: #FEE2E2; color: #B91C1C; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; }
        @media (max-width: 960px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; flex-direction: row; flex-wrap: wrap; }
            .sidebar-footer { width: 100%; }
            .content { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div>
                <h2>🎓 KHODERS</h2>
                <p>Admin Dashboard</p>
            </div>
            <nav class="nav">
                <div class="nav-section">
                    <div class="nav-title">Main</div>
                    <a href="index.php" class="nav-link<?= admin_nav_active($currentPage, 'dashboard'); ?>"><span>📊</span>Dashboard</a>
                </div>
                <div class="nav-section">
                    <div class="nav-title">Management</div>
                    <a href="members.php" class="nav-link<?= admin_nav_active($currentPage, 'members'); ?>"><span>👥</span>Members</a>
                    <a href="contacts.php" class="nav-link<?= admin_nav_active($currentPage, 'contacts'); ?>"><span>✉️</span>Contacts</a>
                    <a href="newsletter.php" class="nav-link<?= admin_nav_active($currentPage, 'newsletter'); ?>"><span>📧</span>Newsletter</a>
                    <a href="events.php" class="nav-link<?= admin_nav_active($currentPage, 'events'); ?>"><span>📅</span>Events</a>
                    <a href="projects.php" class="nav-link<?= admin_nav_active($currentPage, 'projects'); ?>"><span>💼</span>Projects</a>
                    <a href="form-logs.php" class="nav-link<?= admin_nav_active($currentPage, 'form-logs'); ?>"><span>📝</span>Form Logs</a>
                </div>
                <div class="nav-section">
                    <div class="nav-title">System</div>
                    <a href="../test-db.php" target="_blank" class="nav-link"><span>🛠</span>Database Test</a>
                    <a href="../index.html" target="_blank" class="nav-link"><span>🌐</span>View Website</a>
                </div>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="avatar"><?php echo strtoupper(substr($user['username'] ?? 'A', 0, 1)); ?></div>
                    <div>
                        <div style="font-weight:600; font-size:0.9rem;">
                            <?php echo htmlspecialchars($user['username'] ?? 'Admin'); ?>
                        </div>
                        <div style="font-size:0.75rem; color:#9CA3AF;">
                            <?php echo htmlspecialchars(ucfirst($user['role'] ?? 'admin')); ?>
                        </div>
                    </div>
                </div>
                <form action="logout.php" method="POST">
                    <button type="submit" class="logout-btn">🚪 Logout</button>
                </form>
            </div>
        </aside>
        <main class="content">
            <div class="topbar">
                <h1>Welcome back<?php echo $user ? ', ' . htmlspecialchars($user['username']) : ''; ?> 👋</h1>
                <div style="color:#6B7280; font-size:0.95rem;">
                    <?php echo 'Last login: ' . formatDate($user['login_time'] ? date('Y-m-d H:i:s', $user['login_time']) : null); ?>
                </div>
            </div>

            <?php foreach ($errors as $error): ?>
                <div class="error">⚠️ <?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>

            <section class="stats-grid">
                <article class="stat-card">
                    <div class="stat-label">Total Members</div>
                    <div class="stat-value"><?php echo number_format($stats['members_total']); ?></div>
                </article>
                <article class="stat-card">
                    <div class="stat-label">New This Week</div>
                    <div class="stat-value"><?php echo number_format($stats['members_week']); ?></div>
                </article>
                <article class="stat-card">
                    <div class="stat-label">Contact Messages</div>
                    <div class="stat-value"><?php echo number_format($stats['contacts_total']); ?></div>
                </article>
                <article class="stat-card">
                    <div class="stat-label">Newsletter Subscribers</div>
                    <div class="stat-value"><?php echo number_format($stats['newsletter_total']); ?></div>
                </article>
                <article class="stat-card">
                    <div class="stat-label">Form Logs Today</div>
                    <div class="stat-value"><?php echo number_format($stats['form_logs_today']); ?></div>
                </article>
            </section>

            <section class="section">
                <div class="section-header">
                    <h2 class="section-title">Recent Members</h2>
                    <a href="members.php" class="nav-link" style="padding:0.5rem 1rem;">View All →</a>
                </div>
                <div class="section-body">
                    <?php if (empty($recentMembers)): ?>
                        <p style="color:#6B7280;">No members found yet.</p>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentMembers as $member): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')); ?></td>
                                        <td><?php echo htmlspecialchars($member['email'] ?? ''); ?></td>
                                        <td><?php echo formatDate($member['registration_date'] ?? null); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </section>

            <section class="section">
                <div class="section-header">
                    <h2 class="section-title">Latest Form Activity</h2>
                    <a href="form-logs.php" class="nav-link" style="padding:0.5rem 1rem;">View Logs →</a>
                </div>
                <div class="section-body">
                    <?php if (empty($recentLogs)): ?>
                        <p style="color:#6B7280;">No form submissions logged yet.</p>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Form</th>
                                    <th>Status</th>
                                    <th>Email</th>
                                    <th>Received</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentLogs as $log): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(strtoupper($log['form_type'] ?? '')); ?></td>
                                        <td>
                                            <span class="badge <?php echo badgeClass($log['status'] ?? ''); ?>">
                                                <?php echo htmlspecialchars(ucfirst($log['status'] ?? '')); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($log['email'] ?? ''); ?></td>
                                        <td><?php echo formatDate($log['created_at'] ?? null); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </section>

        </main>
    </div>
</body>
</html>
