<?php
/**
 * Courses Page - Khoders World
 * Displays coding courses from database
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/router.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Pagination
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$perPage = 6;
$offset = ($page - 1) * $perPage;

// Level filter
$level = isset($_GET['level']) ? $_GET['level'] : '';
$levels = ['beginner', 'intermediate', 'advanced'];

// Build query
$whereClause = "WHERE status = 'active'";
$params = [];
if (!empty($level) && in_array($level, $levels)) {
    $whereClause .= " AND level = ?";
    $params[] = $level;
}

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM courses $whereClause";
$countStmt = $db->prepare($countQuery);
$countStmt->execute($params);
$totalCourses = $countStmt->fetch()['total'];
$totalPages = ceil($totalCourses / $perPage);

// Get courses
$query = "SELECT id, title, description, level, duration, instructor,  price, enrollment_count, rating, image_url, is_featured 
          FROM courses 
          $whereClause 
          ORDER BY is_featured DESC, created_at DESC 
          LIMIT ? OFFSET ?";
          
$params[] = $perPage;
$params[] = $offset;
$stmt = $db->prepare($query);
$stmt->execute($params);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format course data
foreach ($courses as &$course) {
    if (empty($course['image_url'])) {
        $course['image_url'] = 'assets/img/courses/course-default.jpg';
    }
    if (empty($course['instructor'])) {
        $course['instructor'] = 'Khoders World Mentors';
    }
    $course['price_display'] = ($course['price'] == 0 || empty($course['price'])) ? 'Free' : 'GH₵ ' . number_format($course['price'], 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Courses - Khoders World</title>
  <meta name="description" content="Learn to code with free and affordable courses from Khoders World">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="courses-page">
  <?php include __DIR__ . '/../includes/navigation.php'; ?>
  
  <main class="main">
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Coding Courses</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Courses</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Courses Section -->
    <section id="courses" class="courses section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Learn to Code</h2>
        <p>Free and affordable courses designed for student developers</p>
      </div>

      <div class="container">
        <!-- Level Filter -->
        <div class="row mb-4" data-aos="fade-up">
          <div class="col-12">
            <div class="btn-group" role="group" aria-label="Course levels">
              <a href="index.php?page=courses" class="btn btn-outline-primary <?php echo empty($level) ? 'active' : ''; ?>">All Levels</a>
              <a href="index.php?page=courses&level=beginner" class="btn btn-outline-primary <?php echo $level === 'beginner' ? 'active' : ''; ?>">Beginner</a>
              <a href="index.php?page=courses&level=intermediate" class="btn btn-outline-primary <?php echo $level === 'intermediate' ? 'active' : ''; ?>">Intermediate</a>
              <a href="index.php?page=courses&level=advanced" class="btn btn-outline-primary <?php echo $level === 'advanced' ? 'active' : ''; ?>">Advanced</a>
            </div>
          </div>
        </div>

        <?php if (empty($courses)): ?>
          <!-- Empty State -->
          <div class="row" data-aos="fade-up">
            <div class="col-12 text-center py-5">
              <i class="bi bi-book" style="font-size: 4rem; color: #136ad5;"></i>
              <h3 class="mt-3">No Courses Available</h3>
              <p class="text-muted">
                <?php if (!empty($level)): ?>
                  No <?php echo $level; ?> courses yet. <a href="index.php?page=courses">View all courses</a>
                <?php else: ?>
                  New courses coming soon!
                <?php endif; ?>
              </p>
              </p>
            </div>
          </div>
        <?php else: ?>
          <!-- Courses Grid -->
          <div class="row gy-4">
            <?php foreach ($courses as $course): ?>
              <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
                <div class="course-item">
                  <img src="<?php echo htmlspecialchars($course['image_url']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($course['title']); ?>">
                  <div class="course-content">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <p class="category"><?php echo htmlspecialchars($course['level']); ?></p>
                      <p class="price"><?php echo $course['price_display']; ?></p>
                    </div>

                    <h3><a href="index.php?page=course-details&id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a></h3>
                    <p class="description"><?php echo htmlspecialchars(substr(strip_tags($course['description']), 0, 100)) . '...'; ?></p>
                    <div class="trainer d-flex justify-content-between align-items-center">
                      <div class="trainer-profile d-flex align-items-center">
                        <img src="assets/img/trainers/trainer-1.jpg" class="img-fluid" alt="">
                        <span><?php echo htmlspecialchars($course['instructor']); ?></span>
                      </div>
                      <div class="trainer-rank d-flex align-items-center">
                        <i class="bi bi-person user-icon"></i>&nbsp;<?php echo $course['enrollment_count']; ?>
                        &nbsp;&nbsp;
                        <i class="bi bi-heart heart-icon"></i>&nbsp;<?php echo $course['rating']; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <!-- Redundant closing tags below will be removed by replacing the messed up block -->


          <!-- Pagination -->
          <?php if ($totalPages > 1): ?>
            <div class="row mt-5" data-aos="fade-up">
              <div class="col-12">
                <nav aria-label="Courses pagination">
                  <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                      <li class="page-item">
                        <a class="page-link" href="index.php?page=courses<?php echo !empty($level) ? '&level=' . $level : ''; ?>&p=<?php echo $page - 1; ?>">Previous</a>
                      </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                      <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="index.php?page=courses<?php echo !empty($level) ? '&level=' . $level : ''; ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                      <li class="page-item">
                        <a class="page-link" href="index.php?page=courses<?php echo !empty($level) ? '&level=' . $level : ''; ?>&p=<?php echo $page + 1; ?>">Next</a>
                      </li>
                    <?php endif; ?>
                  </ul>
                </nav>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </section>

    <!-- Why Learn With Us CTA -->
    <section class="cta-section py-5 bg-light">
      <div class="container text-center" data-aos="fade-up">
        <h2>Why Learn With Khoders World?</h2>
        <div class="row mt-4">
          <div class="col-md-4">
            <i class="bi bi-people-fill" style="font-size: 3rem; color: #136ad5;"></i>
            <h5 class="mt-3">Peer Learning</h5>
            <p>Learn alongside fellow students</p>
          </div>
          <div class="col-md-4">
            <i class="bi bi-award-fill" style="font-size: 3rem; color: #136ad5;"></i>
            <h5 class="mt-3">Expert Mentors</h5>
            <p>Guidance from industry professionals</p>
          </div>
          <div class="col-md-4">
            <i class="bi bi-cash-coin" style="font-size: 3rem; color: #136ad5;"></i>
            <h5 class="mt-3">Affordable</h5>
            <p>Free and student-friendly pricing</p>
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
