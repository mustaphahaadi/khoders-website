<?php
// Dynamic team template - renders team members from database
if (empty($pageData)) $pageData = [];
?>

<section id="team" class="instructors section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-4">
      <?php if (empty($pageData)): ?>
        <div class="col-12">
          <div class="alert alert-info">
            <p>Team members will be displayed here.</p>
          </div>
        </div>
      <?php else: ?>
        <?php foreach ($pageData as $index => $member): ?>
          <div class="col-xl-3 col-lg-4 col-md-6 team-member" data-aos="fade-up" data-aos-delay="<?php echo 200 + ($index * 50); ?>">
            <div class="instructor-card">
              <div class="instructor-image">
                <?php if (!empty($member['photo_url'])): ?>
                  <img src="<?php echo htmlspecialchars($member['photo_url']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($member['name']); ?>">
                <?php else: ?>
                  <div class="placeholder-image bg-primary d-flex align-items-center justify-content-center" style="height: 250px;">
                    <i class="bi bi-person text-white" style="font-size: 3rem;"></i>
                  </div>
                <?php endif; ?>
              </div>
              <div class="instructor-info">
                <h5><?php echo htmlspecialchars($member['name']); ?></h5>
                <p class="specialty"><?php echo htmlspecialchars($member['position'] ?? ''); ?></p>
                <p class="description"><?php echo htmlspecialchars($member['bio'] ?? ''); ?></p>
                
                <div class="social-links">
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
