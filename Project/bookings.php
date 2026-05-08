<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "header.php";
include_once "connection.php";
require_once "code.php";

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Ensure columns exist
mysqli_query($connection, "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS has_kids TINYINT(1) NOT NULL DEFAULT 0");
mysqli_query($connection, "ALTER TABLE bookings MODIFY COLUMN total_price DECIMAL(10,2) NOT NULL");

// Login check
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    echo "<script>window.location.href = 'login.php';</script>";
    exit();
}

$error   = "";
$success = false;
$details = [];

// ============================================
// BOOKING FORM SUBMISSION
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $show_id    = isset($_POST['show_id'])    ? (int)$_POST['show_id']    : 0;
    $seat_class = isset($_POST['seat_class']) ? strtolower(trim(mysqli_real_escape_string($connection, $_POST['seat_class']))) : '';
    $seats      = isset($_POST['seats'])      ? (int)$_POST['seats']      : 1;
    $kids_count = isset($_POST['kids_count']) ? (int)$_POST['kids_count'] : 0;
    $adults_count = $seats - $kids_count;
    $has_kids   = $kids_count > 0 ? 1 : 0;

    if ($show_id <= 0) {
        $error = "Please select a valid movie, date, and showtime.";
    } elseif (empty($seat_class)) {
        $error = "Please select a seat class.";
    } elseif ($seats <= 0) {
        $error = "Please select at least 1 seat.";
    } else {
        // Get show info
        $q = "SELECT s.*, m.title
              FROM shows s
              JOIN movies m ON m.id = s.movie_id
              WHERE s.id = $show_id";
        $res  = mysqli_query($connection, $q);
        $show = $res ? mysqli_fetch_assoc($res) : null;

        if (!$show) {
            $error = "Selected show not found in database.";
        } else {
            // Get class_id for selected seat class from database
            $class_name_escaped = mysqli_real_escape_string($connection, $seat_class);
            $class_q = mysqli_query($connection, "SELECT id FROM classes WHERE LOWER(class_name) = '$class_name_escaped' LIMIT 1");
            if ($class_q && mysqli_num_rows($class_q) > 0) {
                $class_data = mysqli_fetch_assoc($class_q);
                $class_id = (int) $class_data['id'];
            } else {
                $error = "Invalid seat class selected.";
                $class_id = 0;
            }

            // Get price from show_class_pricing table
            $price = 0;
            if ($class_id > 0) {
                $price_q = mysqli_query($connection, "SELECT price FROM show_class_pricing WHERE show_id = $show_id AND class_id = $class_id LIMIT 1");
                if ($price_q && mysqli_num_rows($price_q) > 0) {
                    $price_data = mysqli_fetch_assoc($price_q);
                    $price = (float) $price_data['price'];
                }
            }

            if ($price <= 0) {
                $error = "Pricing not available for selected class.";
            }

            // Check seat availability using booking_seats table
            if (empty($error) && $class_id > 0) {
                $theater_id = $show['theater_id'];

                // Get total capacity for this theater/class
                $cap_q = mysqli_query($connection, "SELECT COUNT(*) as total FROM seats WHERE theater_id = $theater_id AND class_id = $class_id");
                $total_capacity = 0;
                if ($cap_q && mysqli_num_rows($cap_q) > 0) {
                    $cap_data = mysqli_fetch_assoc($cap_q);
                    $total_capacity = (int) $cap_data['total'];
                }

                // Get already booked seats for this show/class from booking_seats
                $booked_q = mysqli_query($connection, "SELECT COUNT(*) as booked FROM booking_seats bs
                                                       JOIN bookings b ON b.id = bs.booking_id
                                                       WHERE b.show_id = $show_id AND b.class_id = $class_id");
                $booked_seats = 0;
                if ($booked_q && mysqli_num_rows($booked_q) > 0) {
                    $booked_data = mysqli_fetch_assoc($booked_q);
                    $booked_seats = (int) ($booked_data['booked'] ?? 0);
                }

                $available_seats = $total_capacity - $booked_seats;

                if ($available_seats <= 0) {
                    $error = "Sorry, no seats available for this class. Theater capacity: $total_capacity, Already booked: $booked_seats";
                } elseif ($seats > $available_seats) {
                    $error = "Only $available_seats seats available for this class. You requested $seats seats.";
                }
            }

            if (empty($error)) {
                // Calculate price: adults pay full, kids pay 50%
                $adult_price = $price * $adults_count;
                $kid_price = ($price * 0.5) * $kids_count;
                $total = $adult_price + $kid_price;

                // Insert booking with kids/adults count
                $ins_q = "INSERT INTO bookings (user_id, show_id, class_id, total_seats, total_price, booking_date, has_kids, kids_count, adults_count)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt  = mysqli_prepare($connection, $ins_q);

                if ($stmt) {
                    $b_date = date('Y-m-d'); // Today's date when booking is made
                    mysqli_stmt_bind_param($stmt, "iiiddsiii",
                        $_SESSION['user_id'],
                        $show_id,
                        $class_id,
                        $seats,
                        $total,
                        $b_date,
                        $has_kids,
                        $kids_count,
                        $adults_count
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        $booking_id = mysqli_insert_id($connection);

                        // Get available seat IDs for this theater/class that are not already booked
                        $theater_id = $show['theater_id'];
                        $seat_query = "SELECT s.id FROM seats s
                                       WHERE s.theater_id = ? AND s.class_id = ?
                                       AND s.id NOT IN (
                                           SELECT bs.seat_id FROM booking_seats bs
                                           JOIN bookings b ON b.id = bs.booking_id
                                           WHERE b.show_id = ? AND b.class_id = ?
                                       )
                                       LIMIT ?";
                        $seat_stmt = mysqli_prepare($connection, $seat_query);
                        mysqli_stmt_bind_param($seat_stmt, "iiiii", $theater_id, $class_id, $show_id, $class_id, $seats);
                        mysqli_stmt_execute($seat_stmt);
                        $seat_result = mysqli_stmt_get_result($seat_stmt);

                        // Insert seat assignments into booking_seats
                        $assigned_seats = [];
                        $seat_insert = mysqli_prepare($connection, "INSERT INTO booking_seats (booking_id, seat_id) VALUES (?, ?)");
                        while ($seat_row = mysqli_fetch_assoc($seat_result)) {
                            $seat_id = $seat_row['id'];
                            mysqli_stmt_bind_param($seat_insert, "ii", $booking_id, $seat_id);
                            mysqli_stmt_execute($seat_insert);
                            $assigned_seats[] = $seat_id;
                        }
                        mysqli_stmt_close($seat_insert);
                        mysqli_stmt_close($seat_stmt);

                        $success = true;
                        $details = [
                            'movie' => $show['title'],
                            'date'  => $show['show_date'],
                            'time'  => $show['show_time'],
                            'class' => ucfirst($seat_class),
                            'seats' => $seats,
                            'total' => $total,
                            'assigned_seats' => $assigned_seats
                        ];
                    } else {
                        $error = "Booking Failed: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error = "Booking Failed (Prepare): " . mysqli_error($connection);
                }
            }
        }
    }
}

// Get movie_id from URL if provided
$preselect_movie_id = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : 0;
$show_not_available = false;

// If movie_id provided, check if shows exist
if ($preselect_movie_id > 0) {
    $check_shows = mysqli_query($connection, "SELECT COUNT(*) as show_count FROM shows WHERE movie_id = $preselect_movie_id");
    $show_data = mysqli_fetch_assoc($check_shows);
    if ($show_data['show_count'] == 0) {
        $show_not_available = true;
    }
}

// Get all theaters
$all_theaters_res = mysqli_query($connection, "SELECT id, theater_name, location FROM theaters ORDER BY theater_name");
$all_theaters = $all_theaters_res ? mysqli_fetch_all($all_theaters_res, MYSQLI_ASSOC) : [];

// Get all movies
$all_movies_res = mysqli_query($connection, "SELECT DISTINCT m.id, m.title FROM movies m JOIN shows s ON s.movie_id = m.id ORDER BY m.title");
$movies_count   = $all_movies_res ? mysqli_num_rows($all_movies_res) : 0;
// Reset pointer for later use
mysqli_data_seek($all_movies_res, 0);

// Get all shows data with pricing and seat availability for JS
$shows_json = [];
$shows_res  = mysqli_query($connection, "SELECT s.*, m.poster
              FROM shows s
              JOIN movies m ON m.id = s.movie_id
              ORDER BY s.show_date, s.show_time");
if ($shows_res) {
    while ($row = mysqli_fetch_assoc($shows_res)) {
        $show_id = $row['id'];
        $theater_id = $row['theater_id'];

        // Get pricing for this show
        $pricing_res = mysqli_query($connection, "SELECT c.class_name, scp.price FROM show_class_pricing scp JOIN classes c ON c.id = scp.class_id WHERE scp.show_id = $show_id");
        while ($p = mysqli_fetch_assoc($pricing_res)) {
            $row[strtolower($p['class_name']) . '_price'] = $p['price'];
        }

        // Get available seats for each class for this show
        $classes_res = mysqli_query($connection, "SELECT id, class_name FROM classes");
        while ($class_row = mysqli_fetch_assoc($classes_res)) {
            $class_id = $class_row['id'];
            $class_key = strtolower($class_row['class_name']);

            // Get total capacity for this theater/class
            $cap_q = mysqli_query($connection, "SELECT COUNT(*) as total FROM seats WHERE theater_id = $theater_id AND class_id = $class_id");
            $total_capacity = 0;
            if ($cap_q && mysqli_num_rows($cap_q) > 0) {
                $cap_data = mysqli_fetch_assoc($cap_q);
                $total_capacity = (int) $cap_data['total'];
            }

            // Get already booked seats for this show/class from booking_seats
            $booked_q = mysqli_query($connection, "SELECT bs.seat_id FROM booking_seats bs
                                                   JOIN bookings b ON b.id = bs.booking_id
                                                   WHERE b.show_id = $show_id AND b.class_id = $class_id");
            $booked_seat_ids = [];
            while ($booked_row = mysqli_fetch_assoc($booked_q)) {
                $booked_seat_ids[] = (int) $booked_row['seat_id'];
            }
            $booked_seats = count($booked_seat_ids);

            $available = $total_capacity - $booked_seats;
            $row[$class_key . '_available'] = $available > 0 ? $available : 0;
            $row[$class_key . '_booked_seats'] = $booked_seat_ids;
        }

        $shows_json[] = $row;
    }
}

// Fetch all classes for dynamic display
$all_classes = get_all_classes($connection);

// Default prices 0 until movie selected
$class_prices = [];
foreach ($all_classes as $cls) {
    $class_prices[strtolower($cls['class_name'])] = 0;
}
?>

<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <?php if ($movies_count <= 0): ?>
                <div class="alert alert-warning p-5 text-center shadow-sm rounded-4">
                    <i class="fa fa-calendar-times fa-4x mb-3 text-warning"></i>
                    <h2 class="fw-bold">No Shows Available!</h2>
                    <p class="lead">Pehle Admin Panel mein ja kar <b>Movies</b> aur unke <b>Shows</b> add karein.</p>
                    <a href="admin_panel/admin_dashboard.php" class="btn btn-warning px-4 fw-bold">Go to Admin Panel</a>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger p-4 mb-4 fw-bold rounded-3">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($show_not_available): ?>
                <div class="alert alert-warning p-4 mb-4 fw-bold rounded-3">
                    ⚠️ This show is not available right now. Please select another movie.
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <!-- SUCCESS CARD -->
                <div class="card shadow border-0 rounded-4 p-5 text-center" style="background: #f8fff9;">
                    <div class="mb-3">
                        <i class="fa fa-check-circle fa-4x text-success"></i>
                    </div>
                    <h1 class="text-success fw-bold">Booking Successful!</h1>
                    <hr>
                    <p class="lead">Your ticket for <b><?php echo htmlspecialchars($details['movie']); ?></b> is confirmed.</p>
                    <div class="alert alert-light border text-start d-inline-block p-4 mt-3 rounded-3">
                        <p class="mb-1"><b>📅 Date:</b> <?php echo htmlspecialchars($details['date']); ?></p>
                        <p class="mb-1"><b>🕐 Time:</b> <?php echo htmlspecialchars($details['time']); ?></p>
                        <p class="mb-1"><b>💺 Class:</b> <?php echo htmlspecialchars($details['class']); ?></p>
                        <p class="mb-1"><b>🎟️ Seats:</b> <?php echo $details['seats']; ?></p>
                        <p class="mb-0"><b>💰 Total Price:</b> <span class="text-success fw-bold">Rs. <?php echo number_format($details['total']); ?></span></p>
                    </div>
                    <div class="mt-4">
                        <button onclick="window.print()" class="btn btn-dark btn-lg px-5">🖨️ Print My Ticket</button>
                        <br><br>
                        <a href="bookings.php" class="btn btn-outline-primary me-2">Book Another</a>
                        <a href="index.php" class="text-decoration-none text-muted">← Back to Home</a>
                    </div>
                </div>

            <?php else: ?>
                <!-- BOOKING FORM -->
                <div class="card shadow-sm border-0 rounded-4 p-4 p-lg-5">
                    <h2 class="fw-bold mb-4">🎬 Book Your Movie</h2>
                    <form action="bookings.php" method="POST">
                        <div class="row g-4">
                            <!-- Poster Box -->
                            <div class="col-md-5">
                                <div id="posterBox" class="bg-light rounded-4 d-flex align-items-center justify-content-center overflow-hidden" style="height: 400px;">
                                    <span class="text-muted">Select movie to see poster</span>
                                </div>
                            </div>

                            <!-- Form Fields -->
                            <div class="col-md-7">
                                <!-- Step 1: Theater -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">1. Select Theater</label>
                                    <select name="theater_id" id="t_id" class="form-select py-2" required>
                                        <option value="">Choose Theater...</option>
                                        <?php foreach ($all_theaters as $t): ?>
                                            <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['theater_name'] . ' - ' . $t['location']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Step 2: Movie -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">2. Select Movie</label>
                                    <select name="movie_id" id="m_id" class="form-select py-2" required disabled>
                                        <option value="">Select Theater First</option>
                                    </select>
                                    <small class="text-muted" id="movieHint">Choose a theater to see available movies</small>
                                </div>

                                <!-- Step 3: Date with Calendar -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">3. Select Show Date</label>
                                    <input type="date" id="d_id" class="form-control py-2" required min="<?php echo date('Y-m-d'); ?>">
                                    <small class="text-muted">Select Movie and Theater first</small>
                                </div>

                                <!-- Step 4: Showtime -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">4. Select Showtime</label>
                                    <div id="timeSlots" class="d-flex flex-wrap gap-2">
                                        <span class="text-muted">Select Date First</span>
                                    </div>
                                    <input type="hidden" name="show_id" id="s_id" value="" required>
                                    <small class="text-danger" id="timeError" style="display: none;">Please select a showtime</small>
                                </div>

                                <!-- Step 5: Seat Class -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">5. Select Class</label>
                                    <div class="d-flex gap-2">
                                        <?php 
                                        $btn_colors = ['warning', 'secondary', 'danger', 'success', 'info', 'dark'];
                                        $icons = ['⭐', '💎', '👑', '🎭', '🎬', '🏆'];
                                        $i = 0;
                                        foreach ($all_classes as $cls): 
                                            $class_key = strtolower($cls['class_name']);
                                            $btn_color = $btn_colors[$i % count($btn_colors)];
                                            $icon = $icons[$i % count($icons)];
                                            $is_first = ($i === 0);
                                        ?>
                                        <div class="flex-fill text-center">
                                            <input type="radio" class="btn-check" name="seat_class" id="class_<?php echo $cls['id']; ?>" value="<?php echo $class_key; ?>" <?php echo $is_first ? 'required' : ''; ?>>
                                            <label class="btn btn-outline-<?php echo $btn_color; ?> w-100 py-3" for="class_<?php echo $cls['id']; ?>"><?php echo $icon . ' ' . htmlspecialchars($cls['class_name']); ?></label>
                                            <small class="text-muted d-block mt-1" id="price-<?php echo $class_key; ?>">Rs. <?php echo number_format($class_prices[$class_key] ?? 0); ?></small>
                                            <small class="text-success d-block" id="available-<?php echo $class_key; ?>">Available: --</small>
                                        </div>
                                        <?php $i++; endforeach; ?>
                                    </div>
                                </div>

                                <!-- Step 6: Visual Seat Selection -->
                                <div class="mb-4" id="seatSelectionArea" style="display: none;">
                                    <label class="form-label fw-bold">6. Select Your Seats</label>

                                    <!-- Screen -->
                                    <div class="text-center mb-4">
                                        <div style="background: linear-gradient(180deg, #333 0%, #666 100%); color: white; padding: 15px 50px; border-radius: 10px 10px 50px 50px; margin: 0 auto; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                                            <i class="fas fa-tv me-2"></i> SCREEN
                                        </div>
                                    </div>

                                    <!-- Seat Map -->
                                    <div id="seatMap" class="text-center mb-3">
                                        <!-- Seats will be generated by JavaScript -->
                                    </div>

                                    <!-- Legend -->
                                    <div class="d-flex justify-content-center gap-3 mb-3">
                                        <span class="badge bg-success">Available</span>
                                        <span class="badge bg-danger">Booked</span>
                                        <span class="badge bg-primary">Selected</span>
                                    </div>

                                    <!-- Selected Seats Display -->
                                    <div class="alert alert-info" id="selectedSeatsDisplay">
                                        <strong>Selected Seats:</strong> <span id="selectedSeatsList">None</span>
                                    </div>

                                    <!-- Hidden inputs for selected seats -->
                                    <input type="hidden" name="seats" id="total_seats" value="0">
                                    <input type="hidden" name="selected_seat_ids" id="selected_seat_ids" value="">
                                </div>

                                <!-- Kids Selection (shown after seats selected) -->
                                <div class="row g-3" id="kidsSection" style="display: none;">
                                    <div class="col-6">
                                        <label class="form-label fw-bold small">Kids (50% off)</label>
                                        <input type="number" name="kids_count" id="kids_count" class="form-control" value="0" min="0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold small">Adults</label>
                                        <input type="number" id="adults_count" class="form-control" value="0" min="0" disabled>
                                    </div>
                                </div>
                                <div class="mt-2 text-muted small" id="kidsHint" style="display: none;">
                                    💡 Kids get 50% discount. Adults pay full price.
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold mt-4 shadow" id="confirmBtn" disabled>
                                    🎟️ CONFIRM BOOKING
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
const allData = <?php echo json_encode($shows_json); ?>;
const allMovies = <?php 
// Create array of all movies with their IDs and titles
$movies_array = [];
mysqli_data_seek($all_movies_res, 0);
while ($m = mysqli_fetch_assoc($all_movies_res)) {
    $movies_array[] = $m;
}
echo json_encode($movies_array); 
?>;
const t_sel   = document.getElementById('t_id');
const m_sel   = document.getElementById('m_id');
const d_sel   = document.getElementById('d_id');
const s_sel   = document.getElementById('s_id');
const posterBox = document.getElementById('posterBox');
const movieHint = document.getElementById('movieHint');

// Price elements for all classes
const priceElements = {
<?php foreach ($all_classes as $cls): 
    $class_key = strtolower($cls['class_name']); 
    echo "    '$class_key': document.getElementById('price-$class_key'),\n";
endforeach; ?>
};

const availableElements = {
<?php foreach ($all_classes as $cls): 
    $class_key = strtolower($cls['class_name']); 
    echo "    '$class_key': document.getElementById('available-$class_key'),\n";
endforeach; ?>
};

// Default prices 0 until movie selected
const defaultPrices = {
<?php foreach ($all_classes as $cls): 
    $class_key = strtolower($cls['class_name']); 
    echo "    '$class_key': 0,\n";
endforeach; ?>
};

// Preselected movie from URL
const preselectMovieId = <?php echo $preselect_movie_id; ?>;

function formatPrice(price) {
    return 'Rs. ' + parseInt(price).toLocaleString();
}

// Helper: Get unique available dates for theater+movie
document.addEventListener('DOMContentLoaded', function() {
    // Set min date for calendar (today)
    const today = new Date().toISOString().split('T')[0];
    d_sel.min = today;
});

// Helper: Update movie dropdown based on theater selection
function updateMoviesForTheater() {
    const tid = t_sel.value;
    
    // Reset movie dropdown
    m_sel.innerHTML = '';
    m_sel.value = '';
    
    if (!tid) {
        // No theater selected
        m_sel.disabled = true;
        m_sel.innerHTML = '<option value="">Select Theater First</option>';
        movieHint.textContent = 'Choose a theater to see available movies';
        return;
    }
    
    // Get movies that have shows in this theater
    const availableMovieIds = [...new Set(allData.filter(x => x.theater_id == tid).map(x => x.movie_id))];
    const availableMovies = allMovies.filter(m => availableMovieIds.includes(m.id));
    
    if (availableMovies.length === 0) {
        m_sel.disabled = true;
        m_sel.innerHTML = '<option value="">No movies available</option>';
        movieHint.textContent = 'No movies available in this theater';
        return;
    }
    
    // Enable and populate dropdown
    m_sel.disabled = false;
    m_sel.innerHTML = '<option value="">Choose Movie...</option>';
    availableMovies.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.id;
        opt.textContent = m.title;
        m_sel.appendChild(opt);
    });
    movieHint.textContent = 'Select a movie to continue';
}

// Theater change handler
t_sel.addEventListener('change', function() {
    updateMoviesForTheater();
    updatePosterAndPrices();
    updateAvailableDates();
    timeSlotsContainer.innerHTML = '<span class="text-muted">Select Date First</span>';
    s_sel.value = '';
    d_sel.value = '';
});

// Movie change handler
m_sel.addEventListener('change', function () {
    updatePosterAndPrices();
    updateAvailableDates();
    timeSlotsContainer.innerHTML = '<span class="text-muted">Select Date First</span>';
    s_sel.value = '';
    d_sel.value = '';
});

// Preselect handling - wait for theater to be selected first, then set movie
if (preselectMovieId > 0) {
    const checkAndSetMovie = setInterval(function() {
        if (t_sel.value) {
            // Theater is selected, check if movie is available
            const tid = t_sel.value;
            const availableMovieIds = [...new Set(allData.filter(x => x.theater_id == tid).map(x => x.movie_id))];
            if (availableMovieIds.includes(preselectMovieId)) {
                m_sel.value = preselectMovieId;
                // Trigger change to load dates
                const event = new Event('change');
                m_sel.dispatchEvent(event);
            }
            clearInterval(checkAndSetMovie);
        }
    }, 100);
}

// Helper: Update poster and prices
function updatePosterAndPrices() {
    const tid = t_sel.value;
    const mid = m_sel.value;
    
    if (!mid) {
        posterBox.innerHTML = '<span class="text-muted">Select movie to see poster</span>';
        Object.keys(priceElements).forEach(key => {
            if (priceElements[key]) priceElements[key].textContent = 'Rs. 0';
        });
        return;
    }
    
    // Filter by movie (and theater if selected)
    let filtered = allData.filter(x => x.movie_id == mid);
    if (tid) {
        filtered = filtered.filter(x => x.theater_id == tid);
    }
    
    if (filtered.length > 0 && filtered[0].poster) {
        posterBox.innerHTML = `<img src="images/${filtered[0].poster}" class="w-100 h-100 rounded-4" style="object-fit:cover;" onerror="this.parentElement.innerHTML='<span class=text-muted>No poster available</span>'">`;
    }

    // Update prices - get first show's pricing
    const firstShow = filtered[0];
    if (firstShow) {
<?php foreach ($all_classes as $cls): 
    $class_key = strtolower($cls['class_name']); 
    $js_key = str_replace(' ', '_', $class_key);
    $price_field = $class_key . '_price';
    echo "        const {$js_key}Price = firstShow['{$price_field}'] > 0 ? firstShow['{$price_field}'] : defaultPrices['$class_key'];\n";
    echo "        if (priceElements['$class_key']) priceElements['$class_key'].textContent = formatPrice({$js_key}Price);\n";
endforeach; ?>
    }
}

// Helper: Enable/disable dates based on available shows
function updateAvailableDates() {
    const tid = t_sel.value;
    const mid = m_sel.value;
    
    // Reset calendar
    d_sel.value = '';
    
    if (!mid || !tid) {
        d_sel.disabled = true;
        d_sel.title = 'Please select Theater and Movie first';
        return;
    }
    
    d_sel.disabled = false;
    d_sel.title = 'Pick a show date';
    
    // Get available dates for this theater+movie
    const filtered = allData.filter(x => x.movie_id == mid && x.theater_id == tid);
    const availableDates = [...new Set(filtered.map(x => x.show_date))].sort();
    
    // Store available dates for validation
    d_sel.dataset.availableDates = JSON.stringify(availableDates);
}

// Get time slots container
const timeSlotsContainer = document.getElementById('timeSlots');

// Date change handler - validate and load showtimes
d_sel.addEventListener('change', function () {
    const tid  = t_sel.value;
    const mid  = m_sel.value;
    const date = this.value;
    
    // Reset time slots
    timeSlotsContainer.innerHTML = '';
    s_sel.value = '';
    
    if (!date || !tid || !mid) {
        timeSlotsContainer.innerHTML = '<span class="text-muted">Select Date First</span>';
        return;
    }
    
    // Validate date is available
    const availableDates = JSON.parse(d_sel.dataset.availableDates || '[]');
    if (!availableDates.includes(date)) {
        Swal.fire({
            icon: 'warning',
            title: 'No Shows Available',
            text: 'No shows available on this date. Please select another date.',
            confirmButtonColor: '#007bff'
        });
        d_sel.value = '';
        timeSlotsContainer.innerHTML = '<span class="text-muted">Select Date First</span>';
        return;
    }

    // Filter by theater + movie + date
    const filtered = allData.filter(x => x.movie_id == mid && x.theater_id == tid && x.show_date == date);
    
    if (filtered.length === 0) {
        timeSlotsContainer.innerHTML = '<span class="text-muted">No shows available</span>';
        return;
    }
    
    // Generate time slot buttons
    filtered.forEach(s => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-outline-primary time-slot-btn';
        btn.dataset.showId = s.id;
        btn.textContent = s.show_time;
        btn.style.minWidth = '80px';
        
        // Click handler
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.time-slot-btn').forEach(b => {
                b.classList.remove('btn-primary', 'active');
                b.classList.add('btn-outline-primary');
            });
            
            // Add active class to clicked button
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary', 'active');
            
            // Set hidden input value
            s_sel.value = this.dataset.showId;
            document.getElementById('timeError').style.display = 'none';
            
            // Update available seats for this show
            const selectedShow = allData.find(x => x.id == s.id);
            if (selectedShow) {
<?php foreach ($all_classes as $cls):
    $class_key = strtolower($cls['class_name']);
    $js_key = str_replace(' ', '_', $class_key);
    echo "                const avail$js_key = selectedShow['{$class_key}_available'] ?? 0;\n";
    echo "                if (availableElements['$class_key']) availableElements['$class_key'].textContent = 'Available: ' + avail$js_key;\n";
endforeach; ?>
            }
            
            // Mark booked seats
            markBookedSeats();
        });
        
        timeSlotsContainer.appendChild(btn);
    });
});

