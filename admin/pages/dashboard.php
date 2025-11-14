<?php
/**
 * KHODERS WORLD Admin Dashboard
 * Main dashboard page for the admin panel
 */

// Check if directly accessed
if (!defined('PAGE_TITLE')) {
    require_once '../includes/router.php';
    Router::notFound(function() {
        echo '<h1>404 Not Found</h1>';
        echo '<p>Direct access to this page is not allowed.</p>';
    });
    Router::execute404();
    exit;
}

// Include dashboard helper
require_once __DIR__ . '/../includes/dashboard.php';

// Create dashboard instance
$dashboardHelper = new Dashboard();

// Get dashboard statistics
$stats = $dashboardHelper->getStats();

// Get recent members
$recentMembers = $dashboardHelper->getRecentMembers(5);

// Get recent form logs
$recentLogs = $dashboardHelper->getRecentLogs(5);

// Get monthly registration stats for chart
$monthlyStats = $dashboardHelper->getMonthlyStats(6);

// Get user info
$user = Auth::user();

// Helper functions
function badgeClass($status) {
    switch ($status) {
        case 'success':
            return 'badge bg-success';
        case 'error':
            return 'badge bg-danger';
        case 'spam':
            return 'badge bg-warning';
        default:
            return 'badge bg-secondary';
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

<div class="content-wrapper">
  <?php if (!empty($errors)): ?>
    <?php foreach ($errors as $error): ?>
      <div class="alert alert-danger" role="alert">
        <i class="mdi mdi-alert-circle"></i> <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
  
  <div class="row">
    <div class="col-sm-12">
      <div class="home-tab">
        <div class="d-sm-flex align-items-center justify-content-between border-bottom">
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-selected="true">Overview</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="members-tab" data-bs-toggle="tab" href="#members" role="tab" aria-selected="false">Members</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="activity-tab" data-bs-toggle="tab" href="#activity" role="tab" aria-selected="false">Activity</a>
            </li>
          </ul>
          <div>
            <div class="btn-wrapper">
              <a href="<?php echo Router::url('members'); ?>" class="btn btn-primary text-white me-0"><i class="icon-people"></i> Manage Members</a>
            </div>
          </div>
        </div>
        
        <div class="tab-content tab-content-basic">
          <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
            <div class="row">
              <div class="col-sm-12">
                <div class="statistics-details d-flex align-items-center justify-content-between">
                  <div>
                    <p class="statistics-title">Total Members</p>
                    <h3 class="rate-percentage"><?php echo number_format($stats['members_total']); ?></h3>
                    <p class="text-success d-flex"><i class="mdi mdi-account-multiple"></i><span>Active Members</span></p>
                  </div>
                  <div>
                    <p class="statistics-title">New This Week</p>
                    <h3 class="rate-percentage"><?php echo number_format($stats['members_week']); ?></h3>
                    <p class="text-success d-flex"><i class="mdi mdi-account-plus"></i><span>New Registrations</span></p>
                  </div>
                  <div>
                    <p class="statistics-title">Contact Messages</p>
                    <h3 class="rate-percentage"><?php echo number_format($stats['contacts_total']); ?></h3>
                    <p class="text-success d-flex"><i class="mdi mdi-message"></i><span>Total Inquiries</span></p>
                  </div>
                  <div class="d-none d-md-block">
                    <p class="statistics-title">Newsletter Subscribers</p>
                    <h3 class="rate-percentage"><?php echo number_format($stats['newsletter_total']); ?></h3>
                    <p class="text-success d-flex"><i class="mdi mdi-email-outline"></i><span>Subscribers</span></p>
                  </div>
                  <div class="d-none d-md-block">
                    <p class="statistics-title">Form Logs Today</p>
                    <h3 class="rate-percentage"><?php echo number_format($stats['form_logs_today']); ?></h3>
                    <p class="text-success d-flex"><i class="mdi mdi-file-document"></i><span>Today's Activity</span></p>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-8 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="d-sm-flex justify-content-between align-items-start">
                          <div>
                            <h4 class="card-title card-title-dash">Recent Members</h4>
                            <p class="card-subtitle card-subtitle-dash">Latest member registrations</p>
                          </div>
                          <div>
                            <a href="<?php echo Router::url('members'); ?>" class="btn btn-primary btn-lg text-white mb-0 me-0"><i class="mdi mdi-account-plus"></i>View All</a>
                          </div>
                        </div>
                        <div class="table-responsive mt-1">
                          <?php if (empty($recentMembers)): ?>
                            <p class="text-muted mb-0">No members found yet.</p>
                          <?php else: ?>
                            <table class="table select-table">
                              <thead>
                                <tr>
                                  <th>Member</th>
                                  <th>Email</th>
                                  <th>Joined</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($recentMembers as $member): ?>
                                  <tr>
                                    <td>
                                      <div class="d-flex">
                                        <div class="rounded-circle profile-image-small bg-primary d-flex align-items-center justify-content-center text-white">
                                          <?php echo strtoupper(substr(($member['first_name'] ?? 'U'), 0, 1)); ?>
                                        </div>
                                        <div>
                                          <h6><?php echo htmlspecialchars(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')); ?></h6>
                                        </div>
                                      </div>
                                    </td>
                                    <td><p><?php echo htmlspecialchars($member['email'] ?? ''); ?></p></td>
                                    <td><p><?php echo formatDate($member['registration_date'] ?? null); ?></p></td>
                                  </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="d-sm-flex justify-content-between align-items-start">
                          <div>
                            <h4 class="card-title card-title-dash">Latest Form Activity</h4>
                            <p class="card-subtitle card-subtitle-dash">Recent form submissions</p>
                          </div>
                          <div>
                            <a href="<?php echo Router::url('form-logs'); ?>" class="btn btn-primary btn-lg text-white mb-0 me-0"><i class="mdi mdi-file-document"></i>View Logs</a>
                          </div>
                        </div>
                        <div class="table-responsive mt-1">
                          <?php if (empty($recentLogs)): ?>
                            <p class="text-muted mb-0">No form submissions logged yet.</p>
                          <?php else: ?>
                            <table class="table select-table">
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
                                      <div class="badge <?php echo badgeClass($log['status'] ?? ''); ?>">
                                        <?php echo htmlspecialchars(ucfirst($log['status'] ?? '')); ?>
                                      </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($log['email'] ?? ''); ?></td>
                                    <td><?php echo formatDate($log['created_at'] ?? null); ?></td>
                                  </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-lg-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                              <h4 class="card-title card-title-dash">Member Growth</h4>
                            </div>
                            <canvas id="memberGrowthChart"></canvas>
                            <div class="mt-3">
                              <div class="wrapper d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="d-flex">
                                  <div class="circle-progress-width">
                                    <div class="circle-progress-position d-flex align-items-center justify-content-center">
                                      <i class="mdi mdi-account-multiple text-primary"></i>
                                    </div>
                                  </div>
                                  <div class="ms-2">
                                    <p class="mb-0">Total Members</p>
                                    <h6 class="mb-0"><?php echo number_format($stats['members_total']); ?></h6>
                                  </div>
                                </div>
                              </div>
                              <div class="wrapper d-flex align-items-center justify-content-between py-2">
                                <div class="d-flex">
                                  <div class="circle-progress-width">
                                    <div class="circle-progress-position-success d-flex align-items-center justify-content-center">
                                      <i class="mdi mdi-account-plus text-success"></i>
                                    </div>
                                  </div>
                                  <div class="ms-2">
                                    <p class="mb-0">New This Week</p>
                                    <h6 class="mb-0"><?php echo number_format($stats['members_week']); ?></h6>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Members Tab -->
          <div class="tab-pane fade" id="members" role="tabpanel" aria-labelledby="members-tab">
            <div class="card card-rounded">
              <div class="card-body">
                <h4 class="card-title">KHODERS WORLD Member Management</h4>
                <p class="card-description">
                  Manage all campus coding club members from this dashboard
                </p>
                <div class="text-center mt-4">
                  <a href="<?php echo Router::url('members'); ?>" class="btn btn-primary btn-lg text-white">Go to Member Management</a>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Activity Tab -->
          <div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="activity-tab">
            <div class="card card-rounded">
              <div class="card-body">
                <h4 class="card-title">KHODERS WORLD Activity Logs</h4>
                <p class="card-description">
                  Monitor all form submissions and website activity
                </p>
                <div class="text-center mt-4">
                  <a href="<?php echo Router::url('form-logs'); ?>" class="btn btn-primary btn-lg text-white">View Activity Logs</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Custom js for this page-->
<script>
// Member Growth Chart
document.addEventListener('DOMContentLoaded', function() {
  var ctx = document.getElementById('memberGrowthChart').getContext('2d');
  
  // Extract data from PHP
  var chartData = <?php echo json_encode($monthlyStats); ?>;
  var labels = chartData.labels || [];
  var data = chartData.data || [];
  
  // If no data, provide defaults
  if (labels.length === 0) {
    labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    data = [0, 0, 0, 0, 0, 0];
  }
  
  var memberGrowthChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'New Members',
        data: data,
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: '#4B49AC',
        borderWidth: 2,
        pointBackgroundColor: '#4B49AC',
        pointRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      },
      plugins: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: 'Monthly Member Growth',
          font: {
            size: 16
          }
        }
      }
    }
  });
});
</script>
