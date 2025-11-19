<?php
/**
 * Member Profile Page - Khoders World
 * Edit member profile and change password
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../includes/member-auth.php';

// Require login
MemberAuth::requireLogin();

// Get member data
$member = MemberAuth::getMemberData();
if (!$member) {
    MemberAuth::logout();
    header('Location: index.php?page=member-login');
    exit;
}

$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    if (!CSRFToken::validate()) {
        $error = 'Invalid security token';
    } else {
        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'program' => trim($_POST['program'] ?? ''),
            'year' => trim($_POST['year'] ?? ''),
            'level' => $_POST['level'] ?? '',
            'interests' => $_POST['interests'] ?? [],
            'additional_info' => trim($_POST['additional_info'] ?? '')
        ];
        
        $result = MemberAuth::updateProfile($data);
        if ($result['success']) {
            $success = $result['message'];
            $member = MemberAuth::getMemberData(); // Refresh data
        } else {
            $error = $result['message'];
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    if (!CSRFToken::validate()) {
        $error = 'Invalid security token';
    } else {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'Please fill all password fields';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match';
        } elseif (strlen($newPassword) < 8) {
            $error = 'New password must be at least 8 characters';
        } else {
            $result = MemberAuth::changePassword($currentPassword, $newPassword);
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Parse interests
$interests = [];
if (!empty($member['interests'])) {
    $decoded = json_decode($member['interests'], true);
    $interests = is_array($decoded) ? $decoded : [];
}

$csrfToken = CSRFToken::generate();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Edit Profile - Khoders World</title>
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="profile-page">
  <?php include __DIR__ . '/../includes/navigation.php'; ?>
  
  <main class="main">
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Edit Profile</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php?page=dashboard">Dashboard</a></li>
            <li class="current">Profile</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Profile Section -->
    <section class="profile section">
      <div class="container">
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

        <div class="row">
          <!-- Profile Information -->
          <div class="col-lg-8">
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="mb-0">Profile Information</h5>
              </div>
              <div class="card-body">
                <form method="POST" action="">
                  <?php echo CSRFToken::getFieldHTML(); ?>
                  <input type="hidden" name="action" value="update_profile">
                  
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="first_name" class="form-label">First Name *</label>
                      <input type="text" class="form-control" id="first_name" name="first_name" required value="<?php echo htmlspecialchars($member['first_name']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="last_name" class="form-label">Last Name *</label>
                      <input type="text" class="form-control" id="last_name" name="last_name" required value="<?php echo htmlspecialchars($member['last_name']); ?>">
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($member['email']); ?>" disabled>
                    <small class="text-muted">Email cannot be changed</small>
                  </div>

                  <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone'] ?? ''); ?>">
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="program" class="form-label">Program/Major</label>
                      <input type="text" class="form-control" id="program" name="program" value="<?php echo htmlspecialchars($member['program'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="year" class="form-label">Year</label>
                      <select class="form-select" id="year" name="year">
                        <option value="">Select year</option>
                        <option value="1" <?php echo ($member['year'] ?? '') == '1' ? 'selected' : ''; ?>>Year 1</option>
                        <option value="2" <?php echo ($member['year'] ?? '') == '2' ? 'selected' : ''; ?>>Year 2</option>
                        <option value="3" <?php echo ($member['year'] ?? '') == '3' ? 'selected' : ''; ?>>Year 3</option>
                        <option value="4" <?php echo ($member['year'] ?? '') == '4' ? 'selected' : ''; ?>>Year 4</option>
                        <option value="Graduate" <?php echo ($member['year'] ?? '') == 'Graduate' ? 'selected' : ''; ?>>Graduate</option>
                      </select>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="level" class="form-label">Coding Experience Level</label>
                    <select class="form-select" id="level" name="level">
                      <option value="">Select level</option>
                      <option value="beginner" <?php echo ($member['level'] ?? '') == 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                      <option value="some-experience" <?php echo ($member['level'] ?? '') == 'some-experience' ? 'selected' : ''; ?>>Some Experience</option>
                      <option value="intermediate" <?php echo ($member['level'] ?? '') == 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                      <option value="advanced" <?php echo ($member['level'] ?? '') == 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Interests</label>
                    <div class="row">
                      <?php
                      $interestOptions = ['Web Development', 'Mobile Apps', 'Data Science', 'AI/ML', 'Cybersecurity', 'Game Development', 'Cloud Computing', 'DevOps'];
                      foreach ($interestOptions as $interest):
                      ?>
                        <div class="col-md-6">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="interests[]" value="<?php echo htmlspecialchars($interest); ?>" id="int_<?php echo str_replace(' ', '_', $interest); ?>" <?php echo in_array($interest, $interests) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="int_<?php echo str_replace(' ', '_', $interest); ?>">
                              <?php echo htmlspecialchars($interest); ?>
                            </label>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="additional_info" class="form-label">Additional Information</label>
                    <textarea class="form-control" id="additional_info" name="additional_info" rows="3"><?php echo htmlspecialchars($member['additional_info'] ?? ''); ?></textarea>
                  </div>

                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save Changes
                  </button>
                  <a href="index.php?page=dashboard" class="btn btn-secondary">Cancel</a>
                </form>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="col-lg-4">
            <!-- Password Change -->
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="mb-0">Change Password</h5>
              </div>
              <div class="card-body">
                <form method="POST" action="">
                  <?php echo CSRFToken::getFieldHTML(); ?>
                  <input type="hidden" name="action" value="change_password">
                  
                  <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                  </div>

                  <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                    <small class="text-muted">At least 8 characters</small>
                  </div>

                  <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                  </div>

                  <button type="submit" class="btn btn-warning w-100">
                    <i class="bi bi-key"></i> Change Password
                  </button>
                </form>
              </div>
            </div>

            <!-- Account Info -->
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">Account Information</h5>
              </div>
              <div class="card-body">
                <p><strong>Member Since:</strong><br><?php echo date('F j, Y', strtotime($member['created_at'])); ?></p>
                <?php if (!empty($member['last_login'])): ?>
                  <p><strong>Last Login:</strong><br><?php echo date('M j, Y g:i A', strtotime($member['last_login'])); ?></p>
                <?php endif; ?>
                <p><strong>Status:</strong><br>
                  <span class="badge bg-success"><?php echo ucfirst($member['status']); ?></span>
                </p>
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
