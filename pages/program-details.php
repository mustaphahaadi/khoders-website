<?php
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$program_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$program = null;

if ($db && $program_id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM programs WHERE id = ? AND status = 'active'");
        $stmt->execute([$program_id]);
        $program = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log('[ERROR] Program fetch failed: ' . $e->getMessage());
    }
}

if (!$program) {
    header('Location: index.php?page=services');
    exit;
}

$skills = json_decode($program['skills'] ?? '[]', true) ?? [];
$benefits = json_decode($program['benefits'] ?? '[]', true) ?? [];
$curriculum = json_decode($program['curriculum'] ?? '[]', true) ?? [];
$testimonials = json_decode($program['testimonials'] ?? '[]', true) ?? [];
?>

<div class="page-title light-background">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0">Program Details</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="index.php">Home</a></li>
        <li><a href="index.php?page=services">Services</a></li>
        <li class="current"><?php echo htmlspecialchars($program['title'] ?? 'Program'); ?></li>
      </ol>
    </nav>
  </div>
</div>

<section id="course-details" class="course-details section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row">
      <div class="col-lg-8">
        
        <div class="course-hero" data-aos="fade-up" data-aos-delay="200">
          <div class="hero-content">
            <div class="course-badge">
              <span class="category"><?php echo htmlspecialchars($program['category'] ?? ''); ?></span>
              <span class="level"><?php echo htmlspecialchars($program['level'] ?? ''); ?></span>
            </div>
            <h1><?php echo htmlspecialchars($program['title'] ?? ''); ?></h1>
            <p class="course-subtitle"><?php echo htmlspecialchars($program['subtitle'] ?? ''); ?></p>

            <div class="instructor-card">
              <img src="<?php echo htmlspecialchars($program['instructor_image'] ?? 'assets/img/person/person-f-8.webp'); ?>" alt="Program Lead" class="instructor-image">
              <div class="instructor-details">
                <h5><?php echo htmlspecialchars($program['instructor_name'] ?? 'KHODERS Team'); ?></h5>
                <span><?php echo htmlspecialchars($program['instructor_title'] ?? 'Program Lead'); ?></span>
                <div class="instructor-rating">
                  <?php for($i=0; $i<5; $i++): ?>
                    <i class="bi bi-star-fill"></i>
                  <?php endfor; ?>
                  <span><?php echo number_format((float)($program['rating'] ?? 5.0), 1); ?> (<?php echo (int)($program['reviews_count'] ?? 0); ?> member reviews)</span>
                </div>
              </div>
            </div>
          </div>
          <div class="hero-image">
            <img src="<?php echo htmlspecialchars($program['hero_image'] ?? 'assets/img/education/courses-1.webp'); ?>" alt="<?php echo htmlspecialchars($program['title'] ?? ''); ?>" class="img-fluid">
          </div>
        </div>

        <div class="course-nav-tabs" data-aos="fade-up" data-aos-delay="300">
          <ul class="nav nav-tabs" id="course-detailsCourseTab" role="tablist">
            <li class="nav-item">
              <button class="nav-link active" id="course-detailsoverview-tab" data-bs-toggle="tab" data-bs-target="#course-detailsoverview" type="button" role="tab">
                <i class="bi bi-layout-text-window-reverse"></i> Overview
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link" id="course-detailscurriculum-tab" data-bs-toggle="tab" data-bs-target="#course-detailscurriculum" type="button" role="tab">
                <i class="bi bi-list-ul"></i> Program Structure
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link" id="course-detailsreviews-tab" data-bs-toggle="tab" data-bs-target="#course-detailsreviews" type="button" role="tab">
                <i class="bi bi-star"></i> Testimonials
              </button>
            </li>
          </ul>

          <div class="tab-content" id="course-detailsCourseTabContent">
            
            <div class="tab-pane fade show active" id="course-detailsoverview" role="tabpanel">
              <div class="overview-section">
                <h3>Program Description</h3>
                <p><?php echo nl2br(htmlspecialchars($program['description'] ?? '')); ?></p>
              </div>

              <div class="skills-grid">
                <h3>Skills You'll Gain</h3>
                <div class="row">
                  <?php foreach($skills as $skill): ?>
                  <div class="col-md-6">
                    <div class="skill-item">
                      <div class="skill-icon">
                        <i class="bi <?php echo htmlspecialchars($skill['icon']); ?>"></i>
                      </div>
                      <div class="skill-content">
                        <h5><?php echo htmlspecialchars($skill['title'] ?? ''); ?></h5>
                        <p><?php echo htmlspecialchars($skill['description'] ?? ''); ?></p>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="requirements-section">
                <h3>Program Benefits</h3>
                <ul class="requirements-list">
                  <?php foreach($benefits as $benefit): ?>
                  <li><i class="bi bi-check2"></i><?php echo htmlspecialchars($benefit); ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>

            <div class="tab-pane fade" id="course-detailscurriculum" role="tabpanel">
              <div class="accordion" id="curriculumAccordion">
                <?php foreach($curriculum as $index => $module): ?>
                <div class="accordion-item curriculum-module">
                  <h2 class="accordion-header">
                    <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#module<?php echo $index; ?>">
                      <div class="module-info">
                        <span class="module-title"><?php echo htmlspecialchars($module['title'] ?? ''); ?></span>
                        <span class="module-meta"><?php echo htmlspecialchars($module['duration'] ?? ''); ?></span>
                      </div>
                    </button>
                  </h2>
                  <div id="module<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#curriculumAccordion">
                    <div class="accordion-body">
                      <div class="lessons-list">
                        <?php foreach($module['lessons'] as $lesson): ?>
                        <div class="lesson">
                          <i class="bi bi-file-earmark-code"></i>
                          <span class="lesson-title"><?php echo htmlspecialchars($lesson); ?></span>
                        </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="tab-pane fade" id="course-detailsreviews" role="tabpanel">
              <div class="reviews-summary">
                <div class="rating-overview">
                  <div class="overall-rating">
                    <div class="rating-number"><?php echo number_format((float)($program['rating'] ?? 5.0), 1); ?></div>
                    <div class="rating-stars">
                      <?php for($i=0; $i<5; $i++): ?>
                        <i class="bi bi-star-fill"></i>
                      <?php endfor; ?>
                    </div>
                    <div class="rating-text"><?php echo (int)($program['reviews_count'] ?? 0); ?> member testimonials</div>
                  </div>
                </div>
              </div>

              <div class="reviews-list">
                <?php foreach($testimonials as $testimonial): ?>
                <div class="review-item">
                  <div class="reviewer-info">
                    <img src="<?php echo htmlspecialchars($testimonial['image'] ?? 'assets/img/person/person-f-8.webp'); ?>" alt="Member" class="reviewer-avatar">
                    <div class="reviewer-details">
                      <h6><?php echo htmlspecialchars($testimonial['name'] ?? ''); ?></h6>
                      <div class="review-rating">
                        <?php for($i=0; $i<$testimonial['rating']; $i++): ?>
                          <i class="bi bi-star-fill"></i>
                        <?php endfor; ?>
                      </div>
                    </div>
                    <span class="review-date"><?php echo htmlspecialchars($testimonial['date'] ?? ''); ?></span>
                  </div>
                  <p class="review-text"><?php echo htmlspecialchars($testimonial['text'] ?? ''); ?></p>
                </div>
                <?php endforeach; ?>
              </div>
            </div>

          </div>
        </div>

      </div>

      <div class="col-lg-4">
        <div class="enrollment-card" data-aos="fade-up" data-aos-delay="200">
          <div class="card-header">
            <div class="price-display">
              <span class="current-price">Free</span>
              <span class="discount">KTU Students Only</span>
            </div>
            <div class="enrollment-count">
              <i class="bi bi-people"></i>
              <span><?php echo (int)($program['members_count'] ?? 0); ?> members participating</span>
            </div>
          </div>

          <div class="card-body">
            <div class="action-buttons">
              <a href="index.php?page=enroll&type=program&id=<?php echo (int)($program['id'] ?? 0); ?>" class="btn-primary">Enroll Now</a>
              <a href="index.php?page=contact" class="btn-secondary">Contact Us</a>
            </div>
          </div>
        </div>

        <div class="course-details-card" data-aos="fade-up" data-aos-delay="300">
          <h4>Program Details</h4>
          <div class="detail-grid">
            <div class="detail-row">
              <span class="detail-label">Format</span>
              <span class="detail-value"><?php echo htmlspecialchars($program['format'] ?? ''); ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Duration</span>
              <span class="detail-value"><?php echo htmlspecialchars($program['duration'] ?? ''); ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Skill Levels</span>
              <span class="detail-value"><?php echo htmlspecialchars($program['level'] ?? ''); ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Sessions</span>
              <span class="detail-value"><?php echo htmlspecialchars($program['sessions'] ?? ''); ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Projects</span>
              <span class="detail-value"><?php echo htmlspecialchars($program['projects'] ?? ''); ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Next Start</span>
              <span class="detail-value"><?php echo htmlspecialchars($program['next_start'] ?? ''); ?></span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
