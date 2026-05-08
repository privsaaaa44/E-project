<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
include_once 'admin_header.php';

$stats = get_dashboard_stats($connection);
$recentBookings = get_recent_bookings($connection, 10);
$dailyStats = get_bookings_by_day($connection, 7);
$theaters = get_all_theaters($connection);
$classes = get_all_classes($connection);
$categories = get_all_categories($connection);

// Calculate total revenue
$totalRevenue = 0;
foreach ($recentBookings as $booking) {
    $totalRevenue += (float) $booking['total_price'];
}

// Get today's stats from database (dynamic)
$today = date('Y-m-d');
$todayStart = $today . ' 00:00:00';
$todayEnd = $today . ' 23:59:59';

// Try multiple methods to get today's bookings
$todayStats = mysqli_query($connection, "SELECT COUNT(*) as bookings, COALESCE(SUM(total_price), 0) as revenue FROM bookings WHERE booking_date BETWEEN '$todayStart' AND '$todayEnd'");
if (!$todayStats) {
    // Fallback to DATE() function if booking_date is date type
    $todayStats = mysqli_query($connection, "SELECT COUNT(*) as bookings, COALESCE(SUM(total_price), 0) as revenue FROM bookings WHERE DATE(booking_date) = '$today'");
}
$todayData = $todayStats ? mysqli_fetch_assoc($todayStats) : ['bookings' => 0, 'revenue' => 0];
$todayBookings = (int) $todayData['bookings'];
$todayRevenue = (float) $todayData['revenue'];

// Prepare data for chart
$chartLabels = [];
$chartData = [];
$days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $dayName = $days[date('w', strtotime($date))];
    $chartLabels[] = $dayName;
    $chartData[] = 0;
}

foreach ($dailyStats as $stat) {
    $date = $stat['date'];
    $dayName = $days[date('w', strtotime($date))];
    $index = array_search($dayName, $chartLabels);
    if ($index !== false) {
        $chartData[$index] = (int) $stat['bookings'];
    }
}
?>

<!-- Quick Action Buttons -->
<div class="d-flex gap-2 mb-4">
    <a href="admin_movies.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Movie</a>
    <a href="admin_shows.php" class="btn btn-success"><i class="fas fa-plus me-2"></i>Create Show</a>
    <a href="admin_theaters.php" class="btn btn-info"><i class="fas fa-plus me-2"></i>Add Theater</a>
    <a href="admin_reports.php" class="btn btn-warning"><i class="fas fa-chart-bar me-2"></i>View Reports</a>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm bg-primary text-white">
            <div class="mb-2 fs-3"><i class="fas fa-ticket-alt"></i></div>
            <h6 class="small fw-bold opacity-75">TODAY'S BOOKINGS</h6>
            <h2 class="m-0 fw-bold"><?php echo $todayBookings; ?></h2>
            <small class="opacity-75">Rs. <?php echo number_format($todayRevenue); ?> revenue</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm">
            <div class="text-success mb-2 fs-3"><i class="fas fa-money-bill-wave"></i></div>
            <h6 class="text-muted small fw-bold">TOTAL REVENUE</h6>
            <h2 class="m-0 fw-bold text-success">Rs. <?php echo number_format($totalRevenue); ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm">
            <div class="text-warning mb-2 fs-3"><i class="fas fa-film"></i></div>
            <h6 class="text-muted small fw-bold">MOVIES / SHOWS</h6>
            <h2 class="m-0 fw-bold"><?php echo (int) $stats['total_movies']; ?> <small class="text-muted fs-6">/ <?php echo count(get_all_shows($connection)); ?></small></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm">
            <div class="text-info mb-2 fs-3"><i class="fas fa-users"></i></div>
            <h6 class="text-muted small fw-bold">USERS / THEATERS</h6>
            <h2 class="m-0 fw-bold"><?php echo (int) $stats['total_users']; ?> <small class="text-muted fs-6">/ <?php echo count($theaters); ?></small></h2>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold">Recent Bookings</h6>
                <a href="admin_reports.php?show_all=1" class="btn btn-sm btn-light border small px-3">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Movie</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBookings as $booking): ?>
                        <tr>
                            <td class="small fw-bold">#BK-<?php echo $booking['id']; ?></td>
                            <td><?php echo htmlspecialchars($booking['user_name'] ?: 'N/A'); ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($booking['movie_title'] ?: 'N/A'); ?></td>
                            <td class="text-muted small"><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                            <td><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Confirmed</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-4">Bookings Trend</h6>
            <canvas id="bookingsChart" height="300"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('bookingsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartLabels); ?>,
            datasets: [{
                label: 'Bookings',
                data: <?php echo json_encode($chartData); ?>,
                backgroundColor: '#3b82f6',
                borderRadius: 6,
                barThickness: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: false }, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>

<style>
.bg-success-subtle { background-color: #dcfce7 !important; }
</style>

<?php include_once 'admin_footer.php'; ?>
