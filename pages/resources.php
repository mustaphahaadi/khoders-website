<?php
/**
 * Learning Resources Library - Khoders World
 * Curated learning materials for members
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/router.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Pagination
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Filters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$level = isset($_GET['level']) ? $_GET['level'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$whereClause = "WHERE status = 'active'";
$params = [];

if (!empty($category)) {
    $whereClause .= " AND category = ?";
    $params[] = $category;
}

if (!empty($type)) {
    $whereClause .= " AND resource_type = ?";
    $params[] = $type;
}

if (!empty($level) && $level !== 'all') {
    $whereClause .= " AND (difficulty_level = ? OR difficulty_level = 'all')";
    $params[] = $level;
}

if (!empty($search)) {
    $whereClause .= " AND (title LIKE ? OR description LIKE ? OR tech_stack LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM resources $whereClause";
$countStmt = $db->prepare($countQuery);
$countStmt->execute($params);
$totalResources = $countStmt->fetch()['total'];
$totalPages = ceil($totalResources / $perPage);

// Get resources
$query = "SELECT id, title, description, resource_type, category, url, difficulty_level, tech_stack, thumbnail_url, author, duration, is_free, is_featured, views 
          FROM resources 
          $whereClause 
          ORDER BY is_featured DESC, created_at DESC 
          LIMIT ? OFFSET ?";
          
$params[] = $perPage;
$params[] = $offset;
$stmt = $db->prepare($query);
$stmt->execute($params);
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter
$catQuery = "SELECT DISTINCT category FROM resources WHERE status = 'active' AND category IS NOT NULL ORDER BY category";
$catStmt = $db->prepare($catQuery);
$catStmt->execute();
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// Format resource data
foreach ($resources as &$resource) {
    if (empty($resource['thumbnail_url'])) {
        $resource['thumbnail_url'] = 'assets/img/resources/default-' . $resource['resource_type'] . '.jpg';
    }
    // Parse tech stack
    $resource['tech_array'] = !empty($resource['tech_stack']) ? explode(',', $resource['tech_stack']) : [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Learning Resources - Khoders World</title>
  <meta name="description" content="Free coding tutorials, courses, and learning materials curated by Khoders World">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="resources-page">
  <?php include __DIR__ . '/../includes/navigation.php'; ?>
  
  <main class="main">
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Learning Resources</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Resources</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Resources Section -->
    <section id="resources" class="resources section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Curated Learning Library</h2>
        <p>Free tutorials, courses, and resources handpicked for student developers</p>
      </div>

      <div class="container">
        <!-- Filters -->
        <div class="row mb-4" data-aos="fade-up">
          <div class="col-12">
            <form method="GET" class="row g-3">
              <input type="hidden" name="page" value="resources">
              
              <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Search resources..." value="<?php echo htmlspecialchars($search); ?>">
              </div>
              
              <div class="col-md-2">
                <select class="form-select" name="type">
                  <option value="">All Types</option>
                  <option value="tutorial" <?php echo $type === 'tutorial' ? 'selected' : ''; ?>>Tutorials</option>
                  <option value="video" <?php echo $type === 'video' ? 'selected' : ''; ?>>Videos</option>
                  <option value="course" <?php echo $type === 'course' ? 'selected' : ''; ?>>Courses</option>
                  <option value="article" <?php echo $type === 'article' ? 'selected' : ''; ?>>Articles</option>
                  <option value="book" <?php echo $type === 'book' ? 'selected' : ''; ?>>Books</option>
                  <option value="tool" <?php echo $type === 'tool' ? 'selected' : ''; ?>>Tools</option>
                  <option value="documentation" <?php echo $type === 'documentation' ? 'selected' : ''; ?>>Docs</option>
                </select>
              </div>
              
              <div class="col-md-2">
                <select class="form-select" name="level">
                  <option value="">All Levels</option>
                  <option value="beginner" <?php echo $level === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                  <option value="intermediate" <?php echo $level === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                  <option value="advanced" <?php echo $level === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                </select>
              </div>
              
              <?php if (!empty($categories)): ?>
                <div class="col-md-3">
                  <select class="form-select" name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                      <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              <?php endif; ?>
              
              <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                  <i class="bi bi-search"></i> Filter
                </button>
              </div>
            </form>
          </div>
        </div>

        <?php if (empty($resources)): ?>
          <!-- Empty State -->
          <div class="row" data-aos="fade-up">
            <div class="col-12 text-center py-5">
              <i class="bi bi-journal-code" style="font-size: 4rem; color: #136ad5;"></i>
              <h3 class="mt-3">No Resources Found</h3>
              <p class="text-muted">
                <?php if (!empty($search) || !empty($type) || !empty($level) || !empty($category)): ?>
                  Try adjusting your filters or <a href="index.php?page=resources">view all resources</a>
                <?php else: ?>
                  Resources will be added soon!
                <?php endif; ?>
              </p>
            </div>
          </div>
        <?php else: ?>
          <!-- Resources Grid -->
          <div class="row gy-4">
            <?php foreach ($resources as $resource): ?>
              <div class="col-lg-3 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card resource-card h-100">
                  <div class="card-img-top-wrapper position-relative">
                    <img src="<?php echo htmlspecialchars($resource['thumbnail_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($resource['title']); ?>">
                    <?php if ($resource['is_featured']): ?>
                      <span class="badge bg-warning position-absolute top-0 start-0 m-2">Featured</span>
                    <?php endif; ?>
                    <?php if ($resource['is_free']): ?>
                      <span class="badge bg-success position-absolute top-0 end-0 m-2">Free</span>
                    <?php endif; ?>
                  </div>
                  
                  <div class="card-body">
                    <div class="mb-2">
                      <span class="badge bg-primary"><?php echo ucfirst(htmlspecialchars($resource['resource_type'])); ?></span>
                      <?php if (!empty($resource['difficulty_level']) && $resource['difficulty_level'] !== 'all'): ?>
                        <span class="badge bg-info"><?php echo ucfirst(htmlspecialchars($resource['difficulty_level'])); ?></span>
                      <?php endif; ?>
                    </div>
                    
                    <h6 class="card-title">
                      <a href="<?php echo htmlspecialchars($resource['url']); ?>" target="_blank" rel="noopener">
                        <?php echo htmlspecialchars($resource['title']); ?>
                      </a>
                    </h6>
                    
                    <?php if (!empty($resource['description'])): ?>
                      <p class="card-text text-muted small">
                        <?php 
                          $desc = htmlspecialchars($resource['description']);
                          echo strlen($desc) > 80 ? substr($desc, 0, 80) . '...' : $desc; 
                        ?>
                      </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($resource['tech_array'])): ?>
                      <div class="tech-tags mt-2">
                        <?php foreach (array_slice($resource['tech_array'], 0, 3) as $tech): ?>
                          <span class="badge bg-light text-dark"><?php echo htmlspecialchars(trim($tech)); ?></span>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                  
                  <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                      <small class="text-muted">
                        <?php if (!empty($resource['duration'])): ?>
                          <i class="bi bi-clock"></i> <?php echo htmlspecialchars($resource['duration']); ?>
                        <?php endif; ?>
                      </small>
                      <a href="<?php echo htmlspecialchars($resource['url']); ?>" class="btn btn-sm btn-primary" target="_blank" rel="noopener">
                        <i class="bi bi-box-arrow-up-right"></i> Open
                      </a>
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
                <nav aria-label="Resources pagination">
                  <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                      <li class="page-item">
                        <a class="page-link" href="index.php?page=resources<?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($type) ? '&type=' . $type : ''; ?><?php echo !empty($level) ? '&level=' . $level : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>&p=<?php echo $page - 1; ?>">Previous</a>
                      </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                      <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="index.php?page=resources<?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($type) ? '&type=' . $type : ''; ?><?php echo !empty($level) ? '&level=' . $level : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                      <li class="page-item">
                        <a class="page-link" href="index.php?page=resources<?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($type) ? '&type=' . $type : ''; ?><?php echo !empty($level) ? '&level=' . $level : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>&p=<?php echo $page + 1; ?>">Next</a>
                      </li>
                    <?php endif; ?>
                  </ul>
                </nav>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>

        <!-- Stats -->
        <div class="row mt-5" data-aos="fade-up">
          <div class="col-12 text-center">
            <p class="text-muted">
              Showing <?php echo count($resources); ?> of <?php echo $totalResources; ?> resources
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Submit Resource CTA -->
    <section class="cta-section py-5 bg-light">
      <div class="container text-center" data-aos="fade-up">
        <h2>Know a Great Resource?</h2>
        <p class="lead mb-4">Help the community by suggesting learning materials</p>
        <a href="index.php?page=contact" class="btn btn-primary btn-lg">Submit a Resource</a>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../includes/footer.php'; ?>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
