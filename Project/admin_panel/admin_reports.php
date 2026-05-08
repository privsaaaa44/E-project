<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
include_once 'admin_header.php';

// Check if show_all is requested
$showAll = isset($_GET['show_all']) && $_GET['show_all'] == '1';

// Date filters - if show_all, no date filter applied
if ($showAll) {
    $startDate = '';
    $endDate = '';
} else {
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
}

// Get all bookings with date filter (use unlimited when show_all)
if ($showAll) {
    $allBookings = get_all_bookings_unlimited($connection);
} else {
    $allBookings = get_all_bookings($connection);
}
$filteredBookings = [];
$totalRevenue = 0;
$todayRevenue = 0;
$today = date('Y-m-d');

foreach ($allBookings as $booking) {
    $bookingDate = $booking['booking_date'] ?? '';
    if ($showAll || ($bookingDate >= $startDate && $bookingDate <= $endDate)) {
        $filteredBookings[] = $booking;
        $totalRevenue += (float) $booking['total_price'];
        if ($bookingDate === $today) {
            $todayRevenue += (float) $booking['total_price'];
        }
    }
}

// Calculate stats
$totalTickets = 0;
$totalKids = 0;
$totalAdults = 0;
foreach ($filteredBookings as $booking) {
    $totalTickets += (int) ($booking['total_seats'] ?? 0);
    $totalKids += (int) ($booking['kids_count'] ?? 0);
    $totalAdults += (int) ($booking['adults_count'] ?? 0);
}
$avgTicketPrice = count($filteredBookings) > 0 ? ($totalRevenue / count($filteredBookings)) : 0;

// Get daily stats for chart
$dailyStats = get_bookings_by_day($connection, 7);
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

// Prepare revenue data for chart
$revenueData = [];
for ($i = 6; $i >= 0; $i--) {
    $revenueData[] = 0;
}
foreach ($dailyStats as $stat) {
    $date = $stat['date'];
    $dayName = $days[date('w', strtotime($date))];
    $index = array_search($dayName, $chartLabels);
    if ($index !== false) {
        $revenueData[$index] = (float) $stat['revenue'];
    }
}
?>

<!-- Date Filter -->
<div class="card border-0 shadow-sm mb-4 p-4">
    <?php if ($showAll): ?>
    <div class="alert alert-info mb-0">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Showing All Bookings</strong> - No date filter applied.
        <a href="admin_reports.php" class="btn btn-sm btn-outline-primary ms-3">Apply Date Filter</a>
    </div>
    <?php else: ?>
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-bold">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="<?php echo $startDate; ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">End Date</label>
            <input type="date" name="end_date" class="form-control" value="<?php echo $endDate; ?>">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i>Filter</button>
            <a href="admin_reports.php" class="btn btn-outline-secondary">Reset</a>
        </div>
        <div class="col-md-3 text-end">
            <a href="admin_reports.php?show_all=1" class="btn btn-info"><i class="fas fa-list me-2"></i>View All</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm bg-primary text-white">
            <h6 class="small fw-bold opacity-75">TOTAL REVENUE</h6>
            <h2 class="m-0 fw-bold">Rs. <?php echo number_format($totalRevenue); ?></h2>
            <small class="opacity-75"><?php echo count($filteredBookings); ?> bookings</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm bg-success text-white">
            <h6 class="small fw-bold opacity-75">TODAY'S REVENUE</h6>
            <h2 class="m-0 fw-bold">Rs. <?php echo number_format($todayRevenue); ?></h2>
            <small class="opacity-75"><?php echo date('M d, Y'); ?></small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm bg-white">
            <h6 class="text-muted small fw-bold">TICKETS SOLD</h6>
            <h2 class="m-0 fw-bold text-info"><?php echo $totalTickets; ?></h2>
            <small class="text-muted"><?php echo $totalKids; ?> kids, <?php echo $totalAdults; ?> adults</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm bg-white">
            <h6 class="text-muted small fw-bold">AVG. PER BOOKING</h6>
            <h2 class="m-0 fw-bold text-warning">Rs. <?php echo number_format($avgTicketPrice); ?></h2>
            <small class="text-muted">per transaction</small>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-4">Bookings Trend</h6>
            <canvas id="reportsChart" height="250"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-4">Revenue by Day</h6>
            <canvas id="revenueChart" height="250"></canvas>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold">Detailed Booking Report <span class="badge bg-primary ms-2"><?php echo count($filteredBookings); ?> bookings</span></h6>
        <div class="input-group input-group-sm border rounded-pill px-3" style="width: 250px;">
            <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-search"></i></span>
            <input type="text" id="bookingSearch" class="form-control bg-transparent border-0 shadow-none" placeholder="Search bookings...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="bookingTable">
            <thead>
                <tr>
                    <th class="ps-4">BOOKING ID</th>
                    <th>CUSTOMER</th>
                    <th>MOVIE</th>
                    <th>THEATER</th>
                    <th>CLASS</th>
                    <th>SEATS</th>
                    <th>TOTAL PRICE</th>
                    <th class="pe-4">DATE</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($filteredBookings)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No bookings found for selected date range</td>
                </tr>
                <?php else: ?>
                <?php foreach ($filteredBookings as $booking): ?>
                <tr>
                    <td class="ps-4 fw-bold small">#BK-<?php echo $booking['id']; ?></td>
                    <td><?php echo htmlspecialchars($booking['user_name'] ?: 'N/A'); ?></td>
                    <td class="fw-semibold"><?php echo htmlspecialchars($booking['movie_title'] ?: 'N/A'); ?></td>
                    <td><span class="text-muted small"><?php echo htmlspecialchars($booking['theater_name'] ?: 'N/A'); ?></span></td>
                    <td><span class="badge bg-light text-primary border px-2 py-1"><?php echo htmlspecialchars($booking['class_name'] ?: '-'); ?></span></td>
                    <td><?php echo (int) ($booking['total_seats'] ?? 0); ?> <small class="text-muted">(<?php echo (int) ($booking['adults_count'] ?? 0); ?>A, <?php echo (int) ($booking['kids_count'] ?? 0); ?>K)</small></td>
                    <td class="fw-bold text-success">Rs. <?php echo number_format((float)$booking['total_price'], 0); ?></td>
                    <td class="pe-4 text-muted small"><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('reportsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chartLabels); ?>,
            datasets: [{
                label: 'Bookings',
                data: <?php echo json_encode($chartData); ?>,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartLabels); ?>,
            datasets: [{
                label: 'Revenue (Rs.)',
                data: <?php echo json_encode($revenueData); ?>,
                backgroundColor: '#10b981',
                borderRadius: 6,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });

    const bookingSearch = document.getElementById('bookingSearch');
    if (bookingSearch) {
        bookingSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('#bookingTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
            });
        });
    }
});
</script>

<?php include_once 'admin_footer.php'; ?>
