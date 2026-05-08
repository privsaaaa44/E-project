<?php
include_once "header.php";
include_once "connection.php";
?>

<!-- Hero Section -->
<section class="hero-section py-5 overflow-hidden position-relative" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center min-vh-50 py-5">
            <div class="col-lg-6">
                <span class="badge bg-primary-subtle text-primary mb-3 px-3 py-2 rounded-pill fw-bold">NEW RELEASES NOW SHOWING</span>
                <h1 class="display-3 fw-bold mb-3 text-main">Experience Cinema Like Never Before</h1>
                <p class="lead text-muted mb-4 fs-4">Book tickets for the latest blockbusters in just a few clicks. Minimal, fast, and secure booking for your favorite theaters.</p>
                <div class="d-flex gap-3">
                    <a href="#movies" class="btn btn-primary px-5 py-3 fw-bold rounded-3 shadow">Browse Movies</a>
                    <a href="about.php" class="btn btn-outline-primary px-5 py-3 fw-bold rounded-3">Learn More</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="position-relative">
                    <div class="hero-blob position-absolute top-50 start-50 translate-middle" style="width: 500px; height: 500px; background: rgba(59, 130, 246, 0.1); filter: blur(80px); border-radius: 50%; z-index: -1;"></div>
                    <img src="images/banner1.jpg" class="img-fluid rounded-4 shadow-2xl transform-hover" alt="Cinema Experience" style="transition: transform 0.5s ease;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Movies Section -->
<?php
$featuredMovies = [];
if (isset($connection)) {
    // Query to fetch Top Selling movies based on booking counts
    $featQuery = "SELECT m.id, m.title, m.poster, m.rating, COUNT(b.id) as booking_count,
                         GROUP_CONCAT(c.category_name SEPARATOR ', ') as categories 
                  FROM movies m 
                  LEFT JOIN shows s ON s.movie_id = m.id 
                  LEFT JOIN bookings b ON b.show_id = s.id 
                  LEFT JOIN movie_category mc ON mc.movi_id = m.id
                  LEFT JOIN category c ON c.id = mc.cat_id
                  WHERE m.movie_status = 'now_showing' 
                  GROUP BY m.id 
                  ORDER BY booking_count DESC, m.rating DESC 
                  LIMIT 4";
    $featResult = mysqli_query($connection, $featQuery);
    if ($featResult) { $featuredMovies = mysqli_fetch_all($featResult, MYSQLI_ASSOC); }
}

