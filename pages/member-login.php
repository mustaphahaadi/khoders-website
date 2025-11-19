<?php
/**
 * Member Login Page - Khoders World
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../includes/member-auth.php';

$error = '';
$success = '';

// Redirect if already logged in
if (MemberAuth::isLoggedIn()) {
    header('Location: index.php?page=dashboard');
    exit;
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRFToken::validate()) {
        $error = 'Invalid security token';
    } else {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Please enter both email and password';
        } else {
            $result = MemberAuth::login($email, $password);
            if ($result['success']) {
                header('Location: index.php?page=dashboard');
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

$csrfToken = CSRFToken::generate();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Member Login - Khoders World</title>
  <meta name="description" content="Login to your Khoders World member account">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="login-page">
  <?php include __DIR__ . '/../includes/navigation.php'; ?>
  
  <main class="main">
    <section class="py-5">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-5 col-md-7">
            <div class="card shadow-sm">
              <div class="card-body p-5">
                <div class="text-center mb-4">
                  <h2>Member Login</h2>
                  <p class="text-muted">Access your Khoders World account</p>
                </div>

                <?php if ($error): ?>
                  <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                  </div>
                <?php endif; ?>

                <?php if ($success): ?>
                  <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                  </div>
                <?php endif; ?>

                <form method="POST" action="">
                  <?php echo CSRFToken::getFieldHTML(); ?>
                  
                  <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                  </div>

                  <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                  </div>

                  <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                  </div>

                  <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                  </button>

                  <div class="text-center">
                    <p class="mb-0">
                      Don't have an account? <a href="index.php?page=register">Register here</a>
                    </p>
                    <p class="mt-2">
                      <a href="#" class="text-muted small">Forgot password?</a>
                    </p>
                  </div>
                </form>
              </div>
            </div>

            <!-- Benefits Section -->
            <div class="card mt-4 bg-light border-0">
              <div class="card-body">
                <h6 class="mb-3">Member Benefits:</h6>
                <ul class="list-unstyled">
                  <li><i class="bi bi-check-circle text-success"></i> Register for events and workshops</li>
                  <li><i class="bi bi-check-circle text-success"></i> Track your learning progress</li>
                  <li><i class="bi bi-check-circle text-success"></i> Showcase your projects</li>
                  <li><i class="bi bi-check-circle text-success"></i> Connect with fellow coders</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../includes/footer.php'; ?>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
