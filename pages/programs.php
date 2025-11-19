<?php
/**
 * Programs Page - Khoders World
 * Displays training programs from database
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/router.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get programs
$query = "SELECT id, title, description, duration, level, curriculum, benefits, requirements, price, enrollment_count, is_featured 
          FROM programs 
          WHERE status = 'active' 
          ORDER BY is_featured DESC, created_at DESC";
          
$stmt = $db->prepare($query);
$stmt->execute();
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format program data
foreach ($programs as &$program) {
    $program['price_display'] = ($program['price'] == 0 || empty($program['price'])) ? 'Free' : 'GH₵ ' . number_format($program['price'], 2);
    // Parse curriculum and benefits if stored as JSON
    if (!empty($program['curriculum']) && is_string($program['curriculum'])) {
        $decoded = json_decode($program['curriculum'], true);
        $program['curriculum_array'] = is_array($decoded) ? $decoded : explode("\n", $program['curriculum']);
    } else {
        $program['curriculum_array'] = [];
    }
    if (!empty($program['benefits']) && is_string($program['benefits'])) {
        $decoded = json_decode($program['benefits'], true);
        $program['benefits_array'] = is_array($decoded) ? $decoded : explode("\n", $program['benefits']);
    } else {
        $program['benefits_array'] = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Training Programs - Khoders World</title>
  <meta name="description" content="Join comprehensive coding programs at Khoders World">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="programs-page">
  <?php include __DIR__ . '/../includes/navigation.php'; ?>
  
  <main class="main">
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Training Programs</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Programs</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Programs Section -->
    <section id="programs" class="programs section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Structured Learning Paths</h2>
        <p>Comprehensive programs to take you from beginner to job-ready</p>
      </div>

      <div class="container">
        <?php if (empty($programs)): ?>
          <!-- Empty State -->
          <div class="row" data-aos="fade-up">
            <div class="col-12 text-center py-5">
              <i class="bi bi-mortarboard" style="font-size: 4rem; color: #136ad5;"></i>
              <h3 class="mt-3">Programs Coming Soon</h3>
              <p class="text-muted">We're designing comprehensive training programs for you!</p>
              <a href="index.php?page=courses" class="btn btn-primary mt-3">Explore Courses</a>
            </div>
          </div>
        <?php else: ?>
          <!-- Programs List -->
          <div class="row gy-5">
            <?php foreach ($programs as $program): ?>
              <div class="col-12" data-aos="fade-up">
                <div class="card program-card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-8">
                        <?php if ($program['is_featured']): ?>
                          <span class="badge bg-warning text-dark mb-2">Featured</span>
                        <?php endif; ?>
                        
                        <?php if (!empty($program['level'])): ?>
                          <span class="badge bg-info mb-2"><?php echo ucfirst(htmlspecialchars($program['level'])); ?></span>
                        <?php endif; ?>
                        
                        <h3 class="program-title"><?php echo htmlspecialchars($program['title']); ?></h3>
                        
                        <p class="program-description"><?php echo htmlspecialchars($program['description']); ?></p>
                        
                        <div class="program-meta mt-4">
                          <?php if (!empty($program['duration'])): ?>
                            <span class="me-4"><i class="bi bi-clock"></i> <?php echo htmlspecialchars($program['duration']); ?></span>
                          <?php endif; ?>
                          <span class="me-4"><i class="bi bi-people"></i> <?php echo number_format($program['enrollment_count']); ?> enrolled</span>
                          <span><i class="bi bi-award"></i> Certificate upon completion</span>
                        </div>
                        
                        <?php if (!empty($program['benefits_array'])): ?>
                          <div class="mt-4">
                            <h5>What You'll Gain:</h5>
                            <ul class="list-unstyled">
                              <?php foreach (array_slice($program['benefits_array'], 0, 3) as $benefit): ?>
                                <?php if (!empty(trim($benefit))): ?>
                                  <li><i class="bi bi-check-circle text-success"></i> <?php echo htmlspecialchars($benefit); ?></li>
                                <?php endif; ?>
                              <?php endforeach; ?>
                            </ul>
                          </div>
                        <?php endif; ?>
                      </div>
                      
                      <div class="col-md-4 text-center">
                        <div class="program-price-box p-4 bg-light rounded">
                          <h2 class="price"><?php echo $program['price_display']; ?></h2>
                          <p class="text-muted mb-4">
                            <?php echo $program['price'] == 0 ? 'Complete access' : 'One-time payment'; ?>
                          </p>
                          <a href="index.php?page=enroll&type=program&id=<?php echo $program['id']; ?>" class="btn btn-primary btn-lg w-100 mb-3">
                            Enroll Now
                          </a>
                          <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#program<?php echo $program['id']; ?>">
                            <i class="bi bi-info-circle"></i> More Details
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Program Details Modal -->
              <div class="modal fade" id="program<?php echo $program['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title"><?php echo htmlspecialchars($program['title']); ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <?php if (!empty($program['curriculum_array'])): ?>
                        <h6>Curriculum:</h6>
                        <ul>
                          <?php foreach ($program['curriculum_array'] as $item): ?>
                            <?php if (!empty(trim($item))): ?>
                              <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endif; ?>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                      
                      <?php if (!empty($program['requirements'])): ?>
                        <h6 class="mt-4">Requirements:</h6>
                        <p><?php echo nl2br(htmlspecialchars($program['requirements'])); ?></p>
                      <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <a href="index.php?page=enroll&type=program&id=<?php echo $program['id']; ?>" class="btn btn-primary">
                        Enroll Now
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../includes/footer.php'; ?>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
