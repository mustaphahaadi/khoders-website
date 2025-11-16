<?php require_once __DIR__ . '/../includes/navigation.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>About KHODERS WORLD - Campus Coding Community</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="about-page">
  <main class="main">
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">About</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="<?php echo SiteRouter::getUrl('index'); ?>">Home</a></li>
            <li class="current">About</li>
          </ol>
        </nav>
      </div>
    </div>
    <section id="about" class="about section">
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row align-items-center">
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <img src="assets/img/education/education-square-2.webp" alt="About Us" class="img-fluid rounded-4">
          </div>
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
            <div class="about-content">
              <span class="subtitle">About Us</span>
              <h2>Empowering Student Developers Through Community and Mentorship</h2>
              <p>KHODERS was founded in 2017 at Kumasi Technical University with a simple mission: to help students learn coding in a collaborative, supportive environment.</p>
              <div class="stats-row">
                <div class="stats-item">
                  <span class="count">7</span>
                  <p>Years Running</p>
                </div>
                <div class="stats-item">
                  <span class="count">15+</span>
                  <p>Industry Mentors</p>
                </div>
                <div class="stats-item">
                  <span class="count">500+</span>
                  <p>Student Members</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
