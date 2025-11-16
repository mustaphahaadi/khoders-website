<?php
// Dynamic projects template - renders projects from database
if (empty($pageData)) $pageData = [];
?>

<section id="projects" class="section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row g-4 projects-grid">
      <?php if (empty($pageData)): ?>
        <div class="col-12">
          <div class="alert alert-info">
            <p>Projects will be displayed here.</p>
          </div>
        </div>
      <?php else: ?>
        <?php foreach ($pageData as $index => $project): ?>
          <div class="col-lg-4 col-md-6 project-item" data-aos="fade-up" data-aos-delay="<?php echo 100 + ($index * 100); ?>">
            <div class="project-card shadow-sm rounded-4 overflow-hidden">
              <div class="project-image">
                <?php if (!empty($project['image_url'])): ?>
                  <img src="<?php echo htmlspecialchars($project['image_url']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="img-fluid">
                <?php else: ?>
                  <div class="placeholder-image bg-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="bi bi-laptop text-white" style="font-size: 3rem;"></i>
                  </div>
                <?php endif; ?>
              </div>
              <div class="project-content p-4">
                <h4><a href="#"><?php echo htmlspecialchars($project['title']); ?></a></h4>
                <p><?php echo htmlspecialchars($project['description']); ?></p>
                
                <?php if (!empty($project['tech_stack'])): ?>
                  <div class="project-tags mb-2">
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
                
                <div class="project-links">
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