// Helper: Mark booked seats on the seat map
function markBookedSeats() {
    const showId = s_sel.value;
    const selectedClass = document.querySelector('input[name="seat_class"]:checked')?.value;
    if (!showId || !selectedClass) return;

    const selectedShow = allData.find(x => x.id == showId);
    if (!selectedShow) return;

    // Get booked seat IDs for this class
    const bookedSeatIds = selectedShow[selectedClass + '_booked_seats'] || [];

    // Mark each booked seat
    bookedSeatIds.forEach(seatId => {
        const seatBtn = document.querySelector(`.seat[data-seat-id="${seatId}"]`);
        if (seatBtn) {
            seatBtn.classList.remove('available');
            seatBtn.classList.add('booked');
            seatBtn.style.background = '#dc3545';
            seatBtn.style.cursor = 'not-allowed';
            seatBtn.style.opacity = '0.6';
        }
    });
}

// Auto-calculate adults count and validate kids count
const totalSeatsInput = document.getElementById('total_seats');
const kidsCountInput = document.getElementById('kids_count');
const adultsCountInput = document.getElementById('adults_count');

function updateAdultsCount() {
    const total = parseInt(totalSeatsInput.value) || 1;
    const kids = parseInt(kidsCountInput.value) || 0;
    const adults = total - kids;
    adultsCountInput.value = adults < 0 ? 0 : adults;

    // Ensure kids count doesn't exceed total seats
    if (kids > total) {
        kidsCountInput.value = total;
        adultsCountInput.value = 0;
    }
}

