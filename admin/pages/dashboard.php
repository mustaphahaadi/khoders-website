<?php
/**
 * KHODERS WORLD Enhanced Admin Dashboard
 * Comprehensive metrics and analytics
 */

if (!defined('PAGE_TITLE')) {
    require_once '../includes/router.php';
    Router::notFound(function() {
        echo '<h1>404 Not Found</h1>';
        echo '<p>Direct access to this page is not allowed.</p>';
    });
    Router::execute404();
    exit;
}

require_once __DIR__ . '/../includes/dashboard.php';

$dashboardHelper = new Dashboard();
$stats = $dashboardHelper->getStats();
$recentMembers = $dashboardHelper->getRecentMembers(5);
$recentEnrollments = $dashboardHelper->getRecentEnrollments(10);
$recentLogs = $dashboardHelper->getRecentLogs(5);
$monthlyStats = $dashboardHelper->getMonthlyStats(6);
$enrollmentTrends = $dashboardHelper->getEnrollmentTrends(6);
$contentDistribution = $dashboardHelper->getContentDistribution();

$user = Auth::user();

function formatDate(?string $value): string {
    if (!$value) return '—';
    $timestamp = strtotime($value);
    return $timestamp ? date('M d, Y', $timestamp) : '—';
}
?>

<div class="content-wrapper">
    <!-- Welcome Banner -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-2">Welcome back, <?php echo htmlspecialchars($user['username'] ?? 'Admin'); ?>! 👋</h3>
                            <p class="mb-0 opacity-75">Here's what's happening with Khoders World today</p>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0"><?php echo date('l'); ?></h5>
                            <p class="mb-0"><?php echo date('F j, Y'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="row">
        <!-- Members -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Members</p>
                            <h3 class="mb-0"><?php echo number_format($stats['members_total']); ?></h3>
                            <small class="text-success"><i class="bi bi-arrow-up"></i> +<?php echo $stats['members_week']; ?> this week</small>
                        </div>
                        <div class="icon-shape bg-primary text-white rounded-circle">
                            <i class="bi bi-people" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Upcoming Events</p>
                            <h3 class="mb-0"><?php echo number_format($stats['events_upcoming']); ?></h3>
                            <small class="text-muted"><?php echo $stats['events_total']; ?> total</small>
                        </div>
                        <div class="icon-shape bg-success text-white rounded-circle">
                            <i class="bi bi-calendar-event" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollments -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Enrollments</p>
                            <h3 class="mb-0"><?php echo number_format($stats['enrollments_total']); ?></h3>
                            <small class="text-success"><i class="bi bi-arrow-up"></i> +<?php echo $stats['enrollments_week']; ?> this week</small>
                        </div>
                        <div class="icon-shape bg-warning text-white rounded-circle">
                            <i class="bi bi-person-check" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Courses -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Active Courses</p>
                            <h3 class="mb-0"><?php echo number_format($stats['courses_active']); ?></h3>
                            <small class="text-muted"><?php echo $stats['courses_total']; ?> total</small>
                        </div>
                        <div class="icon-shape bg-info text-white rounded-circle">
                            <i class="bi bi-book" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="row">
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="bi bi-laptop text-primary mb-2" style="font-size: 2rem;"></i>
                    <h5 class="mb-0"><?php echo $stats['projects_total']; ?></h5>
                    <small class="text-muted">Projects</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="bi bi-file-text text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="mb-0"><?php echo $stats['blog_posts_published']; ?></h5>
                    <small class="text-muted">Blog Posts</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="bi bi-people-fill text-warning mb-2" style="font-size: 2rem;"></i>
                    <h5 class="mb-0"><?php echo $stats['team_members_total']; ?></h5>
                    <small class="text-muted">Team Members</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="bi bi-code-square text-info mb-2" style="font-size: 2rem;"></i>
                    <h5 class="mb-0"><?php echo $stats['skills_total']; ?></h5>
                    <small class="text-muted">Tech Skills</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="bi bi-journal-code text-danger mb-2" style="font-size: 2rem;"></i>
                    <h5 class="mb-0"><?php echo $stats['resources_total']; ?></h5>
                    <small class="text-muted">Resources</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="bi bi-envelope text-secondary mb-2" style="font-size: 2rem;"></i>
                    <h5 class="mb-0"><?php echo $stats['newsletter_total']; ?></h5>
                    <small class="text-muted">Subscribers</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Member Growth Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Member Growth Trend</h5>
                        <span class="badge bg-primary">Last 6 Months</span>
                    </div>
                    <canvas id="memberGrowthChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Content Distribution -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Content Distribution</h5>
                    <canvas id="contentChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row">
        <!-- Recent Enrollments -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Enrollments</h5>
                    <a href="?route=enrollments" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentEnrollments)): ?>
                        <p class="text-muted text-center py-4">No enrollments yet</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($recentEnrollments, 0, 5) as $enrollment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']); ?></td>
                                            <td><span class="badge bg-info"><?php echo ucfirst($enrollment['enrollment_type']); ?></span></td>
                                            <td><?php echo formatDate($enrollment['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Members -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Members</h5>
                    <a href="?route=members" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentMembers)): ?>
                        <p class="text-muted text-center py-4">No members yet</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
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
                                            <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($member['email']); ?></td>
                                            <td><?php echo formatDate($member['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
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
.icon-shape {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #136ad5 0%, #0d4fa3 100%) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Member Growth Chart
    var memberCtx = document.getElementById('memberGrowthChart').getContext('2d');
    var monthlyData = <?php echo json_encode($monthlyStats); ?>;
    
    new Chart(memberCtx, {
        type: 'line',
        data: {
            labels: monthlyData.labels.length ? monthlyData.labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'New Members',
                data: monthlyData.data.length ? monthlyData.data : [0, 0, 0, 0, 0, 0],
                borderColor: '#136ad5',
                backgroundColor: 'rgba(19, 106, 213, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // Content Distribution Chart
    var contentCtx = document.getElementById('contentChart').getContext('2d');
    var contentData = <?php echo json_encode($contentDistribution); ?>;
    
    new Chart(contentCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(contentData),
            datasets: [{
                data: Object.values(contentData),
                backgroundColor: ['#136ad5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
