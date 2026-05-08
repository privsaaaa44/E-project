<?php 
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
include_once 'admin_header.php'; 
$reviews = get_all_reviews($connection); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0">Review Management</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold">User Reviews</h6>
        <div class="input-group input-group-sm border rounded-pill px-3" style="width: 250px;">
            <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-search"></i></span>
            <input type="text" id="reviewSearch" class="form-control bg-transparent border-0 shadow-none" placeholder="Search reviews...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="reviewTable">
            <thead>
                <tr>
                    <th class="ps-4">USER</th>
                    <th>MOVIE</th>
                    <th>RATING</th>
                    <th>REVIEW</th>
                    <th>DATE</th>
                    <th class="text-end pe-4">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review): ?>
                <tr>
                    <td class="ps-4 fw-bold small text-main"><?php echo htmlspecialchars($review['user_name'] ?: 'N/A'); ?></td>
                    <td class="fw-semibold small"><?php echo htmlspecialchars($review['movie_title'] ?: 'N/A'); ?></td>
                    <td>
                        <div class="text-warning small">
                            <?php for($i=1;$i<=5;$i++) echo ($i <= $review['rating']) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star text-muted"></i>'; ?>
                        </div>
                    </td>
                    <td><p class="mb-0 text-muted small" style="max-width: 300px;"><?php echo htmlspecialchars($review['review']); ?></p></td>
                    <td><span class="text-muted small"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span></td>
                    <td class="text-end pe-4">
                        <form action="../code.php" method="POST" class="d-inline">
                            <input type="hidden" name="admin_action" value="delete_review">
                            <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-light border" onclick="return confirm('Are you sure you want to delete this review?')">
                                <i class="fas fa-trash text-danger"></i>
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
    const reviewSearch = document.getElementById('reviewSearch');
    if (reviewSearch) {
        reviewSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('#reviewTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
            });
        });
    }
});
</script>

<?php include_once 'admin_footer.php'; ?>
