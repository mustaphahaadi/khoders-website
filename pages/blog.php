<?php
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$posts = [];
if ($db) {
    try {
        $query = "SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log('[ERROR] Blog fetch failed: ' . $e->getMessage());
    }
}

$featuredPost = !empty($posts) ? array_shift($posts) : null;
$heroPosts = array_slice($posts, 0, 4);
$regularPosts = array_slice($posts, 4);
?>

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

<section id="blog-hero" class="blog-hero section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="blog-grid">
      
      <?php if ($featuredPost): ?>
      <article class="blog-item featured" data-aos="fade-up">
        <img src="<?php echo htmlspecialchars($featuredPost['featured_image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($featuredPost['title'] ?? ''); ?>" class="img-fluid">
        <div class="blog-content">
          <div class="post-meta">
            <span class="date"><?php echo date('M. jS, Y', strtotime($featuredPost['created_at'] ?? 'now')); ?></span>
            <span class="category"><?php echo htmlspecialchars($featuredPost['category'] ?? ''); ?></span>
          </div>
          <h2 class="post-title">
            <a href="index.php?page=blog-details&id=<?php echo (int)($featuredPost['id'] ?? 0); ?>"><?php echo htmlspecialchars($featuredPost['title'] ?? ''); ?></a>
          </h2>
        </div>
      </article>
      <?php endif; ?>

      <?php foreach ($heroPosts as $index => $post): ?>
      <article class="blog-item" data-aos="fade-up" data-aos-delay="<?php echo 100 + ($index * 100); ?>">
        <img src="<?php echo htmlspecialchars($post['featured_image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($post['title'] ?? ''); ?>" class="img-fluid">
        <div class="blog-content">
          <div class="post-meta">
            <span class="date"><?php echo date('M. jS, Y', strtotime($post['created_at'] ?? 'now')); ?></span>
            <span class="category"><?php echo htmlspecialchars($post['category'] ?? ''); ?></span>
          </div>
          <h3 class="post-title">
            <a href="index.php?page=blog-details&id=<?php echo (int)($post['id'] ?? 0); ?>"><?php echo htmlspecialchars($post['title'] ?? ''); ?></a>
          </h3>
        </div>
      </article>
      <?php endforeach; ?>

    </div>
  </div>
</section>

<section id="blog-posts" class="blog-posts section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-4">

      <?php foreach ($regularPosts as $post): 
        $postDate = new DateTime($post['created_at']);
      ?>
      <div class="col-lg-4">
        <article class="position-relative h-100">
          <div class="post-img position-relative overflow-hidden">
            <img src="<?php echo htmlspecialchars($post['featured_image'] ?? ''); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($post['title'] ?? ''); ?>">
          </div>
          <div class="meta d-flex align-items-end">
            <span class="post-date"><span><?php echo $postDate->format('d'); ?></span><?php echo $postDate->format('F'); ?></span>
            <div class="d-flex align-items-center">
              <i class="bi bi-person"></i> <span class="ps-2"><?php echo htmlspecialchars($post['author'] ?? ''); ?></span>
            </div>
            <span class="px-3 text-black-50">/</span>
            <div class="d-flex align-items-center">
              <i class="bi bi-folder2"></i> <span class="ps-2"><?php echo htmlspecialchars($post['category'] ?? ''); ?></span>
            </div>
          </div>
          <div class="post-content d-flex flex-column">
            <h3 class="post-title"><?php echo htmlspecialchars($post['title'] ?? ''); ?></h3>
            <a href="index.php?page=blog-details&id=<?php echo (int)($post['id'] ?? 0); ?>" class="readmore stretched-link"><span>Read More</span><i class="bi bi-arrow-right"></i></a>
          </div>
        </article>
      </div>
      <?php endforeach; ?>

    </div>
  </div>
</section>
