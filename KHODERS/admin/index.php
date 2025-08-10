<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get stats
$stats = [];

try {
    $queries = [
        'members' => "SELECT COUNT(*) as count FROM members",
        'contacts' => "SELECT COUNT(*) as count FROM contacts",
        'newsletter' => "SELECT COUNT(*) as count FROM newsletter",
        'events' => "SELECT COUNT(*) as count FROM events WHERE date >= CURDATE()"
    ];
    
    foreach ($queries as $key => $query) {
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats[$key] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
} catch(PDOException $e) {
    $stats = ['members' => 0, 'contacts' => 0, 'newsletter' => 0, 'events' => 0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KHODERS Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-container { max-width: 1200px; margin: 100px auto; padding: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 3rem; font-weight: bold; color: var(--primary-color); }
        .stat-label { color: var(--gray-600); margin-top: 10px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="../assets/qwe.png" alt="KHODERS Logo" width="50" height="50">
                <span class="logo-text">KHODERS ADMIN</span>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <h1>Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['members']; ?></div>
                <div class="stat-label">Total Members</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['contacts']; ?></div>
                <div class="stat-label">Contact Messages</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['newsletter']; ?></div>
                <div class="stat-label">Newsletter Subscribers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['events']; ?></div>
                <div class="stat-label">Upcoming Events</div>
            </div>
        </div>
    </div>
</body>
</html>