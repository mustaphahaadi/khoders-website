<?php
require_once __DIR__ . '/../config/security.php';

$message = '';
$error = '';

require_once __DIR__ . '/../config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRFToken::validate()) {
        $error = 'Invalid security token.';
    } else {
        $message = 'Login functionality coming soon. Please register to join KHODERS.';
    }
}

$csrfToken = CSRFToken::generate();
?>

<div class="page-title light-background">
  <div class="container">
    <h1>Member Login</h1>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-5">
        <div class="card">
          <div class="card-body p-4">
            <h3 class="text-center mb-4">Login to Your Account</h3>

            <?php if ($message): ?>
              <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
              <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
              
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
              </div>
              <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
            </form>

            <div class="text-center">
              <p class="mb-0">Don't have an account? <a href="index.php?page=register">Join KHODERS</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
