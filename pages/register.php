<?php require_once __DIR__ . '/../config/csrf.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Register - KHODERS Campus Coding Community</title>
  <link href="assets/img/khoders/logo.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="register-page">
  <?php include __DIR__ . '/../includes/navigation.php'; ?>
  <main class="main">
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Join KHODERS</h1>
        <nav class="breadcrumbs">
          <ol><li><a href="index.php">Home</a></li><li class="current">Register</li></ol>
        </nav>
      </div>
    </div>
    <section id="register" class="register section">
      <div class="container" data-aos="fade-up">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <h2 class="mb-4">Become a Member</h2>
            <form action="forms/register.php" method="post" class="php-email-form">
              <?php echo CSRFToken::getFieldHTML(); ?>
              <div class="form-group" style="display:none;">
                <input type="text" name="username">
              </div>
              <div class="row">
                <div class="col-md-6 form-group">
                  <label>First Name</label>
                  <input type="text" name="firstName" class="form-control" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>Last Name</label>
                  <input type="text" name="lastName" class="form-control" required>
                </div>
              </div>
              <div class="form-group mt-3">
                <label>Email</label>
                <input type="email" class="form-control" name="email" required>
              </div>
              <div class="form-group mt-3">
                <label>Phone Number</label>
                <input type="tel" class="form-control" name="phone" required>
              </div>
              <div class="form-group mt-3">
                <label>Student ID</label>
                <input type="text" class="form-control" name="studentId">
              </div>
              <div class="form-group mt-3">
                <label>Program of Study</label>
                <input type="text" class="form-control" name="program">
              </div>
              <div class="form-group mt-3">
                <label>Year of Study</label>
                <select class="form-control" name="year">
                  <option value="">Select Year</option>
                  <option value="1">1st Year</option>
                  <option value="2">2nd Year</option>
                  <option value="3">3rd Year</option>
                  <option value="4">4th Year</option>
                </select>
              </div>
              <div class="form-group mt-3">
                <label>Experience Level</label>
                <select class="form-control" name="experience" required>
                  <option value="">Select Level</option>
                  <option value="beginner">Beginner</option>
                  <option value="intermediate">Intermediate</option>
                  <option value="advanced">Advanced</option>
                </select>
              </div>
              <div class="form-group mt-3">
                <label>Areas of Interest</label>
                <div class="mt-2">
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="interests[]" value="Web Development"><label class="form-check-label">Web Development</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="interests[]" value="Mobile Development"><label class="form-check-label">Mobile Development</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="interests[]" value="Data Science"><label class="form-check-label">Data Science</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="interests[]" value="UI/UX Design"><label class="form-check-label">UI/UX Design</label></div>
                </div>
              </div>
              <div class="form-group mt-3">
                <label>Why join KHODERS?</label>
                <textarea class="form-control" name="message" rows="3"></textarea>
              </div>
              <div class="my-3">
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">Registration submitted!</div>
              </div>
              <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Submit Registration</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>
  <?php include __DIR__ . '/../includes/footer.php'; ?>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
