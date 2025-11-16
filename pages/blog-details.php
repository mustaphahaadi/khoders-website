<?php
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;

if ($db && $post_id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ? AND status = 'published'");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log('[ERROR] Blog post fetch failed: ' . $e->getMessage());
    }
}

if (!$post) {
    header('Location: index.php?page=blog');
    exit;
}

$postDate = new DateTime($post['created_at']);
?>

<div class="page-title light-background">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0">Blog Details</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="index.php">Home</a></li>
        <li><a href="index.php?page=blog">Blog</a></li>
        <li class="current"><?php echo htmlspecialchars($post['title']); ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="blog-details section">
  <div class="container">
    <article class="article">
      <div class="post-img">
        <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="img-fluid">
      </div>

      <h2 class="title"><?php echo htmlspecialchars($post['title']); ?></h2>

      <div class="meta-top">
        <ul>
          <li class="d-flex align-items-center"><i class="bi bi-person"></i> <a href="#"><?php echo htmlspecialchars($post['author']); ?></a></li>
          <li class="d-flex align-items-center"><i class="bi bi-clock"></i> <time datetime="<?php echo $post['created_at']; ?>"><?php echo $postDate->format('M d, Y'); ?></time></li>
          <li class="d-flex align-items-center"><i class="bi bi-folder2"></i> <a href="#"><?php echo htmlspecialchars($post['category']); ?></a></li>
        </ul>
      </div>

      <div class="content">
        <?php if (!empty($post['excerpt'])): ?>
        <p><strong><?php echo htmlspecialchars($post['excerpt']); ?></strong></p>
        <?php endif; ?>
        
        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
      </div>

    </article>
  </div>
</section>
