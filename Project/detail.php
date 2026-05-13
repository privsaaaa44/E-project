<?php
include_once 'header.php';
include_once 'connection.php';

$movie_id = (int) ($_GET['id'] ?? 0);
$query = mysqli_query($connection, "SELECT m.*, GROUP_CONCAT(c.category_name SEPARATOR ', ') AS categories 
                                    FROM movies m 
                                    LEFT JOIN movie_category mc ON m.id = mc.movi_id 
                                    LEFT JOIN category c ON mc.cat_id = c.id 
                                    WHERE m.id = $movie_id 
                                    GROUP BY m.id");
$detail = mysqli_fetch_assoc($query);

if (!$detail) {
    echo '<div class="container py-5 text-center"><h2>Movie not found</h2><a href="index.php" class="btn btn-primary mt-3">Back to Home</a></div>';
    include_once 'footer.php';
    exit;
}

// Calculate average rating
$avg_query = mysqli_query($connection, "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM review WHERE movies_id = $movie_id");
$avg_data = mysqli_fetch_assoc($avg_query);
$avg_rating = $avg_data['avg_rating'] ? round($avg_data['avg_rating'], 1) : 0;
$total_reviews = (int)$avg_data['total_reviews'];

$posterPath = !empty($detail['poster']) ? 'images/' . $detail['poster'] : 'img/banner1.jpg';
?>

<div class="container py-5 my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-5">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="movies.php" class="text-decoration-none text-muted">Movies</a></li>
            <li class="breadcrumb-item active fw-bold text-primary" aria-current="page"><?php echo htmlspecialchars($detail['title']); ?></li>
        </ol>
    </nav>

    <div class="row g-5 mb-5">
        <!-- LEFT COLUMN: Movie Poster -->
        <div class="col-lg-4">
            <div class="movie-poster-container shadow-lg rounded-4 overflow-hidden mb-4">
                <img src="<?php echo $posterPath; ?>" class="w-100 img-fluid" alt="<?php echo htmlspecialchars($detail['title']); ?>">
            </div>
            
            <?php if (!empty($detail['trailer_link'])): ?>
            <div class="d-grid mt-4">
                <button class="btn btn-outline-primary py-3 fw-bold rounded-3 shadow-sm" onclick="playTrailer('<?php echo $detail['trailer_link']; ?>')">
                    <i class="fa fa-play-circle me-2"></i> Watch Official Trailer
                </button>
            </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT COLUMN: Movie Content -->
        <div class="col-lg-8">
            <div class="movie-info-section mb-5 p-2">
                <div class="mb-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill small fw-bold">
                        <?php echo htmlspecialchars($detail['categories'] ?? 'Movie'); ?>
                    </span>
                </div>
                <h1 class="display-4 fw-bold text-main mb-4"><?php echo htmlspecialchars($detail['title']); ?></h1>
                
                <div class="d-flex align-items-center gap-4 flex-wrap mb-4 text-muted">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-star text-warning fs-5"></i>
                        <span class="fw-bold text-main fs-5"><?php echo $avg_rating; ?>/5</span>
                        <span class="small">(<?php echo $total_reviews; ?> Reviews)</span>
                    </div>
                    <div class="vr opacity-25 d-none d-md-block"></div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-clock-o"></i>
                        <span><?php echo htmlspecialchars($detail['duration'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-globe"></i>
                        <span><?php echo htmlspecialchars($detail['language'] ?? 'English'); ?></span>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="fw-bold text-main mb-3">About Movie</h5>
                    <p class="text-muted fs-5" style="line-height: 1.8; text-align: justify;">
                        <?php echo nl2br(htmlspecialchars($detail['movie_desc'] ?? 'No description available.')); ?>
                    </p>
                </div>

                <div class="row g-4 py-4 border-top border-bottom mb-4">
                    <div class="col-6 col-md-4">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1">Director</span>
                        <span class="fw-bold text-main"><?php echo htmlspecialchars($detail['director'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1">Release Date</span>
                        <span class="fw-bold text-main"><?php echo date('M d, Y', strtotime($detail['release_date'])); ?></span>
                    </div>
                </div>

                <div class="showtimes-section mb-4">
                    <h5 class="fw-bold text-main mb-3">Available Showtimes</h5>
                    <div class="d-flex gap-2 flex-wrap mb-4">
         <?php
$shows_query = mysqli_query($connection, 
    "SELECT shows.show_time, shows.show_date, shows.id,
            theaters.theater_name, theaters.location 
     FROM shows 
     INNER JOIN theaters ON shows.theater_id = theaters.id 
     WHERE shows.movie_id = $movie_id AND shows.show_date >= CURDATE() 
     LIMIT 4"
);
if(mysqli_num_rows($shows_query) > 0):
    while($show = mysqli_fetch_assoc($shows_query)):
?>

<div style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:14px 20px; display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; width: 100%;">
    <!-- Left Side -->
    <div style="display:flex; align-items:center; gap:18px;">
        <!-- Blue Circle Icon -->
        <div style="width:42px; height:42px; border-radius:50%; background:#eff6ff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fa fa-map-marker" style="color:#3b82f6; font-size:20px;"></i>
        </div>
        <!-- Theater Info -->
        <div>
            <div style="font-weight:700; color:#111; font-size:15px; margin-bottom:6px;">
                <?php echo htmlspecialchars($show['theater_name']); ?>, <?php echo htmlspecialchars($show['location']); ?>
            </div>
            <div style="display:flex; gap:8px;">
                <span style="border:1px solid #bfdbfe; border-radius:6px; padding:3px 10px; font-size:13px; color: #1d4ed8; background: #eff6ff;">
                    <i class="fa fa-clock-o" style="color:#3b82f6; margin-right:4px;"></i>
                    <?php echo date('h:i A', strtotime($show['show_time'])); ?>
                </span>
                <span style="border:1px solid #bfdbfe; border-radius:6px; padding:3px 10px; font-size:13px; color: #1d4ed8; background: #eff6ff;">
                    <i class="fa fa-calendar" style="color:#3b82f6; margin-right:4px;"></i>
                    <?php echo date('M d, Y', strtotime($show['show_date'])); ?>
                </span>
            </div>
        </div>
    </div>
  <a href="bookings.php?movie_id=<?php echo $movie_id; ?>&show_id=<?php echo $show['id']; ?>" 
       onclick="return checkLogin(event)"
       class="btn btn-outline-primary">
        Book Tickets
    </a>
</div>

<?php endwhile; else: ?>
    <p class="text-muted small">No upcoming shows scheduled yet.</p>
<?php endif; ?>
                    </div>
                </div>

                <div class="d-grid mt-5">
                    <?php if (($detail['movie_status'] ?? 'now_showing') == 'now_showing'): ?>
                        <a href="bookings.php?movie_id=<?php echo $movie_id; ?>" class="btn btn-primary py-3 px-5 fw-bold fs-5 shadow rounded-3" onclick="return checkLogin(event)">
                            Book My Tickets Now
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary py-3 px-5 fw-bold fs-5 shadow rounded-3 opacity-50 cursor-not-allowed" disabled>
                            Coming Soon
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="reviews-section mt-5 pt-5 border-top">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="fw-bold m-0">Ratings & Reviews</h2>
            <button class="btn btn-outline-primary rounded-pill px-4" onclick="showReviewModal()">
                <i class="fa fa-pencil me-2"></i> Write a Review
            </button>
        </div>

        <div class="row g-4">
            <?php
            $review_query = mysqli_query($connection, "SELECT r.*, u.name AS user_name FROM review r LEFT JOIN users u ON u.id = r.users_id WHERE r.movies_id = $movie_id ORDER BY r.created_at DESC LIMIT 6");
            if (mysqli_num_rows($review_query) > 0):
                while ($review = mysqli_fetch_assoc($review_query)):
            ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 45px; height: 45px; font-size: 1.2rem;">
                                    <?php echo strtoupper(substr($review['user_name'] ?? 'A', 0, 1)); ?>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($review['user_name'] ?? 'Anonymous'); ?></h6>
                                    <small class="text-muted"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                                </div>
                            </div>
                            <div class="badge bg-warning text-dark px-2 py-1 rounded-pill small">
                                <i class="fa fa-star me-1"></i> <?php echo (int)$review['rating']; ?>.0
                            </div>
                        </div>
                        <p class="text-muted mb-0 lh-lg" style="font-size: 0.95rem;">
                            "<?php echo nl2br(htmlspecialchars($review['review'])); ?>"
                        </p>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <div class="col-12 text-center py-5 bg-light rounded-4">
                    <div class="mb-3 opacity-25"><i class="fa fa-comments fa-3x"></i></div>
                    <p class="text-muted">No reviews yet. Share your experience with others!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="code.php">
                <div class="modal-body p-4 text-center">
                    <h4 class="fw-bold mb-2">Rate your experience</h4>
                    <p class="text-muted small mb-4">How much did you like <?php echo htmlspecialchars($detail['title']); ?>?</p>
                    
                    <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                    <input type="hidden" name="submit_review" value="1">
                    
                    <div class="d-flex justify-content-center gap-3 fs-1 text-warning mb-4" id="ratingStars">
                        <i class="fa fa-star-o cursor-pointer" data-rating="1" onclick="setRating(1)"></i>
                        <i class="fa fa-star-o cursor-pointer" data-rating="2" onclick="setRating(2)"></i>
                        <i class="fa fa-star-o cursor-pointer" data-rating="3" onclick="setRating(3)"></i>
                        <i class="fa fa-star-o cursor-pointer" data-rating="4" onclick="setRating(4)"></i>
                        <i class="fa fa-star-o cursor-pointer" data-rating="5" onclick="setRating(5)"></i>
                    </div>
                    <input type="hidden" id="ratingValue" name="rating" value="0" required>

                    <div class="mb-3 text-start">
                        <label class="form-label text-muted small fw-bold">YOUR REVIEW</label>
                        <textarea name="review_text" class="form-control shadow-none border" rows="4" placeholder="Describe your experience..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer p-3 border-0 d-grid">
                    <button type="submit" class="btn btn-primary py-3 fw-bold rounded-3">Post Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Login Required Modal -->
<div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body text-center p-5">
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px;">
                    <i class="fa fa-lock fa-2x"></i>
                </div>
                <h4 class="fw-bold mb-2">Authentication Required</h4>
                <p class="text-muted mb-4">You need to sign in to your account to perform this action.</p>
                <div class="d-grid gap-2">
                    <a href="login.php" class="btn btn-primary py-2 fw-bold">Sign In Now</a>
                    <button type="button" class="btn btn-light py-2 fw-bold" data-bs-dismiss="modal">Close</button>
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

function showReviewModal() {
    if (!isLoggedIn) {
        var loginModal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
        loginModal.show();
        return;
    }
    var modal = new bootstrap.Modal(document.getElementById('reviewModal'));
    modal.show();
}

function setRating(rating) {
    document.getElementById('ratingValue').value = rating;
    var stars = document.querySelectorAll('#ratingStars i');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.replace('fa-star-o', 'fa-star');
        } else {
            star.classList.replace('fa-star', 'fa-star-o');
        }
    });
}
</script>

<style>
.cursor-pointer { cursor: pointer; }
.bg-primary-subtle { background-color: #dbeafe !important; }
.movie-poster-container {
    transition: transform 0.3s ease;
}
.movie-poster-container:hover {
    transform: scale(1.02);
}
.vr {
    width: 1px;
    background-color: currentColor;
    height: 1.5rem;
}
</style>

<?php include_once 'footer.php'; ?>