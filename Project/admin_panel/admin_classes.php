<?php 
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
include_once 'admin_header.php'; 
$classes = get_all_classes($connection); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0">Seat Class Management</h4>
    <button class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addClassModal">
        <i class="fas fa-plus me-2"></i> Add Seat Class
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold">All Seat Classes</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">CLASS NAME</th>
                    <th class="text-end pe-4">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $class): ?>
                <tr>
                    <td class="ps-4 fw-bold text-main"><?php echo htmlspecialchars($class['class_name']); ?></td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-light border edit-class-btn" 
                                data-id="<?php echo $class['id']; ?>"
                                data-name="<?php echo htmlspecialchars($class['class_name']); ?>"
                            ><i class="fas fa-edit text-primary"></i></button>
                            <form action="../code.php" method="POST" class="d-inline">
                                <input type="hidden" name="admin_action" value="delete_class">
                                <input type="hidden" name="id" value="<?php echo $class['id']; ?>">
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
<div class="modal fade" id="addClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="fw-bold">Add Seat Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../code.php" method="POST">
                <input type="hidden" name="admin_action" value="add_class">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">CLASS NAME</label>
                        <input type="text" name="class_name" class="form-control" placeholder="Gold, Platinum, etc." required>
                    </div>
                </div>
                <div class="modal-footer p-3 border-top-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="fw-bold">Update Seat Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../code.php" method="POST">
                <input type="hidden" name="admin_action" value="update_class">
                <input type="hidden" name="id" id="editClassId">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">CLASS NAME</label>
                        <input type="text" name="class_name" id="editClassName" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer p-3 border-top-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Update Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editClassModal = new bootstrap.Modal(document.getElementById('editClassModal'));
    document.querySelectorAll('.edit-class-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editClassId').value = this.dataset.id;
            document.getElementById('editClassName').value = this.dataset.name;
            editClassModal.show();
        });
    });
});
</script>

<?php include_once 'admin_footer.php'; ?>
