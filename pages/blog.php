<?php
/**
 * Blog Page - Khoders World
 * Displays blog posts from database
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

//Category filter
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build query
$whereClause = "WHERE status = 'published'";
$params = [];
if (!empty($category)) {
    $whereClause .= " AND category = ?";
    $params[] = $category;
}

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM blog_posts $whereClause";
$countStmt = $db->prepare($countQuery);
$countStmt->execute($params);
$totalPosts = $countStmt->fetch()['total'];
$totalPages = ceil($totalPosts / $perPage);

// Get blog posts
$query = "SELECT id, title, content, excerpt, featured_image, category, author, views, created_at, updated_at 
          FROM blog_posts 
          $whereClause 
          ORDER BY created_at DESC 
          LIMIT ? OFFSET ?";
          
$params[] = $perPage;
$params[] = $offset;
$stmt = $db->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format dates and excerpts
foreach ($posts as &$post) {
    $post['formatted_date'] = date('F j, Y', strtotime($post['created_at']));
    if (empty($post['excerpt']) && !empty($post['content'])) {
        $post['excerpt'] = substr(strip_tags($post['content']), 0, 150) . '...';
    }
    if (empty($post['featured_image'])) {
        $post['featured_image'] = 'assets/img/blog/blog-default.jpg';
    }
    if (empty($post['author'])) {
        $post['author'] = 'Khoders World Team';
    }
}

// Get categories for filter
$catQuery = "SELECT DISTINCT category FROM blog_posts WHERE status = 'published' AND category IS NOT NULL ORDER BY category";
$catStmt = $db->prepare($catQuery);
$catStmt->execute();
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Blog - Khoders World</title>
  <meta name="description" content="Read the latest coding tutorials, tech insights, and club updates from Khoders World">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="blog-page">
  <?php include __DIR__ . '/../includes/navigation.php'; ?>
  
  <main class="main">
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Blog</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Blog</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Blog Section -->
    <section id="blog" class="blog section">
      <div class="container">
        <div class="row">
          <!-- Main Blog Content -->
          <div class="col-lg-8">
            <?php if (empty($posts)): ?>
              <!-- Empty State -->
              <div class="text-center py-5" data-aos="fade-up">
                <i class="bi bi-newspaper" style="font-size: 4rem; color: #136ad5;"></i>
                <h3 class="mt-3">No Blog Posts Yet</h3>
                <p class="text-muted">
                  <?php if (!empty($category)): ?>
                    No posts in this category. <a href="index.php?page=blog">View all posts</a>
                  <?php else: ?>
                    Check back soon for coding tutorials and tech insights!
                  <?php endif; ?>
                </p>
              </div>
            <?php else: ?>
              <?php foreach ($posts as  $post): ?>
                <article class="blog-post" data-aos="fade-up">
                  <div class="row g-0">
                    <div class="col-md-5">
                      <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" class="img-fluid rounded-start" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    </div>
                    <div class="col-md-7">
                      <div class="post-content p-4">
                        <?php if (!empty($post['category'])): ?>
                          <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($post['category']); ?></span>
                        <?php endif; ?>
                        
                        <h3 class="post-title">
                          <a href="index.php?page=blog-details&id=<?php echo $post['id']; ?>">
                            <?php echo htmlspecialchars($post['title']); ?>
                          </a>
                        </h3>
                        
                        <div class="post-meta mb-3">
                          <span><i class="bi bi-person"></i> <?php echo htmlspecialchars($post['author']); ?></span>
                          <span class="ms-3"><i class="bi bi-calendar"></i> <?php echo $post['formatted_date']; ?></span>
                          <?php if ($post['views'] > 0): ?>
                            <span class="ms-3"><i class="bi bi-eye"></i> <?php echo number_format($post['views']); ?> views</span>
                          <?php endif; ?>
                        </div>
                        
                        <p class="post-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        
                        <a href="index.php?page=blog-details&id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">
                          Read More <i class="bi bi-arrow-right"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                </article>
              <?php endforeach; ?>

              <!-- Pagination -->
              <?php if ($totalPages > 1): ?>
                <nav aria-label="Blog pagination" class="mt-5" data-aos="fade-up">
                  <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                      <li class="page-item">
                        <a class="page-link" href="index.php?page=blog<?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>&p=<?php echo $page - 1; ?>">Previous</a>
                      </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                      <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="index.php?page=blog<?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                      <li class="page-item">
                        <a class="page-link" href="index.php?page=blog<?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>&p=<?php echo $page + 1; ?>">Next</a>
                      </li>
                    <?php endif; ?>
                  </ul>
                </nav>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          <!-- Sidebar -->
          <div class="col-lg-4">
            <!-- Categories Widget -->
            <?php if (!empty($categories)): ?>
              <div class="sidebar-widget categories-widget" data-aos="fade-up">
                <h4 class="widget-title">Categories</h4>
                <ul class="list-unstyled">
                  <li>
                    <a href="index.php?page=blog" class="<?php echo empty($category) ? 'active' : ''; ?>">
                      All Posts
                    </a>
                  </li>
                  <?php foreach ($categories as $cat): ?>
                    <li>
                      <a href="index.php?page=blog&category=<?php echo urlencode($cat); ?>" class="<?php echo $category === $cat ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <!-- Recent Posts Widget -->
            <?php if (!empty($posts)): ?>
              <div class="sidebar-widget recent-posts-widget" data-aos="fade-up">
                <h4 class="widget-title">Recent Posts</h4>
                <ul class="list-unstyled">
                  <?php foreach (array_slice($posts, 0, 5) as $recentPost): ?>
                    <li class="mb-3">
                      <h6>
                        <a href="index.php?page=blog-details&id=<?php echo $recentPost['id']; ?>">
                          <?php echo htmlspecialchars($recentPost['title']); ?>
                        </a>
                      </h6>
                      <small class="text-muted"><?php echo $recentPost['formatted_date']; ?></small>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
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
