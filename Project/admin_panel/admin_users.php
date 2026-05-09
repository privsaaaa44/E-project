<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
include_once 'admin_header.php';
$users = get_all_users($connection);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0">User Management</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold">Registered Users</h6>
        <div class="input-group input-group-sm border rounded-pill px-3" style="width: 250px;">
            <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-search"></i></span>
            <input type="text" id="userSearch" class="form-control bg-transparent border-0 shadow-none" placeholder="Search users...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="userTable">
            <thead>
                <tr>
                    <th class="ps-4">USER</th>
                    <th>CONTACT</th>
                    <th>ROLE</th>
                    <th>STATUS</th>
                    <th class="text-end pe-4">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): 
                    if ($user['role'] === 'admin') continue;
                ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=random" class="rounded-circle" width="32">
                            <div class="fw-bold text-main"><?php echo htmlspecialchars($user['name']); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="small fw-semibold"><?php echo htmlspecialchars($user['email']); ?></div>
                        <div class="text-muted small"><?php echo htmlspecialchars($user['phone'] ?: '-'); ?></div>
                    </td>
                    <td>
                        <span class="badge bg-<?php echo ($user['role'] === 'admin') ? 'primary' : 'light text-dark border'; ?> px-2 py-1">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?php echo ($user['status'] === 'Active') ? 'success' : 'secondary'; ?>-subtle text-<?php echo ($user['status'] === 'Active') ? 'success' : 'secondary'; ?> border px-2 py-1">
                            <?php echo $user['status'] ?: 'Active'; ?>
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <form action="../code.php" method="POST" class="d-inline">
                            <input type="hidden" name="admin_action" value="toggle_user_status">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <input type="hidden" name="status" value="<?php echo ($user['status'] === 'Active') ? 'Inactive' : 'Active'; ?>">
                            <button type="submit" class="btn btn-sm btn-light border small px-3">
                                Toggle Status
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userSearch = document.getElementById('userSearch');
    if (userSearch) {
        userSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('#userTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
            });
        });
    }
});
</script>

<style>
.bg-success-subtle { background-color: #dcfce7 !important; }
.bg-secondary-subtle { background-color: #f1f5f9 !important; }
</style>

<?php include_once 'admin_footer.php'; ?>
