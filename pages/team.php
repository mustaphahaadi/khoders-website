<?php
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$pageData = [];
if ($db) {
    try {
        $query = "SELECT * FROM team_members WHERE status = 'active' ORDER BY order_index ASC, id ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $pageData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log('[ERROR] Team fetch failed: ' . $e->getMessage());
    }
}

?>

<div class="page-title light-background">
  <div class="container">
    <h1>Our Team</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="index.php">Home</a></li>
        <li class="current">Team</li>
      </ol>
    </nav>
  </div>
</div>

<section id="team" class="instructors section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-4">
      <?php if (empty($pageData)): ?>
        <div class="col-12">
          <div class="alert alert-info">Team members will be displayed here.</div>
        </div>
      <?php else: ?>
        <?php foreach ($pageData as $index => $member): ?>
          <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo 200 + ($index * 50); ?>">
            <div class="card h-100">
              <?php if (!empty($member['photo_url'])): ?>
                <img src="<?php echo htmlspecialchars($member['photo_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($member['name']); ?>">
              <?php endif; ?>
              <div class="card-body">
                <h5><?php echo htmlspecialchars($member['name']); ?></h5>
                <p class="text-muted"><?php echo htmlspecialchars($member['position'] ?? ''); ?></p>
                <p><?php echo htmlspecialchars($member['bio'] ?? ''); ?></p>
                <div class="d-flex gap-2">
                  <?php if (!empty($member['linkedin_url'])): ?>
                    <a href="<?php echo htmlspecialchars($member['linkedin_url']); ?>" target="_blank"><i class="bi bi-linkedin"></i></a>
                  <?php endif; ?>
                  <?php if (!empty($member['github_url'])): ?>
                    <a href="<?php echo htmlspecialchars($member['github_url']); ?>" target="_blank"><i class="bi bi-github"></i></a>
                  <?php endif; ?>
                  <?php if (!empty($member['twitter_url'])): ?>
                    <a href="<?php echo htmlspecialchars($member['twitter_url']); ?>" target="_blank"><i class="bi bi-twitter"></i></a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>
