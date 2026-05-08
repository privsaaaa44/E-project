<?php
include_once 'connection.php';
include_once 'header.php';

// Login check
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    echo "<script>window.location.href = 'login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// Fetch bookings based on role
if ($is_admin) {
    // Admin sees all bookings
    $sql = "SELECT b.*, u.name as user_name, m.title as movie_title, t.theater_name, 
                   c.class_name, s.show_date, s.show_time
            FROM bookings b
            LEFT JOIN users u ON u.id = b.user_id
            LEFT JOIN shows s ON s.id = b.show_id
            LEFT JOIN movies m ON m.id = s.movie_id
            LEFT JOIN theaters t ON t.id = s.theater_id
            LEFT JOIN classes c ON c.id = b.class_id
            ORDER BY b.id DESC";
    $heading = "All Bookings";
} else {
    // User sees only their bookings
    $sql = "SELECT b.*, m.title as movie_title, t.theater_name, 
                   c.class_name, s.show_date, s.show_time
            FROM bookings b
            LEFT JOIN shows s ON s.id = b.show_id
            LEFT JOIN movies m ON m.id = s.movie_id
            LEFT JOIN theaters t ON t.id = s.theater_id
            LEFT JOIN classes c ON c.id = b.class_id
            WHERE b.user_id = $user_id
            ORDER BY b.id DESC";
    $heading = "My Bookings";
}

$result = mysqli_query($connection, $sql);
$bookings = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
?>

<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold"><?php echo $heading; ?></h2>
                <a href="bookings.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Booking
                </a>
            </div>

            <?php if (empty($bookings)): ?>
                <!-- No bookings -->
                <div class="card border-0 shadow-sm p-5 text-center">
                    <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No bookings found</h4>
                    <p class="text-muted">You haven't made any bookings yet.</p>
                    <a href="movies.php" class="btn btn-primary mt-3">Browse Movies</a>
                </div>
            <?php else: ?>
                <!-- Bookings Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="m-0 fw-bold">
                            <?php echo count($bookings); ?> Booking(s) Found
                            <?php if ($is_admin): ?>
                                <span class="badge bg-primary ms-2">Admin View</span>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Booking ID</th>
                                    <?php if ($is_admin): ?>
                                        <th>Customer</th>
                                    <?php endif; ?>
                                    <th>Movie</th>
                                    <th>Theater</th>
                                    <th>Class</th>
                                    <th>Seats</th>
                                    <th>Total</th>
                                    <th>Show Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td class="fw-bold">#BK-<?php echo $booking['id']; ?></td>
                                        <?php if ($is_admin): ?>
                                            <td><?php echo htmlspecialchars($booking['user_name'] ?? 'N/A'); ?></td>
                                        <?php endif; ?>
                                        <td><?php echo htmlspecialchars($booking['movie_title'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($booking['theater_name'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?php echo htmlspecialchars($booking['class_name'] ?? '-'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $booking['total_seats']; ?>
                                            <small class="text-muted">
                                                (<?php echo $booking['adults_count']; ?>A, <?php echo $booking['kids_count']; ?>K)
                                            </small>
                                        </td>
                                        <td class="fw-bold text-success">
                                            Rs. <?php echo number_format($booking['total_price'], 0); ?>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($booking['show_date'] ?? $booking['booking_date'])); ?>
                                            <br>
                                            <small class="text-muted"><?php echo $booking['show_time'] ?? ''; ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo ($booking['booking_status'] ?? 'confirmed') === 'confirmed' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($booking['booking_status'] ?? 'Confirmed'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
