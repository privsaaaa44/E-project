<?php
include_once 'connection.php';
include_once 'header.php';

// Current page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($connection, $_GET['search']) : '';

// Limit per page
$limit = 12;
$offset = ($page - 1) * $limit;

// Build base WHERE
$baseWhere = "WHERE m.movie_status != 'archived'";

// Get movies with limit - include categories
if (!empty($search)) {
    // Search in title, director, or category
    $sql = "SELECT m.*, GROUP_CONCAT(c.category_name SEPARATOR ', ') AS categories 
            FROM movies m 
            LEFT JOIN movie_category mc ON m.id = mc.movi_id 
            LEFT JOIN category c ON mc.cat_id = c.id 
            $baseWhere
            GROUP BY m.id 
            HAVING m.title LIKE '%$search%' OR m.director LIKE '%$search%' OR categories LIKE '%$search%'
            ORDER BY m.release_date DESC 
            LIMIT $offset, $limit";
} else {
    $sql = "SELECT m.*, GROUP_CONCAT(c.category_name SEPARATOR ', ') AS categories 
            FROM movies m 
            LEFT JOIN movie_category mc ON m.id = mc.movi_id 
            LEFT JOIN category c ON mc.cat_id = c.id 
            $baseWhere
            GROUP BY m.id 
            ORDER BY m.release_date DESC 
            LIMIT $offset, $limit";
}
$query = mysqli_query($connection, $sql);

// Total movies count - use subquery for category search
if (!empty($search)) {
    $count_sql = "SELECT COUNT(DISTINCT m.id) as total 
                  FROM movies m 
                  LEFT JOIN movie_category mc ON m.id = mc.movi_id 
                  LEFT JOIN category c ON mc.cat_id = c.id 
                  $baseWhere 
                  AND (m.title LIKE '%$search%' OR m.director LIKE '%$search%' OR c.category_name LIKE '%$search%')";
} else {
    $count_sql = "SELECT COUNT(*) as total FROM movies m $baseWhere";
}
$count_query = mysqli_query($connection, $count_sql);
$total_row = mysqli_fetch_assoc($count_query);
$total_movies = $total_row['total'];
$total_pages = ceil($total_movies / $limit);
?>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="fw-bold">Explore Movies</h1>
        <p class="text-muted lead">Find your next favorite cinematic experience.</p>
    </div>
</div>

<div class="container py-5">
    <!-- Search Bar -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-8 col-lg-6">
            <form action="movies.php" method="GET" class="input-group shadow-sm rounded-pill overflow-hidden bg-white border">
                <span class="input-group-text bg-transparent border-0 ps-4 text-muted"><i class="fa fa-search"></i></span>
                <input type="text" name="search" class="form-control border-0 py-3 shadow-none" placeholder="Search by title, director or category..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary px-4 fw-bold">Search</button>
            </form>
            <?php if (!empty($search)): ?>
                <div class="text-center mt-3">
                    <span class="text-muted small">Showing results for "<?php echo htmlspecialchars($search); ?>"</span>
                    <a href="movies.php" class="text-primary small ms-2 text-decoration-none fw-bold">Clear</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Movies Grid -->
    <div class="row g-4">
        <?php if (mysqli_num_rows($query) > 0): ?>
            <?php while($movie = mysqli_fetch_assoc($query)): 
                $posterPath = !empty($movie['poster']) ? 'images/' . $movie['poster'] : 'img/banner1.jpg';
            ?>
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="movie-card h-100">
                    <div class="position-relative">
                        <img src="<?php echo $posterPath; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-white text-primary rounded-pill px-3 py-2 shadow-sm border small fw-bold">
                                <?php echo ucfirst(str_replace('_', ' ', $movie['movie_status'] ?? 'now_showing')); ?>
                            </span>
                        </div>
                    </div>
                    <div class="movie-card-body">
                        <div class="movie-meta mb-1"><?php echo htmlspecialchars($movie['categories'] ?? 'Movie'); ?> • <?php echo htmlspecialchars($movie['duration'] ?? 'N/A'); ?></div>
                        <h3 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <div class="movie-rating">
                            <i class="fa fa-star me-1"></i> <?php echo number_format((float)($movie['rating'] ?? 0), 1); ?>
                        </div>
                        <div class="mt-auto pt-2">
                            <a href="detail.php?id=<?php echo $movie['id']; ?>" class="btn-view-details mb-2">View Details</a>
                            <?php if(($movie['movie_status'] ?? '') == 'now_showing'): ?>
                                <a href="bookings.php?movie_id=<?php echo $movie['id']; ?>" class="btn btn-primary w-100 py-2" onclick="return checkLogin(event)">Book Now</a>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100 py-2 opacity-50 cursor-not-allowed" disabled>Coming Soon</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="mb-4 text-muted"><i class="fa fa-film fa-4x opacity-25"></i></div>
                <h3 class="fw-bold">No movies found</h3>
                <p class="text-muted">Try a different search term or browse our collection.</p>
                <a href="movies.php" class="btn btn-outline-primary rounded-pill px-4">Browse All</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav class="mt-5 pt-4 border-top">
        <ul class="pagination justify-content-center gap-2">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link rounded-circle border-0 shadow-sm" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>"><i class="fa fa-angle-left"></i></a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item">
                    <a class="page-link rounded-circle border-0 shadow-sm <?php echo ($i == $page) ? 'active bg-primary text-white' : 'bg-white text-muted'; ?>" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link rounded-circle border-0 shadow-sm" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>"><i class="fa fa-angle-right"></i></a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<!-- Login Required Modal -->
<div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-5">
                <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                    <i class="fa fa-lock fa-2x"></i>
                </div>
                <h3 class="fw-bold mb-3">Login Required</h3>
                <p class="text-muted mb-4">Please log in to your account to book tickets and manage your reservations.</p>
                <div class="d-grid gap-2">
                    <a href="login.php" class="btn btn-primary py-2">Login Now</a>
                    <button type="button" class="btn btn-light py-2" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var isLoggedIn = <?php echo isset($_SESSION['user_name']) ? 'true' : 'false'; ?>;

function checkLogin(event) {
    if (!isLoggedIn) {
        event.preventDefault();
        var loginModal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
        loginModal.show();
        return false;
    }
    return true;
}
</script>

<?php include_once 'footer.php'; ?>
