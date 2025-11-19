<?php
/**
 * KHODERS WORLD Admin Blog Editor
 * Add or edit blog posts
 */

// This page should only be included through the router
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'Blog Editor - KHODERS WORLD Admin');
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../config/file-upload.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

// Initialize variables
$currentPage = 'blog';
$action = $_GET['action'] ?? 'add';
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';
$post = [
    'title' => '',
    'slug' => '',
    'content' => '',
    'excerpt' => '',
    'author' => '',
    'featured_image' => '',
    'featured_image_alt' => '',
    'category' => '',
    'tags' => '',
    'is_featured' => 0,
    'status' => 'draft'
];

// Get current user
$user = Auth::user();

// Database connection
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        $error = 'Unable to connect to the database. Please verify database credentials and try again.';
    }
} catch (Exception $e) {
    $error = 'Database connection error: ' . $e->getMessage();
    $db = null;
}

// Check if editing existing blog post
if ($db && $action === 'edit' && $post_id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $existingPost = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingPost) {
            $post = $existingPost;
        } else {
            $error = 'Blog post not found.';
        }
    } catch (PDOException $e) {
        $error = 'Error loading blog post: ' . $e->getMessage();
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_post'])) {
    // Validate CSRF token
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } elseif (!$db) {
        $error = 'Database connection is not available. Cannot save blog post.';
    } else {
        // Get form data
        $post['title'] = $_POST['title'] ?? '';
        $post['slug'] = $_POST['slug'] ?? '';
        $post['content'] = $_POST['content'] ?? '';
        $post['excerpt'] = $_POST['excerpt'] ?? '';
        $post['author'] = $_POST['author'] ?? '';
        $post['featured_image'] = $_POST['featured_image'] ?? '';
        $post['featured_image_alt'] = $_POST['featured_image_alt'] ?? '';
        $post['category'] = $_POST['category'] ?? '';
        $post['tags'] = $_POST['tags'] ?? '';
        $post['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
        $post['status'] = $_POST['status'] ?? 'draft';
        
        // Handle featured image upload if file is provided
        if (!empty($_FILES['featured_image_file']) && $_FILES['featured_image_file']['error'] === UPLOAD_ERR_OK) {
            $uploader = new FileUploader('blog', 5 * 1024 * 1024); // 5MB limit
            $uploadResult = $uploader->upload($_FILES['featured_image_file']);
            
            if ($uploadResult['success']) {
                // Delete old image if editing
                if ($action === 'edit' && !empty($post['featured_image'])) {
                    $uploader->delete($post['featured_image']);
                }
                $post['featured_image'] = $uploadResult['path'];
            } else {
                $error = 'Featured image upload failed: ' . $uploadResult['error'];
            }
        }
        
        // Generate slug if not provided
        if (empty($post['slug']) && !empty($post['title'])) {
            $post['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $post['title']), '-'));
        }
        
        // Basic validation
        if (empty($post['title'])) {
            $error = 'Blog title is required.';
        } elseif (empty($post['content'])) {
            $error = 'Blog content is required.';
        } else {
            try {
                if ($action === 'edit' && $post_id > 0) {
                    // Update existing blog post
                    $stmt = $db->prepare("UPDATE blog_posts SET title = ?, slug = ?, content = ?, excerpt = ?, author = ?, featured_image = ?, featured_image_alt = ?, category = ?, tags = ?, is_featured = ?, status = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([
                        $post['title'],
                        $post['slug'],
                        $post['content'],
                        $post['excerpt'],
                        $post['author'],
                        $post['featured_image'],
                        $post['featured_image_alt'],
                        $post['category'],
                        $post['tags'],
                        $post['is_featured'],
                        $post['status'],
                        $post_id
                    ]);
                    $message = 'Blog post updated successfully.';
                } else {
                    // Insert new blog post
                    $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, content, excerpt, author, featured_image, featured_image_alt, category, tags, is_featured, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    $stmt->execute([
                        $post['title'],
                        $post['slug'],
                        $post['content'],
                        $post['excerpt'],
                        $post['author'],
                        $post['featured_image'],
                        $post['featured_image_alt'],
                        $post['category'],
                        $post['tags'],
                        $post['is_featured'],
                        $post['status']
                    ]);
                    $message = 'Blog post created successfully.';
                    $action = 'add';
                    // Reset form
                    $post = [
                        'title' => '',
                        'slug' => '',
                        'content' => '',
                        'excerpt' => '',
                        'author' => '',
                        'featured_image' => '',
                        'featured_image_alt' => '',
                        'category' => '',
                        'tags' => '',
                        'status' => 'draft'
                    ];
                }
            } catch (PDOException $e) {
                $error = 'Error saving blog post: ' . $e->getMessage();
            }
        }
    }
}

// Generate CSRF token
$csrfToken = Security::generateCSRFToken();

