<?php
/**
 * Member Dashboard - Khoders World
 * Personal dashboard for logged-in members
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/member-auth.php';
require_once __DIR__ . '/../includes/router.php';

// Require login
MemberAuth::requireLogin();

// Get member data
$member = MemberAuth::getMemberData();
if (!$member) {
    MemberAuth::logout();
    header('Location: index.php?page=member-login');
    exit;
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get member's registered events
$eventsQuery = "SELECT e.*, en.created_at as registration_date 
                FROM enrollments en 
                JOIN events e ON en.item_id = e.id 
                WHERE en.enrollment_type = 'event' AND en.email = ? 
                ORDER BY e.date DESC 
                LIMIT 5";
$eventsStmt = $db->prepare($eventsQuery);
$eventsStmt->execute([$member['email']]);
$registeredEvents = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get member's enrolled courses
$coursesQuery = "SELECT c.*, en.created_at as enrollment_date 
                 FROM enrollments en 
                 JOIN courses c ON en.item_id = c.id 
                 WHERE en.enrollment_type = 'course' AND en.email = ? 
                 ORDER BY en.created_at DESC 
                 LIMIT 5";
$coursesStmt = $db->prepare($coursesQuery);
$coursesStmt->execute([$member['email']]);
$enrolledCourses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);

// Parse interests
$interests = [];
if (!empty($member['interests'])) {
    $decoded = json_decode($member['interests'], true);
    $interests = is_array($decoded) ? $decoded : [];
}

// Format member since date
$memberSince = date('F Y', strtotime($member['created_at']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard - Khoders World</title>
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="dashboard-page">
  <?php include __DIR__ . '/../includes/navigation.php'; ?>
  
  <main class="main">
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">My Dashboard</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Dashboard</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Dashboard Section -->
    <section class="dashboard section">
      <div class="container">
        <!-- Welcome Card -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card bg-primary text-white">
              <div class="card-body p-4">
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <h3 class="mb-2">Welcome back, <?php echo htmlspecialchars($member['first_name']); ?>!</h3>
                    <p class="mb-0">Member since <?php echo $memberSince; ?></p>
                  </div>
                  <div class="col-md-4 text-md-end">
                    <a href="index.php?page=profile" class="btn btn-light">
                      <i class="bi bi-person-circle"></i> Edit Profile
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <!-- Main Content -->
          <div class="col-lg-8">
            <!-- Stats Cards -->
            <div class="row mb-4">
              <div class="col-md-4">
                <div class="card text-center">
                  <div class="card-body">
                    <h3 class="text-primary"><?php echo count($registeredEvents); ?></h3>
                    <p class="mb-0 text-muted">Events Registered</p>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card text-center">
                  <div class="card-body">
                    <h3 class="text-success"><?php echo count($enrolledCourses); ?></h3>
                    <p class="mb-0 text-muted">Courses Enrolled</p>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card text-center">
                  <div class="card-body">
                    <h3 class="text-info"><?php echo count($interests); ?></h3>
                    <p class="mb-0 text-muted">Interests</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Registered Events -->
            <div class="card mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">My Registered Events</h5>
                <a href="index.php?page=events" class="btn btn-sm btn-primary">Browse Events</a>
              </div>
              <div class="card-body">
                <?php if (empty($registeredEvents)): ?>
                  <p class="text-muted text-center py-4">
                    <i class="bi bi-calendar-event" style="font-size: 2rem;"></i><br>
                    No events registered yet. <a href="index.php?page=events">Browse upcoming events</a>
                  </p>
                <?php else: ?>
                  <div class="list-group list-group-flush">
                    <?php foreach ($registeredEvents as $event): ?>
                      <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <h6 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                            <p class="mb-1 text-muted small">
                              <i class="bi bi-calendar"></i> <?php echo date('M j, Y', strtotime($event['date'])); ?>
                              <i class="bi bi-clock ms-2"></i> <?php echo date('g:i A', strtotime($event['time'])); ?>
                            </p>
                            <small class="text-muted">Registered: <?php echo date('M j, Y', strtotime($event['registration_date'])); ?></small>
                          </div>
                          <span class="badge bg-success">Registered</span>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Enrolled Courses -->
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">My Courses</h5>
                <a href="index.php?page=courses" class="btn btn-sm btn-primary">Browse Courses</a>
              </div>
              <div class="card-body">
                <?php if (empty($enrolledCourses)): ?>
                  <p class="text-muted text-center py-4">
                    <i class="bi bi-book" style="font-size: 2rem;"></i><br>
                    No courses enrolled yet. <a href="index.php?page=courses">Explore courses</a>
                  </p>
                <?php else: ?>
                  <div class="list-group list-group-flush">
                    <?php foreach ($enrolledCourses as $course): ?>
                      <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <h6 class="mb-1"><?php echo htmlspecialchars($course['title']); ?></h6>
                            <p class="mb-1 text-muted small">
                              <?php if (!empty($course['level'])): ?>
                                <span class="badge bg-info"><?php echo ucfirst($course['level']); ?></span>
                              <?php endif; ?>
                              <?php if (!empty($course['duration'])): ?>
                                <i class="bi bi-clock ms-2"></i> <?php echo htmlspecialchars($course['duration']); ?>
                              <?php endif; ?>
                            </p>
                            <small class="text-muted">Enrolled: <?php echo date('M j, Y', strtotime($course['enrollment_date'])); ?></small>
                          </div>
                          <span class="badge bg-primary">Active</span>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="mb-0">Your Profile</h5>
              </div>
              <div class="card-body">
                <p><strong>Name:</strong><br><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></p>
                <p><strong>Email:</strong><br><?php echo htmlspecialchars($member['email']); ?></p>
                <?php if (!empty($member['student_id'])): ?>
                  <p><strong>Student ID:</strong><br><?php echo htmlspecialchars($member['student_id']); ?></p>
                <?php endif; ?>
                <?php if (!empty($member['program'])): ?>
                  <p><strong>Program:</strong><br><?php echo htmlspecialchars($member['program']); ?></p>
                <?php endif; ?>
                <?php if (!empty($member['year'])): ?>
                  <p><strong>Year:</strong><br><?php echo htmlspecialchars($member['year']); ?></p>
                <?php endif; ?>
                <?php if (!empty($member['level'])): ?>
                  <p><strong>Level:</strong><br><?php echo ucfirst(htmlspecialchars($member['level'])); ?></p>
                <?php endif; ?>
                <a href="index.php?page=profile" class="btn btn-primary w-100">Edit Profile</a>
              </div>
            </div>

            <!-- Interests Card -->
            <?php if (!empty($interests)): ?>
              <div class="card mb-4">
                <div class="card-header">
                  <h5 class="mb-0">Your Interests</h5>
                </div>
                <div class="card-body">
                  <?php foreach ($interests as $interest): ?>
                    <span class="badge bg-secondary me-1 mb-2"><?php echo htmlspecialchars($interest); ?></span>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
              </div>
              <div class="card-body">
                <div class="d-grid gap-2">
                  <a href="index.php?page=events" class="btn btn-outline-primary">
                    <i class="bi bi-calendar-event"></i> Browse Events
                  </a>
                  <a href="index.php?page=courses" class="btn btn-outline-primary">
                    <i class="bi bi-book"></i> Explore Courses
                  </a>
                  <a href="index.php?page=projects" class="btn btn-outline-primary">
                    <i class="bi bi-code-square"></i> View Projects
                  </a>
                  <a href="index.php?page=member-logout" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../includes/footer.php'; ?>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
