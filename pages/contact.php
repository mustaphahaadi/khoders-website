<?php require_once __DIR__ . '/../config/csrf.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Contact - KHODERS Campus Coding Community</title>
  <meta name="description" content="Get in touch with KHODERS campus coding club.">
  <link href="assets/img/khoders/logo.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="contact-page">
  <?php include __DIR__ . '/../includes/navigation.php'; ?>
  <main class="main">
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Contact</h1>
        <nav class="breadcrumbs">
          <ol><li><a href="index.php">Home</a></li><li class="current">Contact</li></ol>
        </nav>
      </div>
    </div>
    <section id="contact" class="contact section">
      <div class="container" data-aos="fade-up">
        <div class="contact-main-wrapper">
          <div class="map-wrapper">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3962.6614440310703!2d-1.573630285506217!3d6.687859822979193!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdb976e4564374d%3A0x4c41ff165bdc4059!2sKumasi%20Technical%20University!5e0!3m2!1sen!2sgh!4v1636559330961!5m2!1sen!2sgh" width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"></iframe>
          </div>
          <div class="contact-content">
            <div class="contact-form-container" data-aos="fade-up">
              <h3>Get in Touch</h3>
              <form action="forms/contact.php" method="post" class="php-email-form">
                <?php echo CSRFToken::getFieldHTML(); ?>
                <div class="form-group" style="display:none;">
                  <input type="text" name="website" id="website">
                </div>
                <div class="row">
                  <div class="col-md-6 form-group">
                    <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                  </div>
                  <div class="col-md-6 form-group mt-3 mt-md-0">
                    <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                  </div>
                </div>
                <div class="form-group mt-3">
                  <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                </div>
                <div class="form-group mt-3">
                  <textarea class="form-control" name="message" rows="5" placeholder="Message" required></textarea>
                </div>
                <div class="my-3">
                  <div class="loading">Loading</div>
                  <div class="error-message"></div>
                  <div class="sent-message">Your message has been sent. Thank you!</div>
                </div>
                <div class="text-center"><button type="submit">Send Message</button></div>
              </form>
            </div>
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
