<?php 
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
include_once 'admin_header.php'; 
$categories = get_all_categories($connection); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0">Category Management</h4>
    <button class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="fas fa-plus me-2"></i> Add Category
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold">All Categories</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">CATEGORY NAME</th>
                    <th>STATUS</th>
                    <th class="text-end pe-4">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                <tr>
                    <td class="ps-4 fw-bold text-main"><?php echo htmlspecialchars($category['category_name']); ?></td>
                    <td>
                        <span class="badge bg-<?php echo ($category['status'] === 'Active') ? 'success' : 'secondary'; ?>-subtle text-<?php echo ($category['status'] === 'Active') ? 'success' : 'secondary'; ?> border px-2 py-1">
                            <?php echo $category['status']; ?>
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-light border edit-category-btn" 
                                data-id="<?php echo $category['id']; ?>"
                                data-name="<?php echo htmlspecialchars($category['category_name']); ?>"
                                data-status="<?php echo $category['status']; ?>"
                            ><i class="fas fa-edit text-primary"></i></button>
                            <form action="../code.php" method="POST" class="d-inline">
                                <input type="hidden" name="admin_action" value="delete_category">
                                <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-light border" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="fw-bold">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../code.php" method="POST">
                <input type="hidden" name="admin_action" value="add_category">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">CATEGORY NAME</label>
                        <input type="text" name="category_name" class="form-control" placeholder="Action, Drama, etc." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">STATUS</label>
                        <select name="status" class="form-select">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer p-3 border-top-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="fw-bold">Update Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../code.php" method="POST">
                <input type="hidden" name="admin_action" value="update_category">
                <input type="hidden" name="id" id="editCategoryId">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">CATEGORY NAME</label>
                        <input type="text" name="category_name" id="editCategoryName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">STATUS</label>
                        <select name="status" id="editCategoryStatus" class="form-select">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer p-3 border-top-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editCategoryModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    document.querySelectorAll('.edit-category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editCategoryId').value = this.dataset.id;
            document.getElementById('editCategoryName').value = this.dataset.name;
            document.getElementById('editCategoryStatus').value = this.dataset.status;
            editCategoryModal.show();
        });
    });
});
</script>

<style>
.bg-success-subtle { background-color: #dcfce7 !important; }
.bg-secondary-subtle { background-color: #f1f5f9 !important; }
.text-success { color: #166534 !important; }
.text-secondary { color: #475569 !important; }
</style>

<?php include_once 'admin_footer.php'; ?>
