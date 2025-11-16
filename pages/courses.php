<?php
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$programs = [];
if ($db) {
    try {
        $query = "SELECT * FROM courses WHERE status = 'active' ORDER BY id ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug: Check if query returned results
        if (empty($programs)) {
            error_log('[DEBUG] No active courses found in database');
        }
    } catch(PDOException $e) {
        error_log('[ERROR] Programs fetch failed: ' . $e->getMessage());
    }
} else {
    error_log('[ERROR] Database connection failed');
}
?>

<section class="courses-hero section light-background">
  <div class="hero-content">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
          <div class="hero-text">
            <h1>Choose the Learning Path That Matches Your Goals</h1>
            <p>From your first line of code to advanced innovation projects, KHODERS programs blend mentorship, collaboration, and real-world experience to help you level up fast.</p>
            <div class="hero-features">
              <div class="feature">
                <i class="bi bi-lightning-charge"></i>
                <span>Project-based curriculum</span>
              </div>
              <div class="feature">
                <i class="bi bi-people"></i>
                <span>Small peer cohorts</span>
              </div>
              <div class="feature">
                <i class="bi bi-mortarboard"></i>
                <span>Certified mentors</span>
              </div>
            </div>
            <div class="hero-buttons">
              <a href="#tracks" class="btn btn-primary">View Learning Tracks</a>
              <a href="#admissions" class="btn btn-outline">See Admissions</a>
            </div>
          </div>
        </div>
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
          <div class="hero-image">
            <img src="assets/img/education/courses-1.webp" alt="Students collaborating" class="img-fluid">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="tracks" class="featured-courses section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Learning Tracks</h2>
    <p>Each program combines workshops, mentorship, and hands-on deliverables so you graduate with real portfolio pieces.</p>
  </div>

  <div class="container">
    <?php if (empty($programs)): ?>
      <div class="alert alert-info text-center">
        <p>No courses available at the moment. Please check back soon!</p>
        <p class="small text-muted">If you're an admin, please add courses through the admin panel.</p>
      </div>
    <?php else: ?>
    <div class="row gy-4">
      <?php foreach($programs as $index => $program): ?>
      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="<?php echo 100 + ($index * 50); ?>">
        <div class="course-card">
          <div class="course-image">
            <img src="<?php echo htmlspecialchars($program['hero_image'] ?? 'assets/img/education/courses-1.webp'); ?>" alt="<?php echo htmlspecialchars($program['title']); ?>" class="img-fluid">
            <div class="badge <?php echo $index === 0 ? 'featured' : ($index === 1 ? 'badge-free' : 'badge-new'); ?>">
              <?php echo $index === 0 ? 'New Cohort' : ($index === 1 ? 'Popular' : 'Expanding'); ?>
            </div>
          </div>
          <div class="course-content">
            <div class="course-meta">
              <span class="category"><?php echo htmlspecialchars($program['category'] ?? ''); ?></span>
              <span class="level"><?php echo htmlspecialchars($program['level'] ?? ''); ?></span>
            </div>
            <h3><?php echo htmlspecialchars($program['title'] ?? ''); ?></h3>
            <p><?php echo htmlspecialchars($program['subtitle'] ?? ''); ?></p>
            <ul class="course-highlights">
              <li><i class="bi bi-people"></i> <?php echo (int)($program['members_count'] ?? 0); ?> enrolled</li>
              <li><i class="bi bi-star-fill"></i> <?php echo number_format((float)($program['rating'] ?? 5.0), 1); ?> rating</li>
              <li><i class="bi bi-clock"></i> <?php echo htmlspecialchars($program['duration'] ?? '12 weeks'); ?></li>
            </ul>
            <a href="index.php?page=course-details&id=<?php echo (int)($program['id'] ?? 0); ?>" class="btn-course">Learn More</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<section class="section light-background">
  <div class="container" data-aos="fade-up">
    <div class="row align-items-center gy-4">
      <div class="col-lg-5">
        <h2>Program Experience Timeline</h2>
        <p>Every track is designed around a 12-week experience combining hands-on building, mentorship, and community events.</p>
        <a href="index.php?page=register" class="btn btn-primary">Talk to an Advisor</a>
      </div>
      <div class="col-lg-7">
        <div class="timeline">
          <div class="timeline-item">
            <span class="timeline-badge">Weeks 1-2</span>
            <div>
              <h4>Onboarding & Skill Assessment</h4>
              <p>Kick-off workshop, mentor pairing, and personalized learning plan.</p>
            </div>
          </div>
          <div class="timeline-item">
            <span class="timeline-badge">Weeks 3-6</span>
            <div>
              <h4>Core Workshops & Labs</h4>
              <p>Live sessions, code-alongs, and weekly challenges.</p>
            </div>
          </div>
          <div class="timeline-item">
            <span class="timeline-badge">Weeks 7-10</span>
            <div>
              <h4>Applied Project Sprint</h4>
              <p>Deliver a capstone project with peer feedback.</p>
            </div>
          </div>
          <div class="timeline-item">
            <span class="timeline-badge">Weeks 11-12</span>
            <div>
              <h4>Showcase & Career Prep</h4>
              <p>Portfolio polishing and Demo Day presentation.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="admissions" class="section admissions-section">
  <div class="container" data-aos="fade-up">
    <div class="row gy-5 align-items-start">
      <div class="col-lg-5">
        <h2>Admissions & Eligibility</h2>
        <p>Our programs welcome students excited to dedicate time each week to building their skills.</p>
        <div class="admission-requirements">
          <h3>What we look for</h3>
          <ul>
            <li><i class="bi bi-check2-circle"></i> Curiosity and commitment to collaborative learning</li>
            <li><i class="bi bi-check2-circle"></i> Availability for 8-10 hours per week</li>
            <li><i class="bi bi-check2-circle"></i> Basic familiarity with computers</li>
            <li><i class="bi bi-check2-circle"></i> Completion of the KHODERS community pledge</li>
          </ul>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="application-process">
          <h3>How to apply</h3>
          <ol>
            <li>Submit the <a href="index.php?page=register">program interest form</a>.</li>
            <li>Attend a 15-minute fit conversation with a mentor advisor.</li>
            <li>Complete a short challenge aligned with your track.</li>
            <li>Receive cohort placement within 5 business days.</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section cta-section accent-background">
  <div class="container" data-aos="zoom-in">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <h2>Ready to take the next step?</h2>
        <p>Apply now to join our next cohort and gain access to mentors, industry projects, and a supportive tech community.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="index.php?page=register" class="btn btn-light">Submit Your Application</a>
      </div>
    </div>
  </div>
</section>
