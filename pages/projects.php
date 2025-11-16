<?php
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$pageData = [];
if ($db) {
    try {
        $query = "SELECT * FROM projects WHERE status = 'active' ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $pageData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log('[ERROR] Projects fetch failed: ' . $e->getMessage());
    }
}

?>

<div class="page-title light-background">
  <div class="container">
    <h1>Projects</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="index.php">Home</a></li>
        <li class="current">Projects</li>
      </ol>
    </nav>
  </div>
</div>

<section id="projects" class="section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row g-4 projects-grid">
      <?php if (empty($pageData)): ?>
        <div class="col-12">
          <div class="alert alert-info">No projects available at the moment.</div>
        </div>
      <?php else: ?>
        <?php foreach ($pageData as $index => $project): ?>
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo 100 + ($index * 100); ?>">
            <div class="card h-100">
              <?php if (!empty($project['image_url'])): ?>
                <img src="<?php echo htmlspecialchars($project['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($project['title']); ?>">
              <?php endif; ?>
              <div class="card-body">
                <h4><?php echo htmlspecialchars($project['title']); ?></h4>
                <p><?php echo htmlspecialchars($project['description']); ?></p>
                <?php if (!empty($project['tech_stack'])): ?>
                  <div class="mb-2">
                    <?php 
                    $techs = is_string($project['tech_stack']) ? json_decode($project['tech_stack'], true) : $project['tech_stack'];
                    if (is_array($techs)) {
                        foreach ($techs as $tech) {
                            echo '<span class="badge bg-primary">' . htmlspecialchars($tech) . '</span> ';
                        }
                    }
                    ?>
                  </div>
                <?php endif; ?>
                <div class="d-flex gap-2">
                  <?php if (!empty($project['github_url'])): ?>
                    <a href="<?php echo htmlspecialchars($project['github_url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">GitHub</a>
                  <?php endif; ?>
                  <?php if (!empty($project['demo_url'])): ?>
                    <a href="<?php echo htmlspecialchars($project['demo_url']); ?>" target="_blank" class="btn btn-sm btn-outline-success">Demo</a>
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
