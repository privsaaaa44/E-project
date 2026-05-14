<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
include_once 'admin_header.php';
$theaters = get_all_theaters($connection);
$shows = get_all_shows($connection);
$classes = get_all_classes($connection);

// Single query - now_showing movies only
$now_showing_result = mysqli_query($connection, "SELECT * FROM movies WHERE movie_status = 'now_showing'");
$now_showing_movies = mysqli_fetch_all($now_showing_result, MYSQLI_ASSOC);
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-4">Create New Show</h5>
            <form action="../code.php" method="POST">
                <input type="hidden" name="admin_action" value="add_show">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">MOVIE</label>
                    <select name="movie_id" class="form-select py-2" required>
                        <option selected disabled>Select movie...</option>
                        <?php foreach ($now_showing_movies as $movie): ?>
                            <option  value="<?php echo $movie['id']; ?>">
                                <?php echo htmlspecialchars($movie['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">THEATER</label>
                    <select name="theater_id" class="form-select py-2" required>
                        <option selected disabled>Select theater...</option>
                        <?php foreach ($theaters as $theater): ?>
                            <option value="<?php echo $theater['id']; ?>"><?php echo htmlspecialchars($theater['theater_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted">DATE</label>
                         <input type="date" name="show_date" id="showDate" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted">TIME</label>
                        <input type="time" name="show_time" id="showTime" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted"> PRICING</label>
                    <div class="row g-2">
                        <?php foreach ($classes as $class): ?>
                        <div class="col-6">
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="has_class[<?php echo $class['id']; ?>]" id="hasClass<?php echo $class['id']; ?>" value="1" checked>
                                <label class="form-check-label fw-bold" for="hasClass<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></label>
                            </div>
                            <input type="number" name="class_price[<?php echo $class['id']; ?>]" class="form-control form-control-sm" placeholder="Price" value="0">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Create Show</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold">Active Shows</h6>
                <div class="input-group input-group-sm border rounded-pill px-3" style="width: 250px;">
                    <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" id="showSearch" class="form-control bg-transparent border-0 shadow-none" placeholder="Search shows...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="showTable">
                    <thead>
                        <tr>
                            <th class="ps-4">MOVIE / THEATER</th>
                            <th>SCHEDULE</th>
                            <th>PRICING</th>
                            <th class="text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shows as $show): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-main"><?php echo htmlspecialchars($show['movie_title'] ?: 'N/A'); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($show['theater_name'] ?: 'N/A'); ?></div>
                            </td>
                            <td>
                                <div class="small fw-semibold"><?php echo date('M d, Y', strtotime($show['show_date'])); ?></div>
                                <div class="text-muted small"><?php echo date('h:i A', strtotime($show['show_time'])); ?></div>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <?php foreach ($classes as $class): 
                                        $price_key = strtolower($class['class_name']) . '_price';
                                        $price = $show[$price_key] ?? 0;
                                        $initial = strtoupper(substr($class['class_name'], 0, 1));
                                        if ($price > 0): ?>
                                    <span class="badge bg-light text-primary border px-2 py-1"><?php echo $initial; ?>: <?php echo (int)$price; ?></span>
                                    <?php endif; endforeach; ?>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-light border edit-show-btn"
                                        data-id="<?php echo $show['id']; ?>"
                                        data-movie-id="<?php echo $show['movie_id']; ?>"
                                        data-theater-id="<?php echo $show['theater_id']; ?>"
                                        data-date="<?php echo $show['show_date']; ?>"
                                        data-time="<?php echo $show['show_time']; ?>"
                                    ><i class="fas fa-edit text-primary"></i></button>
                                    <form action="../code.php" method="POST" class="d-inline">
                                        <input type="hidden" name="admin_action" value="delete_show">
                                        <input type="hidden" name="id" value="<?php echo $show['id']; ?>">
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
    </div>
</div>

<!-- Edit Show Modal -->
<div class="modal fade" id="editShowModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="fw-bold">Update Show Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../code.php" method="POST">
                <input type="hidden" name="admin_action" value="update_show">
                <input type="hidden" name="id" id="editShowId">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">MOVIE</label>
                        <select name="movie_id" id="editShowMovie" class="form-select" required>
                            <?php foreach ($now_showing_movies as $movie): ?>
                                <option value="<?php echo $movie['id']; ?>">
                                    <?php echo htmlspecialchars($movie['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">THEATER</label>
                        <select name="theater_id" id="editShowTheater" class="form-select" required>
                            <?php foreach ($theaters as $theater): ?>
                                <option value="<?php echo $theater['id']; ?>"><?php echo htmlspecialchars($theater['theater_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">DATE</label>
                            <input type="date" name="show_date" id="editShowDate" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">TIME</label>
                            <input type="time" name="show_time" id="editShowTime" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">SEAT CLASSES & PRICING</label>
                        <div class="row g-2">
                            <?php foreach ($classes as $class): ?>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted"><?php echo htmlspecialchars($class['class_name']); ?></label>
                                <input type="number" name="class_price[<?php echo $class['id']; ?>]" id="editClassPrice<?php echo $class['id']; ?>" class="form-control form-control-sm" placeholder="Price">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Update Show</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Date min = today
const today = new Date();
const yyyy = today.getFullYear();
const mm = String(today.getMonth() + 1).padStart(2, '0');
const dd = String(today.getDate()).padStart(2, '0');
const todayStr = `${yyyy}-${mm}-${dd}`;

const showDate = document.getElementById('showDate');
const showTime = document.getElementById('showTime');

if (showDate) showDate.min = todayStr;

// Agar aaj ki date select ho toh time bhi current se aage
function updateTimeMin() {
    if (!showDate || !showTime) return;
    if (showDate.value === todayStr) {
        const hh = String(today.getHours()).padStart(2, '0');
        const min = String(today.getMinutes()).padStart(2, '0');
        showTime.min = `${hh}:${min}`;
    } else {
        showTime.min = '';
    }
}

if (showDate) {
    showDate.addEventListener('change', updateTimeMin);
    updateTimeMin(); // page load pe bhi check karo
}
document.addEventListener('DOMContentLoaded', function() {
    const showSearch = document.getElementById('showSearch');
    if (showSearch) {
        showSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('#showTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
            });
        });
    }

    const editModal = new bootstrap.Modal(document.getElementById('editShowModal'));
    document.querySelectorAll('.edit-show-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editShowId').value = this.dataset.id;
            document.getElementById('editShowMovie').value = this.dataset.movieId;
            document.getElementById('editShowTheater').value = this.dataset.theaterId;
            document.getElementById('editShowDate').value = this.dataset.date;
            document.getElementById('editShowTime').value = this.dataset.time;
            editModal.show();
        });
    });
});
// Edit modal - date/time min restriction
const editShowDate = document.getElementById('editShowDate');
const editShowTime = document.getElementById('editShowTime');

if (editShowDate) editShowDate.min = todayStr;

function updateEditTimeMin() {
    if (!editShowDate || !editShowTime) return;
    if (editShowDate.value === todayStr) {
        const hh = String(today.getHours()).padStart(2, '0');
        const min = String(today.getMinutes()).padStart(2, '0');
        editShowTime.min = `${hh}:${min}`;
    } else {
        editShowTime.min = '';
    }
}

if (editShowDate) {
    editShowDate.addEventListener('change', updateEditTimeMin);
}

// Jab edit modal open ho tab bhi check karo
document.querySelectorAll('.edit-show-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        setTimeout(updateEditTimeMin, 100); // modal open hone ke baad run karo
    });
});
</script>

<?php include_once 'admin_footer.php'; ?>