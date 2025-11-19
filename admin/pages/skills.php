<?php
/**
 * Skills Management Page - Admin Panel
 * CRUD operations for homepage skills/technology areas
 */

define('PAGE_TITLE', 'Skills Management - Khoders Admin');

require_once '../config/database.php';
require_once '../config/csrf.php';

$database = new Database();
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
            $name = trim($_POST['name'] ?? '');
            $icon = trim($_POST['icon'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $order_index = (int)($_POST['order_index'] ?? 0);
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $status = $_POST['status'] ?? 'active';
            
            try {
                if ($action === 'create') {
                    $query = "INSERT INTO skills (name, icon, description, category, order_index, is_featured, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$name, $icon, $description, $category, $order_index, $is_featured, $status]);
                    $message = 'Skill created successfully!';
                } else {
                    $query = "UPDATE skills SET name=?, icon=?, description=?, category=?, order_index=?, is_featured=?, status=? WHERE id=?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$name, $icon, $description, $category, $order_index, $is_featured, $status, $id]);
                    $message = 'Skill updated successfully!';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        } elseif ($action === 'delete') {
            $id = $_POST['id'] ?? 0;
            try {
                $query = "DELETE FROM skills WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$id]);
                $message = 'Skill deleted successfully!';
            } catch (PDOException $e) {
                $error = 'Delete failed: ' . $e->getMessage();
            }
        }
    }
}

// Get all skills
$query = "SELECT * FROM skills ORDER BY is_featured DESC, order_index ASC, name ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get skill for editing if ID provided
$editSkill = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $query = "SELECT * FROM skills WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$editId]);
    $editSkill = $stmt->fetch(PDO::FETCH_ASSOC);
}

$csrfToken = CSRFToken::generate();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0 font-weight-bold">Skills Management</h3>
                    <p class="text-muted mb-0">Manage technology areas displayed on homepage</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#skillModal" onclick="resetForm()">
                    <i class="bi bi-plus-circle"></i> Add New Skill
                </button>
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
                                    <th width="60">Icon</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th width="80">Order</th>
                                    <th width="100">Featured</th>
                                    <th width="100">Status</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($skills)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                            <p class="mb-0 mt-2">No skills yet. Create one to get started!</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($skills as $skill): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input row-checkbox">
                                            </td>
                                            <td>
                                                <i class="bi <?php echo htmlspecialchars($skill['icon']); ?>" style="font-size: 1.5rem; color: #136ad5;"></i>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($skill['name']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($skill['description']); ?></td>
                                            <td>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($skill['category']); ?></span>
                                            </td>
                                            <td><?php echo $skill['order_index']; ?></td>
                                            <td>
                                                <?php if ($skill['is_featured']): ?>
                                                    <span class="badge bg-warning text-dark">Featured</span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-dark">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($skill['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick='editSkill(<?php echo json_encode($skill); ?>)'>
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSkill(<?php echo $skill['id']; ?>, '<?php echo addslashes($skill['name']); ?>')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Skill Modal -->
<div class="modal fade" id="skillModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Skill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="skillForm">
                <?php echo CSRFToken::getFieldHTML(); ?>
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="skillId">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Skill Name *</label>
                            <input type="text" class="form-control" name="name" id="skillName" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Order Index</label>
                            <input type="number" class="form-control" name="order_index" id="orderIndex" value="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bootstrap Icon Class</label>
                            <input type="text" class="form-control" name="icon" id="skillIcon" placeholder="bi-code-slash">
                            <small class="text-muted">Browse icons at <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" name="category" id="skillCategory" placeholder="Development">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="description" id="skillDescription" maxlength="300">
                        <small class="text-muted">Brief description (max 300 characters)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="skillStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured">
                                <label class="form-check-label" for="isFeatured">
                                    <strong>Featured on Homepage</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Skill
                    </button>
                </div>
            </form>
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
function resetForm() {
    document.getElementById('skillForm').reset();
    document.getElementById('formAction').value = 'create';
    document.getElementById('skillId').value = '';
    document.getElementById('modalTitle').textContent = 'Add New Skill';
}

function editSkill(skill) {
    document.getElementById('formAction').value = 'update';
    document.getElementById('skillId').value = skill.id;
    document.getElementById('skillName').value = skill.name;
    document.getElementById('skillIcon').value = skill.icon || '';
    document.getElementById('skillDescription').value = skill.description || '';
    document.getElementById('skillCategory').value = skill.category || '';
    document.getElementById('orderIndex').value = skill.order_index;
    document.getElementById('skillStatus').value = skill.status;
    document.getElementById('isFeatured').checked = skill.is_featured == 1;
    document.getElementById('modalTitle').textContent = 'Edit Skill';
    
    const modal = new bootstrap.Modal(document.getElementById('skillModal'));
    modal.show();
}

function deleteSkill(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"?`)) {
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
.modal-content {
    border-radius: 12px !important;
    border: none !important;
}
.modal-header {
    background: linear-gradient(135deg, #136ad5 0%, #0d4fa3 100%) !important;
    color: white !important;
    border-radius: 12px 12px 0 0 !important;
}
.modal-header .btn-close {
    filter: brightness(0) invert(1);
}
</style>