totalSeatsInput.addEventListener('change', updateAdultsCount);
kidsCountInput.addEventListener('change', updateAdultsCount);

// Theater or movie change - reset time slots
t_sel.addEventListener('change', function() {
    timeSlotsContainer.innerHTML = '<span class="text-muted">Select Date First</span>';
    s_sel.value = '';
});

m_sel.addEventListener('change', function() {
    timeSlotsContainer.innerHTML = '<span class="text-muted">Select Date First</span>';
    s_sel.value = '';
});

// ============================================
// VISUAL SEAT SELECTION
// ============================================
let selectedSeats = [];
let selectedSeatIds = [];
const seatMapContainer = document.getElementById('seatMap');
const seatSelectionArea = document.getElementById('seatSelectionArea');
const kidsSection = document.getElementById('kidsSection');
const kidsHint = document.getElementById('kidsHint');
const confirmBtn = document.getElementById('confirmBtn');
const selectedSeatsList = document.getElementById('selectedSeatsList');
const selectedSeatIdsInput = document.getElementById('selected_seat_ids');

// Seat configuration - 8 rows, 10 seats per row
const ROWS = 8;
const SEATS_PER_ROW = 10;

// Generate seat map
function generateSeatMap() {
    seatMapContainer.innerHTML = '';

    for (let row = 1; row <= ROWS; row++) {
        const rowDiv = document.createElement('div');
        rowDiv.className = 'seat-row mb-2';
        rowDiv.style.display = 'flex';
        rowDiv.style.justifyContent = 'center';
        rowDiv.style.alignItems = 'center';
        rowDiv.style.gap = '8px';

        // Row label
        const rowLabel = document.createElement('span');
        rowLabel.textContent = String.fromCharCode(64 + row); // A, B, C...
        rowLabel.style.width = '25px';
        rowLabel.style.fontWeight = 'bold';
        rowLabel.style.color = '#666';
        rowDiv.appendChild(rowLabel);

        // Seats
        for (let seat = 1; seat <= SEATS_PER_ROW; seat++) {
            const seatNum = (row - 1) * SEATS_PER_ROW + seat;
            const seatBtn = document.createElement('div');
            seatBtn.className = 'seat available';
            seatBtn.dataset.seatId = seatNum;
            seatBtn.dataset.row = String.fromCharCode(64 + row);
            seatBtn.dataset.seat = seat;
            seatBtn.textContent = seat;

            // Styling
            seatBtn.style.width = '35px';
            seatBtn.style.height = '35px';
            seatBtn.style.background = '#28a745';
            seatBtn.style.color = 'white';
            seatBtn.style.borderRadius = '8px';
            seatBtn.style.display = 'flex';
            seatBtn.style.alignItems = 'center';
            seatBtn.style.justifyContent = 'center';
            seatBtn.style.cursor = 'pointer';
            seatBtn.style.fontSize = '12px';
            seatBtn.style.fontWeight = 'bold';
            seatBtn.style.transition = 'all 0.2s';
            seatBtn.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';

            // Hover effect
            seatBtn.addEventListener('mouseenter', function() {
                if (this.classList.contains('available')) {
                    this.style.background = '#34ce57';
                    this.style.transform = 'scale(1.1)';
                }
            });
            seatBtn.addEventListener('mouseleave', function() {
                if (this.classList.contains('available')) {
                    this.style.background = '#28a745';
                    this.style.transform = 'scale(1)';
                }
            });

            // Click handler
            seatBtn.addEventListener('click', function() {
                toggleSeatSelection(this);
            });

            rowDiv.appendChild(seatBtn);
        }

        seatMapContainer.appendChild(rowDiv);
    }
}

