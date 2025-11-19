<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../config/security.php';

$type = $_GET['type'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';
$item = null;

// Generate CSRF token BEFORE any processing
$csrfToken = CSRFToken::generate();

$database = new Database();
$db = $database->getConnection();

// SECURITY FIX: Use strict whitelist for table mapping
$tableMap = [
    'course' => 'courses',
    'program' => 'programs',
    'project' => 'projects',
    'event' => 'events'
];

if ($db && $id > 0 && isset($tableMap[$type])) {
    try {
        $table = $tableMap[$type];
        
        // Additional security: verify table is in allowed list
        $allowedTables = ['courses', 'programs', 'projects', 'events'];
        if (!in_array($table, $allowedTables, true)) {
            throw new Exception('Invalid table name');
        }
        
        $stmt = $db->prepare("SELECT id, title FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log('[ERROR] Item fetch failed: ' . $e->getMessage());
    } catch(Exception $e) {
        error_log('[SECURITY] Invalid table access attempt: ' . $e->getMessage());
    }
}

if (!$item) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRFToken::validate()) {
        $error = 'Invalid security token.';
    } else {
        try {
            // Capture IP address using Security helper
            $ipAddress = Security::getClientIP();
            
            $stmt = $db->prepare("INSERT INTO enrollments (enrollment_type, item_id, item_title, first_name, last_name, email, phone, student_id, program, year_of_study, experience_level, motivation, expectations, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $type,
                $id,
                $item['title'],
                $_POST['first_name'] ?? '',
                $_POST['last_name'] ?? '',
                $_POST['email'] ?? '',
                $_POST['phone'] ?? '',
                $_POST['student_id'] ?? '',
                $_POST['program'] ?? '',
                $_POST['year_of_study'] ?? '',
                $_POST['experience_level'] ?? 'beginner',
                $_POST['motivation'] ?? '',
                $_POST['expectations'] ?? '',
                $ipAddress
            ]);
            $message = 'Enrollment submitted successfully! We will contact you soon.';
            
            // Regenerate CSRF token after successful submission
            CSRFToken::regenerate();
        } catch(PDOException $e) {
            error_log('[ERROR] Enrollment failed: ' . $e->getMessage());
            $error = 'Enrollment failed. Please try again later.';
        }
    }
}
?>

<div class="page-title light-background">
  <div class="container">
    <h1>Enroll Now</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="index.php">Home</a></li>
        <li class="current">Enroll</li>
      </ol>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body p-4">
            <h3 class="mb-3">Enrolling in: <?php echo htmlspecialchars($item['title']); ?></h3>
            <p class="text-muted mb-4">Type: <?php echo ucfirst($type); ?></p>

            <?php if ($message): ?>
              <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
              <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!$message): ?>
            <form method="POST">
              <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">First Name *</label>
                  <input type="text" class="form-control" name="first_name" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Last Name *</label>
                  <input type="text" class="form-control" name="last_name" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Email *</label>
                  <input type="email" class="form-control" name="email" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Phone</label>
                  <input type="tel" class="form-control" name="phone">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Student ID</label>
                  <input type="text" class="form-control" name="student_id">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Program/Department</label>
                  <input type="text" class="form-control" name="program">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Year of Study</label>
                  <select class="form-select" name="year_of_study">
                    <option value="">Select...</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                    <option value="Graduate">Graduate</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Experience Level</label>
                  <select class="form-select" name="experience_level">
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                  </select>
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label">Why do you want to enroll?</label>
                  <textarea class="form-control" name="motivation" rows="3"></textarea>
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label">What do you hope to achieve?</label>
                  <textarea class="form-control" name="expectations" rows="3"></textarea>
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100">Submit Enrollment</button>
            </form>
            <?php else: ?>
              <a href="index.php" class="btn btn-primary">Back to Home</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
