<?php
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Program Editor - KHODERS WORLD Admin');
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../config/file-upload.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

$currentPage = 'programs';
$action = $_GET['action'] ?? 'add';
$program_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';
$program = [
    'title' => '', 'slug' => '', 'subtitle' => '', 'description' => '', 'category' => '', 'level' => 'All Levels',
    'duration' => '', 'format' => '', 'sessions' => '', 'projects' => '', 'next_start' => '',
    'instructor_name' => '', 'instructor_image' => '', 'instructor_title' => '', 'hero_image' => '',
    'members_count' => 0, 'rating' => 5.0, 'reviews_count' => 0, 'skills' => '[]', 'benefits' => '[]',
    'curriculum' => '[]', 'testimonials' => '[]', 'status' => 'active'
];

$user = Auth::user();

try {
    $database = new Database();
    $db = $database->getConnection();
    if (!$db) $error = 'Unable to connect to the database.';
} catch (Exception $e) {
    $error = 'Database connection error: ' . $e->getMessage();
    $db = null;
}

if ($db && $action === 'edit' && $program_id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM programs WHERE id = ?");
        $stmt->execute([$program_id]);
        $existingProgram = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingProgram) $program = $existingProgram;
        else $error = 'Program not found.';
    } catch (PDOException $e) {
        $error = 'Error loading program: ' . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_program'])) {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } elseif (!$db) {
        $error = 'Database connection is not available.';
    } else {
        $program = array_merge($program, [
            'title' => $_POST['title'] ?? '', 'slug' => $_POST['slug'] ?? '', 'subtitle' => $_POST['subtitle'] ?? '',
            'description' => $_POST['description'] ?? '', 'category' => $_POST['category'] ?? '',
            'level' => $_POST['level'] ?? 'All Levels', 'duration' => $_POST['duration'] ?? '',
            'format' => $_POST['format'] ?? '', 'sessions' => $_POST['sessions'] ?? '',
            'projects' => $_POST['projects'] ?? '', 'next_start' => $_POST['next_start'] ?? '',
            'instructor_name' => $_POST['instructor_name'] ?? '', 'instructor_title' => $_POST['instructor_title'] ?? '',
            'members_count' => (int)($_POST['members_count'] ?? 0), 'rating' => (float)($_POST['rating'] ?? 5.0),
            'reviews_count' => (int)($_POST['reviews_count'] ?? 0), 'status' => $_POST['status'] ?? 'active'
        ]);

        if (empty($program['slug']) && !empty($program['title'])) {
            $program['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $program['title']), '-'));
        }

        $uploader = new FileUploader('programs', 5 * 1024 * 1024);
        if (!empty($_FILES['hero_image_file']['name'])) {
            $uploadResult = $uploader->upload($_FILES['hero_image_file']);
            if ($uploadResult['success']) {
                if ($action === 'edit' && !empty($program['hero_image'])) $uploader->delete($program['hero_image']);
                $program['hero_image'] = $uploadResult['path'];
            } else $error = 'Hero image upload failed: ' . $uploadResult['error'];
        }
        if (!empty($_FILES['instructor_image_file']['name'])) {
            $uploadResult = $uploader->upload($_FILES['instructor_image_file']);
            if ($uploadResult['success']) {
                if ($action === 'edit' && !empty($program['instructor_image'])) $uploader->delete($program['instructor_image']);
                $program['instructor_image'] = $uploadResult['path'];
            } else $error = 'Instructor image upload failed: ' . $uploadResult['error'];
        }

        $program['skills'] = json_encode(array_filter(array_map(function($i) {
            return ['icon' => $_POST["skill_icon_$i"] ?? '', 'title' => $_POST["skill_title_$i"] ?? '', 'description' => $_POST["skill_desc_$i"] ?? ''];
        }, range(0, 9))));

        $program['benefits'] = json_encode(array_filter($_POST['benefits'] ?? []));
        $program['curriculum'] = json_encode(array_filter(array_map(function($i) {
            return ['title' => $_POST["module_title_$i"] ?? '', 'duration' => $_POST["module_duration_$i"] ?? '', 'lessons' => array_filter(explode("\n", $_POST["module_lessons_$i"] ?? ''))];
        }, range(0, 9))));
        $program['testimonials'] = json_encode(array_filter(array_map(function($i) {
            return ['name' => $_POST["test_name_$i"] ?? '', 'image' => $_POST["test_image_$i"] ?? '', 'rating' => (int)($_POST["test_rating_$i"] ?? 5), 'date' => $_POST["test_date_$i"] ?? '', 'text' => $_POST["test_text_$i"] ?? ''];
        }, range(0, 9))));

        if (empty($program['title'])) {
            $error = 'Program title is required.';
        } else {
            try {
                if ($action === 'edit' && $program_id > 0) {
                    $stmt = $db->prepare("UPDATE programs SET title=?, slug=?, subtitle=?, description=?, category=?, level=?, duration=?, format=?, sessions=?, projects=?, next_start=?, instructor_name=?, instructor_image=?, instructor_title=?, hero_image=?, members_count=?, rating=?, reviews_count=?, skills=?, benefits=?, curriculum=?, testimonials=?, status=?, updated_at=NOW() WHERE id=?");
                    $stmt->execute([$program['title'], $program['slug'], $program['subtitle'], $program['description'], $program['category'], $program['level'], $program['duration'], $program['format'], $program['sessions'], $program['projects'], $program['next_start'], $program['instructor_name'], $program['instructor_image'], $program['instructor_title'], $program['hero_image'], $program['members_count'], $program['rating'], $program['reviews_count'], $program['skills'], $program['benefits'], $program['curriculum'], $program['testimonials'], $program['status'], $program_id]);
                    $message = 'Program updated successfully.';
                } else {
                    $stmt = $db->prepare("INSERT INTO programs (title, slug, subtitle, description, category, level, duration, format, sessions, projects, next_start, instructor_name, instructor_image, instructor_title, hero_image, members_count, rating, reviews_count, skills, benefits, curriculum, testimonials, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    $stmt->execute([$program['title'], $program['slug'], $program['subtitle'], $program['description'], $program['category'], $program['level'], $program['duration'], $program['format'], $program['sessions'], $program['projects'], $program['next_start'], $program['instructor_name'], $program['instructor_image'], $program['instructor_title'], $program['hero_image'], $program['members_count'], $program['rating'], $program['reviews_count'], $program['skills'], $program['benefits'], $program['curriculum'], $program['testimonials'], $program['status']]);
                    $message = 'Program created successfully.';
                }
                if (empty($error)) {
                    header('Location: ?route=programs&message=' . urlencode($message));
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Error saving program: ' . $e->getMessage();
            }
        }
    }
}

$csrfToken = Security::generateCSRFToken();
$skills = json_decode($program['skills'], true) ?? [];
$benefits = json_decode($program['benefits'], true) ?? [];
$curriculum = json_decode($program['curriculum'], true) ?? [];
$testimonials = json_decode($program['testimonials'], true) ?? [];
?>

<div class="content-wrapper">
  <div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title"><?php echo $action === 'edit' ? 'Edit Program' : 'Add New Program'; ?></h4>
          
          <?php if ($message): ?>
            <div class="alert alert-success"><i class="mdi mdi-check-circle-outline"></i> <?php echo htmlspecialchars($message); ?></div>
          <?php endif; ?>
          <?php if ($error): ?>
            <div class="alert alert-danger"><i class="mdi mdi-alert-circle"></i> <?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>
          
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            <input type="hidden" name="save_program" value="1">
            
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#basic">Basic Info</a></li>
              <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#skills">Skills</a></li>
              <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#curriculum">Curriculum</a></li>
              <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#testimonials">Testimonials</a></li>
            </ul>
            
            <div class="tab-content mt-3">
              <div class="tab-pane fade show active" id="basic">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label>Title *</label>
                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($program['title']); ?>" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label>Slug</label>
                    <input type="text" class="form-control" name="slug" value="<?php echo htmlspecialchars($program['slug']); ?>">
                  </div>
                  <div class="col-12 mb-3">
                    <label>Subtitle</label>
                    <input type="text" class="form-control" name="subtitle" value="<?php echo htmlspecialchars($program['subtitle'] ?? ''); ?>">
                  </div>
                  <div class="col-12 mb-3">
                    <label>Description</label>
                    <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($program['description'] ?? ''); ?></textarea>
                  </div>
                  <div class="col-md-4 mb-3">
                    <label>Category</label>
                    <input type="text" class="form-control" name="category" value="<?php echo htmlspecialchars($program['category'] ?? ''); ?>">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label>Level</label>
                    <select class="form-select" name="level">
                      <option value="All Levels" <?php echo $program['level'] === 'All Levels' ? 'selected' : ''; ?>>All Levels</option>
                      <option value="Beginner" <?php echo $program['level'] === 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                      <option value="Intermediate" <?php echo $program['level'] === 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                      <option value="Advanced" <?php echo $program['level'] === 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                    </select>
                  </div>
                  <div class="col-md-4 mb-3">
                    <label>Duration</label>
                    <input type="text" class="form-control" name="duration" value="<?php echo htmlspecialchars($program['duration'] ?? ''); ?>">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label>Format</label>
                    <input type="text" class="form-control" name="format" value="<?php echo htmlspecialchars($program['format'] ?? ''); ?>">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label>Sessions</label>
                    <input type="text" class="form-control" name="sessions" value="<?php echo htmlspecialchars($program['sessions'] ?? ''); ?>">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label>Projects</label>
                    <input type="text" class="form-control" name="projects" value="<?php echo htmlspecialchars($program['projects'] ?? ''); ?>">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label>Next Start</label>
                    <input type="text" class="form-control" name="next_start" value="<?php echo htmlspecialchars($program['next_start'] ?? ''); ?>">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label>Instructor Name</label>
                    <input type="text" class="form-control" name="instructor_name" value="<?php echo htmlspecialchars($program['instructor_name'] ?? ''); ?>">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label>Instructor Title</label>
                    <input type="text" class="form-control" name="instructor_title" value="<?php echo htmlspecialchars($program['instructor_title'] ?? ''); ?>">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label>Instructor Image</label>
                    <input type="file" class="form-control" name="instructor_image_file" accept="image/*">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label>Hero Image</label>
                    <input type="file" class="form-control" name="hero_image_file" accept="image/*">
                  </div>
                  <div class="col-md-2 mb-3">
                    <label>Members</label>
                    <input type="number" class="form-control" name="members_count" value="<?php echo $program['members_count']; ?>">
                  </div>
                  <div class="col-md-2 mb-3">
                    <label>Rating</label>
                    <input type="number" step="0.1" class="form-control" name="rating" value="<?php echo $program['rating']; ?>">
                  </div>
                  <div class="col-md-2 mb-3">
                    <label>Reviews</label>
                    <input type="number" class="form-control" name="reviews_count" value="<?php echo $program['reviews_count']; ?>">
                  </div>
                  <div class="col-md-12 mb-3">
                    <label>Benefits (one per line)</label>
                    <textarea class="form-control" name="benefits[]" rows="5"><?php echo implode("\n", $benefits); ?></textarea>
                  </div>
                </div>
              </div>
              
              <div class="tab-pane fade" id="skills">
                <?php for($i=0; $i<6; $i++): $skill = $skills[$i] ?? []; ?>
                <div class="card mb-2">
                  <div class="card-body">
                    <h6>Skill <?php echo $i+1; ?></h6>
                    <div class="row">
                      <div class="col-md-3"><input type="text" class="form-control" name="skill_icon_<?php echo $i; ?>" placeholder="Icon (bi-code-slash)" value="<?php echo htmlspecialchars($skill['icon'] ?? ''); ?>"></div>
                      <div class="col-md-4"><input type="text" class="form-control" name="skill_title_<?php echo $i; ?>" placeholder="Title" value="<?php echo htmlspecialchars($skill['title'] ?? ''); ?>"></div>
                      <div class="col-md-5"><input type="text" class="form-control" name="skill_desc_<?php echo $i; ?>" placeholder="Description" value="<?php echo htmlspecialchars($skill['description'] ?? ''); ?>"></div>
                    </div>
                  </div>
                </div>
                <?php endfor; ?>
              </div>
              
              <div class="tab-pane fade" id="curriculum">
                <?php for($i=0; $i<4; $i++): $module = $curriculum[$i] ?? []; ?>
                <div class="card mb-2">
                  <div class="card-body">
                    <h6>Module <?php echo $i+1; ?></h6>
                    <input type="text" class="form-control mb-2" name="module_title_<?php echo $i; ?>" placeholder="Title" value="<?php echo htmlspecialchars($module['title'] ?? ''); ?>">
                    <input type="text" class="form-control mb-2" name="module_duration_<?php echo $i; ?>" placeholder="Duration" value="<?php echo htmlspecialchars($module['duration'] ?? ''); ?>">
                    <textarea class="form-control" name="module_lessons_<?php echo $i; ?>" rows="3" placeholder="Lessons (one per line)"><?php echo implode("\n", $module['lessons'] ?? []); ?></textarea>
                  </div>
                </div>
                <?php endfor; ?>
              </div>
              
              <div class="tab-pane fade" id="testimonials">
                <?php for($i=0; $i<3; $i++): $test = $testimonials[$i] ?? []; ?>
                <div class="card mb-2">
                  <div class="card-body">
                    <h6>Testimonial <?php echo $i+1; ?></h6>
                    <div class="row">
                      <div class="col-md-4"><input type="text" class="form-control mb-2" name="test_name_<?php echo $i; ?>" placeholder="Name" value="<?php echo htmlspecialchars($test['name'] ?? ''); ?>"></div>
                      <div class="col-md-4"><input type="text" class="form-control mb-2" name="test_image_<?php echo $i; ?>" placeholder="Image URL" value="<?php echo htmlspecialchars($test['image'] ?? ''); ?>"></div>
                      <div class="col-md-2"><input type="number" class="form-control mb-2" name="test_rating_<?php echo $i; ?>" placeholder="Rating" value="<?php echo $test['rating'] ?? 5; ?>"></div>
                      <div class="col-md-2"><input type="text" class="form-control mb-2" name="test_date_<?php echo $i; ?>" placeholder="Date" value="<?php echo htmlspecialchars($test['date'] ?? ''); ?>"></div>
                      <div class="col-12"><textarea class="form-control" name="test_text_<?php echo $i; ?>" rows="2" placeholder="Testimonial text"><?php echo htmlspecialchars($test['text'] ?? ''); ?></textarea></div>
                    </div>
                  </div>
                </div>
                <?php endfor; ?>
              </div>
            </div>
            
            <div class="mt-3">
              <button type="submit" class="btn btn-primary"><?php echo $action === 'edit' ? 'Update' : 'Create'; ?> Program</button>
              <a href="?route=programs" class="btn btn-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