// Toggle seat selection
function toggleSeatSelection(seatElement) {
    const seatId = seatElement.dataset.seatId;
    const row = seatElement.dataset.row;
    const seat = seatElement.dataset.seat;
    const seatLabel = row + seat;

    // Prevent clicking on booked seats
    if (seatElement.classList.contains('booked')) {
        Swal.fire({
            icon: 'error',
            title: 'Seat Already Booked',
            text: 'This seat is already booked! Please select another seat.',
            confirmButtonColor: '#dc3545',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
        return;
    }

    if (seatElement.classList.contains('selected')) {
        // Deselect
        seatElement.classList.remove('selected');
        seatElement.classList.add('available');
        seatElement.style.background = '#28a745';
        seatElement.style.transform = 'scale(1)';

        selectedSeats = selectedSeats.filter(s => s !== seatLabel);
        selectedSeatIds = selectedSeatIds.filter(id => id !== seatId);
    } else if (seatElement.classList.contains('available')) {
        // Select
        if (selectedSeats.length >= 10) {
            Swal.fire({
                icon: 'info',
                title: 'Limit Reached',
                text: 'Maximum 10 seats can be selected',
                confirmButtonColor: '#007bff',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            return;
        }
        seatElement.classList.remove('available');
        seatElement.classList.add('selected');
        seatElement.style.background = '#007bff';
        seatElement.style.transform = 'scale(1.1)';

        selectedSeats.push(seatLabel);
        selectedSeatIds.push(seatId);
    }

    updateSelectionDisplay();
}

// Update display
function updateSelectionDisplay() {
    // Update list
    selectedSeatsList.textContent = selectedSeats.length > 0 ? selectedSeats.join(', ') : 'None';

    // Update hidden inputs
    totalSeatsInput.value = selectedSeats.length;
    selectedSeatIdsInput.value = selectedSeatIds.join(',');

    // Update max kids
    kidsCountInput.max = selectedSeats.length;
    if (parseInt(kidsCountInput.value) > selectedSeats.length) {
        kidsCountInput.value = selectedSeats.length;
    }

    // Show/hide sections
    if (selectedSeats.length > 0) {
        kidsSection.style.display = 'flex';
        kidsHint.style.display = 'block';
        confirmBtn.disabled = false;
    } else {
        kidsSection.style.display = 'none';
        kidsHint.style.display = 'none';
        confirmBtn.disabled = true;
    }

    updateAdultsCount();
}

// Show seat selection when class is selected
document.querySelectorAll('input[name="seat_class"]').forEach(radio => {
    radio.addEventListener('change', function() {
        selectedSeats = [];
        selectedSeatIds = [];
        updateSelectionDisplay();
        generateSeatMap();
        markBookedSeats();
        seatSelectionArea.style.display = 'block';
    });
});

// Initialize
kidsCountInput.addEventListener('change', updateAdultsCount);
</script>

<style>
.seat {
    user-select: none;
}
.seat.booked {
    background: #dc3545 !important;
    cursor: not-allowed !important;
    opacity: 0.6;
}
.seat.selected {
    background: #007bff !important;
    box-shadow: 0 0 10px rgba(0,123,255,0.5) !important;
}

/* Time Slot Button Styles */
.time-slot-btn {
    border-radius: 25px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.2s ease;
    border-width: 2px;
}
.time-slot-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,123,255,0.3);
}
.time-slot-btn.active {
    background: #007bff !important;
    border-color: #007bff !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(0,123,255,0.4) !important;
}
</style>

<?php include_once "footer.php"; ?>