if (!empty($featuredMovies)):
?>
<section class="py-5 bg-white border-bottom">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0"><i class="fa fa-star text-warning me-2"></i> Featured Blockbusters</h4>
            <a href="movies.php" class="text-primary text-decoration-none small fw-bold">View All <i class="fa fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($featuredMovies as $fMovie): 
                $fPoster = !empty($fMovie['poster']) ? 'images/' . $fMovie['poster'] : 'img/banner1.jpg';
            ?>
            <div class="col-md-3">
                <a href="detail.php?id=<?php echo $fMovie['id']; ?>" class="text-decoration-none">
                    <div class="featured-card position-relative rounded-4 overflow-hidden shadow-sm">
                        <img src="<?php echo $fPoster; ?>" class="w-100" style="height: 400px; object-fit: cover;" alt="<?php echo htmlspecialchars($fMovie['title']); ?>">
                        <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-gradient-dark text-white">
                            <div class="small opacity-75 mb-1"><?php echo htmlspecialchars($fMovie['categories'] ?? 'Movie'); ?></div>
                            <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($fMovie['title']); ?></h6>
                        </div>
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="fa fa-star me-1"></i><?php echo number_format((float)$fMovie['rating'], 1); ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Movie List Section -->
<section id="movies" class="section-padding">
    <div class="container">
        <div class="text-center mb-4">
            <span class="text-primary fw-bold text-uppercase small tracking-widest">Now Playing</span>
            <h2 class="fw-bold display-6">Explore Latest Movies</h2>
        </div>

        <div class="d-flex justify-content-center mb-5">
            <div class="filter-container p-1 rounded-pill bg-white border shadow-sm d-inline-flex">
                <button type="button" class="btn px-4 py-2 rounded-pill movie-filter-btn active" data-filter="all">All Movies</button>
                <button type="button" class="btn px-4 py-2 rounded-pill movie-filter-btn" data-filter="now_showing">Now Showing</button>
                <button type="button" class="btn px-4 py-2 rounded-pill movie-filter-btn" data-filter="upcoming">Upcoming</button>
            </div>
        </div>

        <?php
        $homeMovies = [];
        if (isset($connection)) {
            $moviesQuery = "SELECT m.id, m.title, m.duration, m.release_date, m.poster, m.movie_status,
                                    GROUP_CONCAT(c.category_name SEPARATOR ', ') as categories 
                             FROM movies m 
                             LEFT JOIN movie_category mc ON mc.movi_id = m.id
                             LEFT JOIN category c ON c.id = mc.cat_id
                             GROUP BY m.id
                             ORDER BY m.release_date DESC LIMIT 12";
            $moviesResult = mysqli_query($connection, $moviesQuery);
            if ($moviesResult) { $homeMovies = mysqli_fetch_all($moviesResult, MYSQLI_ASSOC); }
        }
        ?>

        <div class="row g-4" id="homeMovieGrid">
            <?php if (!empty($homeMovies)): ?>
                <?php foreach ($homeMovies as $movie): ?>
                    <?php
                    $movieStatus = $movie['movie_status'] ?? 'now_showing';
                    $posterPath = !empty($movie['poster']) ? 'images/' . $movie['poster'] : 'img/banner1.jpg';
                    ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 home-movie-item" data-status="<?php echo $movieStatus; ?>">
                        <div class="movie-card">
                            <div class="position-relative">
                                <img src="<?php echo $posterPath; ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-white text-primary rounded-pill px-3 py-2 shadow-sm border">
                                        <?php echo ucfirst(str_replace('_', ' ', $movieStatus)); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="movie-card-body">
                                <div class="movie-meta"><?php echo htmlspecialchars($movie['categories'] ?? 'Movie'); ?> • <?php echo htmlspecialchars($movie['duration']); ?></div>
                                <h3 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                                <div class="movie-rating">
                                    <i class="fa fa-star me-1"></i> <?php echo number_format((float)($movie['rating'] ?? 0), 1); ?>
                                </div>
                                <div class="mt-auto pt-2">
                                    <a href="detail.php?id=<?php echo $movie['id']; ?>" class="btn-view-details mb-2">View Details</a>
                                    <?php if($movieStatus == 'now_showing'): ?>
                                        <a href="bookings.php?movie_id=<?php echo $movie['id']; ?>" class="btn btn-primary w-100 py-2" onclick="return checkLogin(event)">Book Now</a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary w-100 py-2 opacity-50 cursor-not-allowed" disabled>Coming Soon</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No movies found at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
        <div id="movieFilterEmpty" class="text-center py-5 d-none">
            <p class="text-muted">No movies match this filter.</p>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-4">
                    <i class="fa fa-ticket fa-3x text-primary mb-4"></i>
                    <h4>Easy Booking</h4>
                    <p class="text-muted">Select your movie, class, and seat in just 3 simple steps.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <i class="fa fa-star fa-3x text-primary mb-4"></i>
                    <h4>Top Ratings</h4>
                    <p class="text-muted">See what others are saying before you book your ticket.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <i class="fa fa-mobile fa-3x text-primary mb-4"></i>
                    <h4>Mobile Ready</h4>
                    <p class="text-muted">Book on the go with our fully responsive mobile interface.</p>
                </div>
            </div>
        </div>
    </div>
</section>

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

document.addEventListener('DOMContentLoaded', function () {
    var filterButtons = document.querySelectorAll('.movie-filter-btn');
    var movieItems = document.querySelectorAll('.home-movie-item');
    var emptyState = document.getElementById('movieFilterEmpty');

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var filter = button.dataset.filter;
            var visibleCount = 0;

            filterButtons.forEach(btn => btn.classList.remove('active', 'btn-primary'));
            button.classList.add('active');

            movieItems.forEach(function (item) {
                var show = filter === 'all' || item.dataset.status === filter;
                item.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });

            emptyState.classList.toggle('d-none', visibleCount > 0);
        });
    });
});
</script>

<?php include_once "footer.php"; ?>