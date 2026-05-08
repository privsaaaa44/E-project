<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
include_once 'admin_header.php';
$theaters = get_all_theaters($connection);
$classes = get_all_classes($connection);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0">Theater Management</h4>
    <button class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addTheaterModal">
        <i class="fas fa-plus me-2"></i> Add Theater
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm border rounded-pill px-3">
                    <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" id="theaterSearch" class="form-control bg-transparent border-0 shadow-none" placeholder="Search theaters...">
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="theaterTable">
            <thead>
                <tr>
                    <th class="ps-4">THEATER NAME</th>
                    <th>LOCATION</th>
                    <th>SCREENS</th>
                    <th class="text-end pe-4">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($theaters as $theater): ?>
                <tr>
                    <td class="ps-4 fw-bold text-main"><?php echo htmlspecialchars($theater['theater_name']); ?></td>
                    <td><span class="text-muted"><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($theater['location']); ?></span></td>
                    <td><span class="badge bg-light text-dark text-main border px-3 py-2"><?php echo (int)$theater['screens']; ?> Screens</span></td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <?php $seat_caps = get_theater_seat_capacity($connection, $theater['id']); ?>
                            <button class="btn btn-sm btn-light border edit-theater-btn"
                                data-id="<?php echo $theater['id']; ?>"
                                data-name="<?php echo htmlspecialchars($theater['theater_name']); ?>"
                                data-location="<?php echo htmlspecialchars($theater['location']); ?>"
                                data-screens="<?php echo $theater['screens']; ?>"
                                <?php foreach ($classes as $class): 
                                    $cap = $seat_caps[$class['id']] ?? 0;
                                    echo ' data-seat-capacity' . $class['id'] . '="' . $cap . '"';
                                endforeach; ?>
                            ><i class="fas fa-edit text-primary"></i></button>
                            <form action="../code.php" method="POST" class="d-inline">
                                <input type="hidden" name="admin_action" value="delete_theater">
                                <input type="hidden" name="id" value="<?php echo $theater['id']; ?>">
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
<div class="modal fade" id="addTheaterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="fw-bold">Add Theater</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../code.php" method="POST">
                <input type="hidden" name="admin_action" value="add_theater">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">THEATER NAME</label>
                        <input type="text" name="theater_name" class="form-control" placeholder="Cineplex Galaxy" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">LOCATION</label>
                        <input type="text" name="location" class="form-control" placeholder="Downtown, NYC" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NUMBER OF SCREENS</label>
                        <input type="number" name="screens" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">SEAT CAPACITY BY CLASS</label>
                        <div class="row g-2">
                            <?php foreach ($classes as $class): ?>
                            <div class="col-6">
                                <label class="form-label small text-muted"><?php echo htmlspecialchars($class['class_name']); ?></label>
                                <input type="number" name="seat_capacity[<?php echo $class['id']; ?>]" class="form-control form-control-sm" placeholder="Seats" min="0" value="0">
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Set number of seats for each seat class</small>
                    </div>
                </div>
                <div class="modal-footer p-3 border-top-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Theater</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editTheaterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="fw-bold">Update Theater</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../code.php" method="POST">
                <input type="hidden" name="admin_action" value="update_theater">
                <input type="hidden" id="editTheaterId" name="id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">THEATER NAME</label>
                        <input type="text" id="editTheaterName" name="theater_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">LOCATION</label>
                        <input type="text" id="editTheaterLocation" name="location" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NUMBER OF SCREENS</label>
                        <input type="number" id="editTheaterScreens" name="screens" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">SEAT CAPACITY BY CLASS</label>
                        <div class="row g-2" id="editSeatCapacities">
                            <?php foreach ($classes as $class): ?>
                            <div class="col-6">
                                <label class="form-label small text-muted"><?php echo htmlspecialchars($class['class_name']); ?></label>
                                <input type="number" name="seat_capacity[<?php echo $class['id']; ?>]" id="editSeatCapacity<?php echo $class['id']; ?>" class="form-control form-control-sm" placeholder="Seats" min="0" value="0">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-3 border-top-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Update Theater</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const theaterSearch = document.getElementById('theaterSearch');
    if (theaterSearch) {
        theaterSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('#theaterTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
            });
        });
    }

    const editTheaterModal = new bootstrap.Modal(document.getElementById('editTheaterModal'));
    document.querySelectorAll('.edit-theater-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editTheaterId').value = this.dataset.id;
            document.getElementById('editTheaterName').value = this.dataset.name;
            document.getElementById('editTheaterLocation').value = this.dataset.location;
            document.getElementById('editTheaterScreens').value = this.dataset.screens;
            // Set seat capacities
            <?php foreach ($classes as $class): ?>
            const seatCap<?php echo $class['id']; ?> = document.getElementById('editSeatCapacity<?php echo $class['id']; ?>');
            if (seatCap<?php echo $class['id']; ?>) {
                seatCap<?php echo $class['id']; ?>.value = this.dataset.seatCapacity<?php echo $class['id']; ?> || 0;
            }
            <?php endforeach; ?>
            editTheaterModal.show();
        });
    });
});
</script>

<?php include_once 'admin_footer.php'; ?>
