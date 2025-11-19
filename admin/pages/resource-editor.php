<?php
/**
 * Resource Editor - Admin Panel
 * Create and edit learning resources with WYSIWYG
 */

define('PAGE_TITLE', 'Resource Editor - Khoders Admin');

require_once '../config/database.php';
require_once '../config/csrf.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';
$resource = null;

// Get resource for editing
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare("SELECT * FROM resources WHERE id = ?");
    $stmt->execute([$id]);
    $resource = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resource) {
        header('Location: ?route=resources');
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRFToken::validate()) {
        $error = 'Invalid security token';
    } else {
        $id = $_POST['id'] ?? null;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $resource_type = $_POST['resource_type'] ?? 'article';
        $category = trim($_POST['category'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $difficulty_level = $_POST['difficulty_level'] ?? 'all';
        $tech_stack = trim($_POST['tech_stack'] ?? '');
        $thumbnail_url = trim($_POST['thumbnail_url'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $is_free = isset($_POST['is_free']) ? 1 : 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $status = $_POST['status'] ?? 'active';
        
        try {
            if ($id) {
                // Update
                $query = "UPDATE resources SET title=?, description=?, resource_type=?, category=?, url=?, difficulty_level=?, tech_stack=?, thumbnail_url=?, author=?, duration=?, is_free=?, is_featured=?, status=?, updated_at=NOW() WHERE id=?";
                $stmt = $db->prepare($query);
                $stmt->execute([$title, $description, $resource_type, $category, $url, $difficulty_level, $tech_stack, $thumbnail_url, $author, $duration, $is_free, $is_featured, $status, $id]);
                $message = 'Resource updated successfully!';
            } else {
                // Create
                $query = "INSERT INTO resources (title, description, resource_type, category, url, difficulty_level, tech_stack, thumbnail_url, author, duration, is_free, is_featured, status, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt = $db->prepare($query);
                $stmt->execute([$title, $description, $resource_type, $category, $url, $difficulty_level, $tech_stack, $thumbnail_url, $author, $duration, $is_free, $is_featured, $status]);
                $message = 'Resource created successfully!';
                $id = $db->lastInsertId();
            }
            
            // Reload resource data
            $stmt = $db->prepare("SELECT * FROM resources WHERE id = ?");
            $stmt->execute([$id]);
            $resource = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

$csrfToken = CSRFToken::generate();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0 font-weight-bold"><?php echo $resource ? 'Edit' : 'Add New'; ?> Resource</h3>
                    <p class="text-muted mb-0">Manage learning resources for the community library</p>
                </div>
                <a href="?route=resources" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" id="resourceForm">
                        <?php echo CSRFToken::getFieldHTML(); ?>
                        <?php if ($resource): ?>
                            <input type="hidden" name="id" value="<?php echo $resource['id']; ?>">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Resource Title *</label>
                                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($resource['title'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Resource Type *</label>
                                <select class="form-select" name="resource_type" required>
                                    <option value="tutorial" <?php echo ($resource['resource_type'] ?? '') === 'tutorial' ? 'selected' : ''; ?>>Tutorial</option>
                                    <option value="article" <?php echo ($resource['resource_type'] ?? 'article') === 'article' ? 'selected' : ''; ?>>Article</option>
                                    <option value="video" <?php echo ($resource['resource_type'] ?? '') === 'video' ? 'selected' : ''; ?>>Video</option>
                                    <option value="course" <?php echo ($resource['resource_type'] ?? '') === 'course' ? 'selected' : ''; ?>>Course</option>
                                    <option value="book" <?php echo ($resource['resource_type'] ?? '') === 'book' ? 'selected' : ''; ?>>Book</option>
                                    <option value="tool" <?php echo ($resource['resource_type'] ?? '') === 'tool' ? 'selected' : ''; ?>>Tool</option>
                                    <option value="documentation" <?php echo ($resource['resource_type'] ?? '') === 'documentation' ? 'selected' : ''; ?>>Documentation</option>
                                    <option value="other" <?php echo ($resource['resource_type'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control tinymce-editor" name="description" rows="6"><?php echo htmlspecialchars($resource['description'] ?? ''); ?></textarea>
                            <small class="text-muted">Rich text editor - add formatting, links, etc.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Resource URL *</label>
                                <input type="url" class="form-control" name="url" value="<?php echo htmlspecialchars($resource['url'] ?? ''); ?>" required placeholder="https://example.com/resource">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Thumbnail Image URL</label>
                                <input type="url" class="form-control" name="thumbnail_url" id="thumbnailUrl" value="<?php echo htmlspecialchars($resource['thumbnail_url'] ?? ''); ?>" placeholder="https://example.com/image.jpg">
                                <small class="text-muted">URL to resource thumbnail image</small>
                            </div>
                        </div>

                        <?php if (!empty($resource['thumbnail_url']) || isset($_POST['thumbnail_url'])): ?>
                            <div class="mb-3">
                                <img id="thumbnailPreview" src="<?php echo htmlspecialchars($resource['thumbnail_url'] ?? ''); ?>" alt="Thumbnail Preview" style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Category</label>
                                <input type="text" class="form-control" name="category" value="<?php echo htmlspecialchars($resource['category'] ?? ''); ?>" placeholder="Web Development">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Difficulty Level</label>
                                <select class="form-select" name="difficulty_level">
                                    <option value="all" <?php echo ($resource['difficulty_level'] ?? 'all') === 'all' ? 'selected' : ''; ?>>All Levels</option>
                                    <option value="beginner" <?php echo ($resource['difficulty_level'] ?? '') === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                                    <option value="intermediate" <?php echo ($resource['difficulty_level'] ?? '') === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                    <option value="advanced" <?php echo ($resource['difficulty_level'] ?? '') === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Duration</label>
                                <input type="text" class="form-control" name="duration" value="<?php echo htmlspecialchars($resource['duration'] ?? ''); ?>" placeholder="2 hours, 30 min, etc.">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tech Stack/Tags</label>
                                <input type="text" class="form-control" name="tech_stack" value="<?php echo htmlspecialchars($resource['tech_stack'] ?? ''); ?>" placeholder="React, Node.js, MongoDB">
                                <small class="text-muted">Comma-separated list</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Author/Creator</label>
                                <input type="text" class="form-control" name="author" value="<?php echo htmlspecialchars($resource['author'] ?? ''); ?>" placeholder="John Doe">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active" <?php echo ($resource['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo ($resource['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="archived" <?php echo ($resource['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_free" id="isFree" <?php echo ($resource['is_free'] ?? true) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="isFree">
                                        <strong>Free Resource</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" <?php echo ($resource['is_featured'] ?? false) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="isFeatured">
                                        <strong>⭐ Featured Resource</strong>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> <?php echo $resource ? 'Update' : 'Create'; ?> Resource
                            </button>
                            <a href="?route=resources" class="btn btn-secondary btn-lg">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '.tinymce-editor',
    height: 400,
    menubar: false,
    plugins: 'lists link image code wordcount',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
    branding: false,
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; }'
});

// Thumbnail preview
document.getElementById('thumbnailUrl')?.addEventListener('change', function() {
    const url = this.value;
    let preview = document.getElementById('thumbnailPreview');
    
    if (url) {
        if (!preview) {
            preview = document.createElement('img');
            preview.id = 'thumbnailPreview';
            preview.style.cssText = 'max-width: 200px; max-height: 150px; border-radius: 8px; margin-top: 10px;';
            this.parentElement.appendChild(preview);
        }
        preview.src = url;
        preview.style.display = 'block';
    } else if (preview) {
        preview.style.display = 'none';
    }
});
</script>

<style>
.card {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.form-label {
    font-weight: 600;
    color: #223a58;
}
</style>
