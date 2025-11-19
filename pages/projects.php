<?php
/**
 * Projects Page - Khoders World  
 * Displays member projects from database
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/router.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Pagination
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Technology filter
$tech = isset($_GET['tech']) ? $_GET['tech'] : '';

// Build query
$whereClause = "WHERE status = 'published'";
$params = [];
if (!empty($tech)) {
    $whereClause .= " AND tech_stack LIKE ?";
    $params[] = "%$tech%";
}

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM projects $whereClause";
$countStmt = $db->prepare($countQuery);
$countStmt->execute($params);
$totalProjects = $countStmt->fetch()['total'];
$totalPages = ceil($totalProjects / $perPage);

// Get projects
$query = "SELECT id, title, description, image_url, tech_stack, github_url, demo_url, is_featured, created_by 
          FROM projects 
          $whereClause 
          ORDER BY is_featured DESC, created_at DESC 
          LIMIT ? OFFSET ?";
          
$params[] = $perPage;
$params[] = $offset;
$stmt = $db->prepare($query);
$stmt->execute($params);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process tech stack for each project
foreach ($projects as &$project) {
    if (empty($project['image_url'])) {
        $project['image_url'] = 'assets/img/projects/project-default.jpg';
    }
    // Parse tech stack
    $project['tech_array'] = !empty($project['tech_stack']) ? explode(',', $project['tech_stack']) : [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Projects - Khoders World</title>
  <meta name="description" content="Explore amazing projects built by Khoders World members">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="projects-page">
  <?php include __DIR__ . '/../includes/navigation.php'; ?>
  
  <main class="main">
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Member Projects</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Projects</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Projects Section -->
    <section id="projects" class="projects section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Showcase</h2>
        <p>Discover innovative projects built by our talented members</p>
      </div>

      <div class="container">
        <!-- Search Bar -->
        <div class="row mb-4" data-aos="fade-up">
          <div class="col-md-6 mx-auto">
            <form method="GET" class="input-group">
              <input type="hidden" name="page" value="projects">
              <input type="text" name="tech" class="form-control" placeholder="Search by technology (e.g., React, Python)" value="<?php echo htmlspecialchars($tech); ?>">
              <button class="btn btn-primary" type="submit">
                <i class="bi bi-search"></i> Search
              </button>
            </form>
          </div>
        </div>

        <?php if (empty($projects)): ?>
          <!-- Empty State -->
          <div class="row" data-aos="fade-up">
            <div class="col-12 text-center py-5">
              <i class="bi bi-code-square" style="font-size: 4rem; color: #136ad5;"></i>
              <h3 class="mt-3">No Projects Found</h3>
              <p class="text-muted">
                <?php if (!empty($tech)): ?>
                  Try a different technology or <a href="index.php?page=projects">view all projects</a>
                <?php else: ?>
                  Members will showcase their amazing projects here soon!
                <?php endif; ?>
              </p>
            </div>
          </div>
        <?php else: ?>
          <!-- Projects Grid -->
          <div class="row gy-4">
            <?php foreach ($projects as $project): ?>
              <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card project-card h-100">
                  <img src="<?php echo htmlspecialchars($project['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($project['title']); ?>">
                  
                  <div class="card-body">
                    <?php if ($project['is_featured']): ?>
                      <span class="badge bg-warning text-dark mb-2">Featured</span>
                    <?php endif; ?>
                    
                    <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                    
                    <p class="card-text text-muted">
                      <?php 
                        $description = htmlspecialchars($project['description']);
                        echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description; 
                      ?>
                    </p>
                    
                    <!-- Tech Stack -->
                    <?php if (!empty($project['tech_array'])): ?>
                      <div class="tech-stack mt-3">
                        <?php foreach (array_slice($project['tech_array'], 0, 4) as $technology): ?>
                          <span class="badge bg-secondary"><?php echo htmlspecialchars(trim($technology)); ?></span>
                        <?php endforeach; ?>
                        <?php if (count($project['tech_array']) > 4): ?>
                          <span class="badge bg-light text-dark">+<?php echo count($project['tech_array']) - 4; ?></span>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                  
                  <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between">
                      <?php if (!empty($project['github_url'])): ?>
                        <a href="<?php echo htmlspecialchars($project['github_url']); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                          <i class="bi bi-github"></i> Code
                        </a>
                      <?php endif; ?>
                      <?php if (!empty($project['demo_url'])): ?>
                        <a href="<?php echo htmlspecialchars($project['demo_url']); ?>" class="btn btn-primary btn-sm" target="_blank">
                          <i class="bi bi-eye"></i> Live Demo
                        </a>
                      <?php endif; ?>
                      <?php if (empty($project['github_url']) && empty($project['demo_url'])): ?>
                        <span class="text-muted small">Links coming soon</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Pagination -->
          <?php if ($totalPages > 1): ?>
            <div class="row mt-5" data-aos="fade-up">
              <div class="col-12">
                <nav aria-label="Projects pagination">
                  <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                      <li class="page-item">
                        <a class="page-link" href="index.php?page=projects<?php echo !empty($tech) ? '&tech=' . urlencode($tech) : ''; ?>&p=<?php echo $page - 1; ?>">Previous</a>
                      </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                      <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="index.php?page=projects<?php echo !empty($tech) ? '&tech=' . urlencode($tech) : ''; ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                      <li class="page-item">
                        <a class="page-link" href="index.php?page=projects<?php echo !empty($tech) ? '&tech=' . urlencode($tech) : ''; ?>&p=<?php echo $page + 1; ?>">Next</a>
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

    <!-- Submit Project CTA -->
    <section class="cta-section py-5 bg-light">
      <div class="container text-center" data-aos="fade-up">
        <h2>Have a Project to Showcase?</h2>
        <p class="lead mb-4">Share your work with the Khoders World community</p>
        <a href="index.php?page=register" class="btn btn-primary btn-lg">Join & Submit Project</a>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../includes/footer.php'; ?>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