// Determine page title based on action
$pageTitle = ($action === 'edit') ? 'Edit Blog Post' : 'Create New Blog Post';
?>

<div class="content-wrapper">
  <div class="row">
    <div class="col-lg-9 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title"><?php echo $pageTitle; ?></h4>
          <p class="card-subtitle">Write and publish blog posts</p>
          
          <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="mdi mdi-check-circle-outline"></i> <?php echo htmlspecialchars($message); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          
          <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="mdi mdi-alert-circle"></i> <?php echo htmlspecialchars($error); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          
          <form method="POST" class="forms-sample" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <input type="hidden" name="save_post" value="1">
            
            <div class="mb-3">
              <label for="title" class="form-label">Blog Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="title" name="title" 
                     value="<?php echo htmlspecialchars($post['title']); ?>" 
                     placeholder="Enter blog title" required onkeyup="generateSlug()">
            </div>
            
            <div class="mb-3">
              <label for="slug" class="form-label">Slug (URL-friendly)</label>
              <input type="text" class="form-control" id="slug" name="slug" 
                     value="<?php echo htmlspecialchars($post['slug']); ?>" 
                     placeholder="Generated from title">
              <small class="form-text text-muted">Used in URLs. Leave blank to auto-generate from title.</small>
            </div>
            
            <div class="mb-3">
              <label for="excerpt" class="form-label">Excerpt</label>
              <textarea class="form-control" id="excerpt" name="excerpt" 
                        rows="2" placeholder="Short summary of the blog post"><?php echo htmlspecialchars($post['excerpt']); ?></textarea>
            </div>
            
            <div class="mb-3">
              <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
              <textarea class="form-control tinymce-editor" id="content" name="content" 
                        rows="8" placeholder="Write your blog post content here" required><?php echo htmlspecialchars($post['content']); ?></textarea>
              <small class="form-text text-muted">Rich text editor with formatting options.</small>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="author" class="form-label">Author</label>
                  <input type="text" class="form-control" id="author" name="author" 
                         value="<?php echo htmlspecialchars($post['author']); ?>" 
                         placeholder="Author name">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="category" class="form-label">Category</label>
                  <input type="text" class="form-control" id="category" name="category" 
                         value="<?php echo htmlspecialchars($post['category']); ?>" 
                         placeholder="e.g., Technology, News">
                </div>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="tags" class="form-label">Tags</label>
              <input type="text" class="form-control" id="tags" name="tags" 
                     value="<?php echo htmlspecialchars($post['tags']); ?>" 
                     placeholder="Separate tags with commas">
            </div>
            
            <div class="mb-3">
              <label for="featured_image_file" class="form-label">Featured Image</label>
              <input type="file" class="form-control" id="featured_image_file" name="featured_image_file" accept="image/*" onchange="previewImage(this, 'featured_image_preview')">
              <small class="form-text text-muted">JPG, PNG, WebP or GIF (Max 5MB)</small>
              <?php if (!empty($post['featured_image'])): ?>
                <div class="mt-2">
                  <label class="form-text text-muted d-block">Current Image:</label>
                  <img id="featured_image_preview" src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="Featured image" style="max-width: 200px; max-height: 150px;" class="img-thumbnail">
                </div>
              <?php else: ?>
                <div class="mt-2">
                  <img id="featured_image_preview" style="display: none; max-width: 200px; max-height: 150px;" class="img-thumbnail">
                </div>
              <?php endif; ?>
            </div>
            
            <div class="mb-3">
              <label for="featured_image_alt" class="form-label">Featured Image Alt Text</label>
              <input type="text" class="form-control" id="featured_image_alt" name="featured_image_alt" 
                     value="<?php echo htmlspecialchars($post['featured_image_alt']); ?>" 
                     placeholder="Describe the image for accessibility">
            </div>
            
            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" id="status" name="status">
                <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                <option value="archived" <?php echo $post['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
              </select>
            </div>
            
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" <?php echo $post['is_featured'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="is_featured">
                  <strong>⭐ Featured Post</strong> <small class="text-muted">(Display prominently on homepage)</small>
                </label>
              </div>
            </div>
            
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="mdi mdi-content-save"></i> <?php echo ($action === 'edit') ? 'Update' : 'Create'; ?> Post
              </button>
              <a href="?route=blog" class="btn btn-outline-secondary">
                <i class="mdi mdi-arrow-left"></i> Back to Blog
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function previewImage(input, previewId) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview = document.getElementById(previewId);
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function generateSlug() {
    const title = document.getElementById('title').value;
    if (title) {
        const slug = title.toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    }
}
</script>

<!-- TinyMCE WYSIWYG Editor -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '.tinymce-editor',
    height: 500,
    menubar: true,
    plugins: 'lists link image code table wordcount fullscreen preview',
    toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code fullscreen preview',
    branding: false,
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; }'
});
</script>
