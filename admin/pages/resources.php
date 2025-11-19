$db = $database->getConnection();

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRFToken::validate()) {
        $error = 'Invalid security token';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create' || $action === 'update') {
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
                if ($action === 'create') {
                    $query = "INSERT INTO resources (title, description, resource_type, category, url, difficulty_level, tech_stack, thumbnail_url, author, duration, is_free, is_featured, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$title, $description, $resource_type, $category, $url, $difficulty_level, $tech_stack, $thumbnail_url, $author, $duration, $is_free, $is_featured, $status]);
                    $message = 'Resource created successfully!';
                } else {
                    $query = "UPDATE resources SET title=?, description=?, resource_type=?, category=?, url=?, difficulty_level=?, tech_stack=?, thumbnail_url=?, author=?, duration=?, is_free=?, is_featured=?, status=? WHERE id=?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$title, $description, $resource_type, $category, $url, $difficulty_level, $tech_stack, $thumbnail_url, $author, $duration, $is_free, $is_featured, $status, $id]);
                    $message = 'Resource updated successfully!';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        } elseif ($action === 'delete') {
            $id = $_POST['id'] ?? 0;
            try {
                $query = "DELETE FROM resources WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$id]);
                $message = 'Resource deleted successfully!';
            } catch (PDOException $e) {
                $error = 'Delete failed: ' . $e->getMessage();
            }
        }
    }
}

// Pagination
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM resources";
$countStmt = $db->prepare($countQuery);
$countStmt->execute();
$totalResources = $countStmt->fetch()['total'];
$totalPages = ceil($totalResources / $perPage);

// Get resources
$query = "SELECT * FROM resources ORDER BY is_featured DESC, created_at DESC LIMIT ? OFFSET ?";
$stmt = $db->prepare($query);
$stmt->execute([$perPage, $offset]);
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

$csrfToken = CSRFToken::generate();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0 font-weight-bold">Learning Resources</h3>
                    <p class="text-muted mb-0">Manage curated learning materials for the community</p>
                </div>
                <a href="?route=resource-editor" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add New Resource
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
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th width="80">Thumbnail</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Level</th>
                                    <th width="80">Featured</th>
                                    <th width="80">Free</th>
                                    <th width="80">Status</th>
                                    <th width="80">Views</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($resources)): ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">
                                            <i class="bi bi-journal-code" style="font-size: 3rem;"></i>
                                            <p class="mb-0 mt-2">No resources yet. Add one to get started!</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($resources as $resource): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input row-checkbox">
                                            </td>
                                            <td>
                                                <?php if (!empty($resource['thumbnail_url'])): ?>
                                                    <img src="<?php echo htmlspecialchars($resource['thumbnail_url']); ?>" 
                                                         alt="Thumbnail" 
                                                         style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                <?php else: ?>
                                                    <div style="width:60px; height:40px; background:#f5f5f5; border-radius:4px; display:flex; align-items:center; justify-content:center;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($resource['title']); ?></strong>
                                                <?php if (!empty($resource['author'])): ?>
                                                    <br><small class="text-muted">by <?php echo htmlspecialchars($resource['author']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo ucfirst($resource['resource_type']); ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars($resource['category']); ?></td>
                                            <td>
                                                <span class="badge bg-info"><?php echo ucfirst($resource['difficulty_level']); ?></span>
                                            </td>
                                            <td>
                                                <?php if ($resource['is_featured']): ?>
                                                    <span class="badge bg-warning text-dark">Yes</span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-dark">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($resource['is_free']): ?>
                                                    <span class="badge bg-success">Free</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Paid</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($resource['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo number_format($resource['views']); ?></td>
                                            <td>
                                                <a href="?route=resource-editor&id=<?php echo $resource['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?php echo htmlspecialchars($resource['url']); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteResource(<?php echo $resource['id']; ?>, '<?php echo addslashes($resource['title']); ?>')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?route=resources&p=<?php echo $page - 1; ?>">Previous</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?route=resources&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?route=resources&p=<?php echo $page + 1; ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form method="POST" id="deleteForm" style="display:none;">
    <?php echo CSRFToken::getFieldHTML(); ?>
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
function deleteResource(id, title) {
    if (confirm(`Are you sure you want to delete "${title}"?`))  {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

// Select all functionality
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>

<style>
.table tbody tr {
    transition: all 0.2s ease !important;
}
.table tbody tr:hover {
    transform: translateX(2px);
    box-shadow: -4px 0 0 #136ad5;
}
</style>
