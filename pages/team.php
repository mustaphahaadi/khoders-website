<?php
/**
 * Team Page - Khoders World
 * Displays team members from database
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/router.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get team members
$query = "SELECT id, name, position, bio, photo_url as image_url, twitter_url as social_twitter, linkedin_url as social_linkedin, github_url as social_github, order_index, is_featured 
          FROM team_members 
          WHERE status = 'active' 
          ORDER BY is_featured DESC, order_index ASC, name ASC";
          
$stmt = $db->prepare($query);
$stmt->execute();
$teamMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separate featured and regular team members
$featuredMembers = array_filter($teamMembers, function($member) {
    return $member['is_featured'] == 1;
});
$regularMembers = array_filter($teamMembers, function($member) {
    return $member['is_featured'] == 0;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Our Team - Khoders World</title>
  <meta name="description" content="Meet the passionate leaders and mentors of Khoders World coding club">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="team-page">
  <?php include __DIR__ . '/../includes/navigation.php'; ?>
  
  <main class="main">
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Our Team</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Team</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Team Section -->
    <section id="team" class="team section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Leadership Team</h2>
        <p>Meet the passionate individuals driving Khoders World forward</p>
      </div>

      <div class="container">
        <?php if (!empty($featuredMembers)): ?>
          <!-- Featured Team Members -->
          <div class="row gy-5 mb-5">
            <div class="col-12" data-aos="fade-up">
              <h3 class="mb-4">Executive Leadership</h3>
            </div>
            <?php foreach ($featuredMembers as $member): ?>
              <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="member">
                  <div class="member-img">
                    <img src="<?php echo !empty($member['image_url']) ? htmlspecialchars($member['image_url']) : 'assets/img/team/team-default.jpg'; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($member['name']); ?>">
                    <div class="social">
                      <?php if (!empty($member['social_twitter'])): ?>
                        <a href="<?php echo htmlspecialchars($member['social_twitter']); ?>" target="_blank"><i class="bi bi-twitter"></i></a>
                      <?php endif; ?>
                      <?php if (!empty($member['social_facebook'])): ?>
                        <a href="<?php echo htmlspecialchars($member['social_facebook']); ?>" target="_blank"><i class="bi bi-facebook"></i></a>
                      <?php endif; ?>
                      <?php if (!empty($member['social_linkedin'])): ?>
                        <a href="<?php echo htmlspecialchars($member['social_linkedin']); ?>" target="_blank"><i class="bi bi-linkedin"></i></a>
                      <?php endif; ?>
                      <?php if (!empty($member['social_github'])): ?>
                        <a href="<?php echo htmlspecialchars($member['social_github']); ?>" target="_blank"><i class="bi bi-github"></i></a>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="member-info text-center">
                    <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                    <span><?php echo htmlspecialchars($member['position']); ?></span>
                    <?php if (!empty($member['bio'])): ?>
                      <p class="mt-3"><?php echo htmlspecialchars($member['bio']); ?></p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($regularMembers)): ?>
          <!-- Regular Team Members -->
          <div class="row gy-5">
            <div class="col-12" data-aos="fade-up">
              <h3 class="mb-4">Team Members</h3>
            </div>
            <?php foreach ($regularMembers as $member): ?>
              <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="member">
                  <div class="member-img">
                    <img src="<?php echo !empty($member['image_url']) ? htmlspecialchars($member['image_url']) : 'assets/img/team/team-default.jpg'; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($member['name']); ?>">
                    <div class="social">
                      <?php if (!empty($member['social_twitter'])): ?>
                        <a href="<?php echo htmlspecialchars($member['social_twitter']); ?>" target="_blank"><i class="bi bi-twitter"></i></a>
                      <?php endif; ?>
                      <?php if (!empty($member['social_facebook'])): ?>
                        <a href="<?php echo htmlspecialchars($member['social_facebook']); ?>" target="_blank"><i class="bi bi-facebook"></i></a>
                      <?php endif; ?>
                      <?php if (!empty($member['social_linkedin'])): ?>
                        <a href="<?php echo htmlspecialchars($member['social_linkedin']); ?>" target="_blank"><i class="bi bi-linkedin"></i></a>
                      <?php endif; ?>
                      <?php if (!empty($member['social_github'])): ?>
                        <a href="<?php echo htmlspecialchars($member['social_github']); ?>" target="_blank"><i class="bi bi-github"></i></a>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="member-info text-center">
                    <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                    <span><?php echo htmlspecialchars($member['position']); ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if (empty($teamMembers)): ?>
          <!-- Empty State -->
          <div class="row" data-aos="fade-up">
            <div class="col-12 text-center py-5">
              <i class="bi bi-people" style="font-size: 4rem; color: #136ad5;"></i>
              <h3 class="mt-3">Team Members Coming Soon</h3>
              <p class="text-muted">We're building an amazing team of coding enthusiasts!</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Join CTA Section -->
    <section class="cta-section py-5 bg-primary text-white">
      <div class="container text-center" data-aos="fade-up">
        <h2>Want to Join Our Team?</h2>
        <p class="lead mb-4">We're always looking for passionate coders to help lead our community</p>
        <a href="index.php?page=register" class="btn btn-light btn-lg">Become a Member</a>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../includes/footer.php'; ?>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
