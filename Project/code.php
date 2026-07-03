<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// __DIR__ use karo taake hosting par bhi sahi path mile
include_once __DIR__ . '/connection.php';

function set_flash($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash()
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Hosting-safe redirect function
 * Relative path automatically absolute URL mein convert hoti hai
 */
function redirect_to($path)
{
    // Agar already absolute URL hai to direct use karo
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        header("Location: {$path}");
        exit();
    }

    // Server se base URL detect karo (hosting par bhi kaam karega)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];

    // Script ka directory path pata karo
    // code.php project root mein hai
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);

    // Project root calculate karo
    // Agar admin_panel ke andar se call hua to ek level upar
    $currentScript = basename($_SERVER['SCRIPT_NAME']);
    $currentDir = basename(dirname($_SERVER['SCRIPT_NAME']));

    if ($currentDir === 'admin_panel') {
        // admin_panel ke andar se call -> project root ek level upar
        $projectRoot = dirname(dirname($_SERVER['SCRIPT_NAME']));
    } else {
        // Direct project root se call
        $projectRoot = dirname($_SERVER['SCRIPT_NAME']);
    }

    // Trailing slash hatao
    $projectRoot = rtrim($projectRoot, '/');

    // Path ke aage slash lagao agar nahi hai
    $path = ltrim($path, '/');

    $fullUrl = $protocol . '://' . $host . $projectRoot . '/' . $path;
    header("Location: {$fullUrl}");
    exit();
}

function require_admin()
{
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        set_flash('danger', 'Admin access required.');
        // redirect_to() ab automatically project root se login.php find kar lega
        redirect_to('login.php');
    }
}

function validate_required($data, $fields)
{
    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
            return false;
        }
    }
    return true;
}

function ensure_admin_schema($connection)
{
    $dbName = null;
    $dbRes = mysqli_query($connection, "SELECT DATABASE() AS db_name");
    if ($dbRes) {
        $dbName = mysqli_fetch_assoc($dbRes)['db_name'];
    }

    if (!$dbName) {
        return;
    }

    $has_column = function ($table, $column) use ($connection, $dbName) {
        $res = mysqli_query($connection, "SHOW COLUMNS FROM `$table` LIKE '$column'");
        return $res && mysqli_num_rows($res) > 0;
    };

    if (!$has_column('theaters', 'screens')) {
        mysqli_query($connection, "ALTER TABLE theaters ADD COLUMN screens INT NOT NULL DEFAULT 1");
    }
    if (!$has_column('movies', 'genre')) {
        mysqli_query($connection, "ALTER TABLE movies ADD COLUMN genre VARCHAR(255) NULL");
    }
    if (!$has_column('movies', 'release_date')) {
        mysqli_query($connection, "ALTER TABLE movies ADD COLUMN release_date DATE NULL");
    }
    if (!$has_column('movies', 'movie_status')) {
        mysqli_query($connection, "ALTER TABLE movies ADD COLUMN movie_status VARCHAR(20) NOT NULL DEFAULT 'now_showing'");
    }
    if (!$has_column('movies', 'is_featured')) {
        mysqli_query($connection, "ALTER TABLE movies ADD COLUMN is_featured TINYINT(1) NOT NULL DEFAULT 0");
    }
    if (!$has_column('users', 'phone')) {
        mysqli_query($connection, "ALTER TABLE users ADD COLUMN phone VARCHAR(30) NULL");
    }
    if (!$has_column('users', 'status')) {
        mysqli_query($connection, "ALTER TABLE users ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'Active'");
    }
    if (!$has_column('bookings', 'has_kids')) {
        mysqli_query($connection, "ALTER TABLE bookings ADD COLUMN has_kids TINYINT(1) NOT NULL DEFAULT 0");
    }

    // show_class_pricing table is created by database SQL, no need to create here

    mysqli_query(
        $connection,
        "CREATE TABLE IF NOT EXISTS carousel (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            image VARCHAR(255) NOT NULL,
            status TINYINT(1) NOT NULL DEFAULT 1,
            display_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )"
    );

    if (!$has_column('carousel', 'display_order')) {
        mysqli_query($connection, "ALTER TABLE carousel ADD COLUMN display_order INT NOT NULL DEFAULT 0");
    }
    if (!$has_column('carousel', 'status')) {
        mysqli_query($connection, "ALTER TABLE carousel ADD COLUMN status TINYINT(1) NOT NULL DEFAULT 1");
    }
    if (!$has_column('carousel', 'title')) {
        mysqli_query($connection, "ALTER TABLE carousel ADD COLUMN title VARCHAR(255) NOT NULL DEFAULT 'Untitled Slide'");
    }
}

function get_all_theaters($connection)
{
    $result = mysqli_query($connection, "SELECT id, theater_name, location, screens FROM theaters ORDER BY id DESC");
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_all_movies($connection)
{
    $sql = "SELECT m.id, m.title, m.duration, m.release_date, m.movie_status, m.is_featured,
                   m.poster, m.trailer_link, m.movie_desc, m.director, m.language, m.rating,
                   GROUP_CONCAT(c.category_name SEPARATOR ', ') AS categories
            FROM movies m
            LEFT JOIN movie_category mc ON m.id = mc.movi_id
            LEFT JOIN category c ON mc.cat_id = c.id
            GROUP BY m.id
            ORDER BY m.id DESC";
    $result = mysqli_query($connection, $sql);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_all_categories($connection)
{
    $result = mysqli_query($connection, "SELECT id, category_name, status FROM category ORDER BY id DESC");
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_all_classes($connection)
{
    $result = mysqli_query($connection, "SELECT id, class_name FROM classes ORDER BY id DESC");
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_theater_seat_capacity($connection, $theater_id)
{
    // Get seat capacity for each class in this theater
    $sql = "SELECT class_id, COUNT(*) as seat_count FROM seats WHERE theater_id = ? GROUP BY class_id";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $theater_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $capacities = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $capacities[$row['class_id']] = $row['seat_count'];
    }
    return $capacities;
}

function create_theater_seats($connection, $theater_id, $class_capacities)
{
    // Create seats for each class with specified capacity
    // Format: $class_capacities = [class_id => count, ...]
    foreach ($class_capacities as $class_id => $count) {
        $class_id = (int) $class_id;
        $count = (int) $count;
        if ($count > 0) {
            // Delete existing seats for this theater-class combination
            $del = mysqli_prepare($connection, "DELETE FROM seats WHERE theater_id = ? AND class_id = ?");
            mysqli_stmt_bind_param($del, "ii", $theater_id, $class_id);
            mysqli_stmt_execute($del);

            // Insert new seats
            $stmt = mysqli_prepare($connection, "INSERT INTO seats (theater_id, class_id, seat_number) VALUES (?, ?, ?)");
            for ($i = 1; $i <= $count; $i++) {
                $seat_num = 'S' . $i;
                mysqli_stmt_bind_param($stmt, "iis", $theater_id, $class_id, $seat_num);
                mysqli_stmt_execute($stmt);
            }
        }
    }
}

function get_all_reviews($connection)
{
    $sql = "SELECT r.id, r.review, r.rating, r.created_at,
                   u.name AS user_name, m.title AS movie_title
            FROM review r
            LEFT JOIN users u ON u.id = r.users_id
            LEFT JOIN movies m ON m.id = r.movies_id
            ORDER BY r.id DESC";
    $result = mysqli_query($connection, $sql);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_all_shows($connection)
{
    $sql = "SELECT s.id, s.movie_id, s.theater_id, m.title AS movie_title, t.theater_name, s.show_date, s.show_time
            FROM shows s
            LEFT JOIN movies m ON m.id = s.movie_id
            LEFT JOIN theaters t ON t.id = s.theater_id
            ORDER BY s.id DESC";
    $result = mysqli_query($connection, $sql);
    $shows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

    // Get pricing for each show
    foreach ($shows as &$show) {
        $show_id = $show['id'];
        $pricing_sql = "SELECT c.class_name, scp.price FROM show_class_pricing scp JOIN classes c ON c.id = scp.class_id WHERE scp.show_id = $show_id";
        $pricing_result = mysqli_query($connection, $pricing_sql);
        while ($p = mysqli_fetch_assoc($pricing_result)) {
            $show[strtolower($p['class_name']) . '_price'] = $p['price'];
        }
    }
    return $shows;
}

function get_all_users($connection)
{
    $result = mysqli_query($connection, "SELECT id, name, email, phone, role, status FROM users ORDER BY id DESC");
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_all_carousels($connection)
{
    $sql = "SELECT id, movie_id, title, image, status, display_order, created_at
            FROM carousel
            ORDER BY display_order ASC, id DESC";
    $result = mysqli_query($connection, $sql);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_dashboard_stats($connection)
{
    $stats = ['total_users' => 0, 'total_movies' => 0, 'total_bookings' => 0];
    $resUsers    = mysqli_query($connection, "SELECT COUNT(*) AS total FROM users");
    $resMovies   = mysqli_query($connection, "SELECT COUNT(*) AS total FROM movies");
    $resBookings = mysqli_query($connection, "SELECT COUNT(*) AS total FROM bookings");
    if ($resUsers)    $stats['total_users']    = (int) mysqli_fetch_assoc($resUsers)['total'];
    if ($resMovies)   $stats['total_movies']   = (int) mysqli_fetch_assoc($resMovies)['total'];
    if ($resBookings) $stats['total_bookings'] = (int) mysqli_fetch_assoc($resBookings)['total'];
    return $stats;
}

function get_recent_bookings($connection, $limit = 10)
{
    $limit = (int) $limit;
    $sql = "SELECT b.id, u.name AS user_name, m.title AS movie_title, t.theater_name, c.class_name,
                   b.booking_date, b.total_seats, b.total_price, b.kids_count, b.adults_count
            FROM bookings b
            LEFT JOIN users u ON u.id = b.user_id
            LEFT JOIN shows s ON s.id = b.show_id
            LEFT JOIN movies m ON m.id = s.movie_id
            LEFT JOIN theaters t ON t.id = s.theater_id
            LEFT JOIN classes c ON c.id = b.class_id
            ORDER BY b.id DESC
            LIMIT {$limit}";
    $result = mysqli_query($connection, $sql);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_all_bookings($connection)
{
    return get_recent_bookings($connection, 200);
}

function get_all_bookings_unlimited($connection)
{
    $sql = "SELECT b.id, u.name AS user_name, m.title AS movie_title, t.theater_name, c.class_name,
                   b.booking_date, b.total_seats, b.total_price, b.kids_count, b.adults_count
            FROM bookings b
            LEFT JOIN users u ON u.id = b.user_id
            LEFT JOIN shows s ON s.id = b.show_id
            LEFT JOIN movies m ON m.id = s.movie_id
            LEFT JOIN theaters t ON t.id = s.theater_id
            LEFT JOIN classes c ON c.id = b.class_id
            ORDER BY b.id DESC";
    $result = mysqli_query($connection, $sql);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_bookings_by_day($connection, $days = 7)
{
    $days = (int) $days;
    $sql = "SELECT 
                DATE(booking_date) as date,
                COUNT(*) as bookings,
                SUM(total_price) as revenue
            FROM bookings
            WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL {$days} DAY)
            GROUP BY DATE(booking_date)
            ORDER BY date ASC";
    $result = mysqli_query($connection, $sql);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

ensure_admin_schema($connection);

// ============================================
// ADMIN PANEL ACTIONS
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_action'])) {
    require_admin();
    $action = $_POST['admin_action'];

    // -------- THEATER MANAGEMENT --------
    if ($action === 'add_theater' && validate_required($_POST, ['theater_name', 'location', 'screens'])) {
        $stmt = mysqli_prepare($connection, "INSERT INTO theaters (theater_name, location, screens) VALUES (?, ?, ?)");
        $name     = trim($_POST['theater_name']);
        $location = trim($_POST['location']);
        $screens  = (int) $_POST['screens'];
        mysqli_stmt_bind_param($stmt, "ssi", $name, $location, $screens);
        mysqli_stmt_execute($stmt);
        $theater_id = mysqli_insert_id($connection);

        // Create seats for each class with specified capacity
        if (isset($_POST['seat_capacity']) && is_array($_POST['seat_capacity'])) {
            create_theater_seats($connection, $theater_id, $_POST['seat_capacity']);
        }

        set_flash('success', 'Theater and seats added successfully.');
        redirect_to('admin_panel/admin_theaters.php');
    }

    if ($action === 'delete_theater' && validate_required($_POST, ['id'])) {
        $id = (int) $_POST['id'];

        // Get all shows for this theater to delete related data
        $shows_res = mysqli_query($connection, "SELECT id FROM shows WHERE theater_id = $id");
        $show_ids = [];
        while ($row = mysqli_fetch_assoc($shows_res)) {
            $show_ids[] = $row['id'];
        }

        // Delete booking_seats for bookings of these shows
        if (!empty($show_ids)) {
            $show_ids_str = implode(',', $show_ids);
            mysqli_query($connection, "DELETE FROM booking_seats WHERE booking_id IN (SELECT id FROM bookings WHERE show_id IN ($show_ids_str))");
            mysqli_query($connection, "DELETE FROM bookings WHERE show_id IN ($show_ids_str)");
            mysqli_query($connection, "DELETE FROM show_class_pricing WHERE show_id IN ($show_ids_str)");
            mysqli_query($connection, "DELETE FROM shows WHERE id IN ($show_ids_str)");
        }

        // Delete booking_seats for this theater's seats
        $delBookingSeats = mysqli_prepare($connection, "DELETE FROM booking_seats WHERE seat_id IN (SELECT id FROM seats WHERE theater_id = ?)");
        mysqli_stmt_bind_param($delBookingSeats, "i", $id);
        mysqli_stmt_execute($delBookingSeats);
        mysqli_stmt_close($delBookingSeats);

        // Delete associated seats
        $delSeats = mysqli_prepare($connection, "DELETE FROM seats WHERE theater_id = ?");
        mysqli_stmt_bind_param($delSeats, "i", $id);
        mysqli_stmt_execute($delSeats);
        mysqli_stmt_close($delSeats);

        // Now delete the theater
        $stmt = mysqli_prepare($connection, "DELETE FROM theaters WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        set_flash('success', 'Theater and all related records deleted.');
        redirect_to('admin_panel/admin_theaters.php');
    }

    if ($action === 'update_theater' && validate_required($_POST, ['id', 'theater_name', 'location', 'screens'])) {
        $stmt     = mysqli_prepare($connection, "UPDATE theaters SET theater_name = ?, location = ?, screens = ? WHERE id = ?");
        $id       = (int) $_POST['id'];
        $name     = trim($_POST['theater_name']);
        $location = trim($_POST['location']);
        $screens  = (int) $_POST['screens'];
        mysqli_stmt_bind_param($stmt, "ssii", $name, $location, $screens, $id);
        mysqli_stmt_execute($stmt);

        // Update seats for each class with specified capacity
        if (isset($_POST['seat_capacity']) && is_array($_POST['seat_capacity'])) {
            create_theater_seats($connection, $id, $_POST['seat_capacity']);
        }

        set_flash('success', 'Theater and seats updated successfully.');
        redirect_to('admin_panel/admin_theaters.php');
    }

    // -------- MOVIE MANAGEMENT --------
    if ($action === 'add_movie' && validate_required($_POST, ['title', 'duration'])) {
        $posterFilename = '';
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/images/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext            = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
            $posterFilename = uniqid('poster_') . '.' . $ext;
            move_uploaded_file($_FILES['poster']['tmp_name'], $uploadDir . $posterFilename);
        }

        $title       = trim($_POST['title']);
        $duration    = trim($_POST['duration']);
        $release     = !empty($_POST['release_date']) ? $_POST['release_date'] : null;
        $movieStatus = trim($_POST['movie_status'] ?? 'now_showing');
        if (!in_array($movieStatus, ['now_showing', 'upcoming'], true)) $movieStatus = 'now_showing';
        $trailer    = trim($_POST['trailer_link'] ?? '');
        $desc       = trim($_POST['movie_desc'] ?? '');
        $director   = trim($_POST['director'] ?? '');
        $language   = trim($_POST['language'] ?? '');
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

        $stmt = mysqli_prepare($connection, "INSERT INTO movies (title, duration, release_date, movie_status, is_featured, poster, trailer_link, movie_desc, director, language) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssisssss", $title, $duration, $release, $movieStatus, $isFeatured, $posterFilename, $trailer, $desc, $director, $language);
        mysqli_stmt_execute($stmt);
        $movieId = mysqli_insert_id($connection);

        // Insert category mappings
        if (isset($_POST['categories']) && is_array($_POST['categories'])) {
            $catStmt = mysqli_prepare($connection, "INSERT INTO movie_category (movi_id, cat_id) VALUES (?, ?)");
            foreach ($_POST['categories'] as $cat_id) {
                $cat_id = (int) $cat_id;
                mysqli_stmt_bind_param($catStmt, "ii", $movieId, $cat_id);
                mysqli_stmt_execute($catStmt);
            }
        }

        set_flash('success', 'Movie added successfully.');
        redirect_to('admin_panel/admin_movies.php');
    }

    if ($action === 'delete_movie' && validate_required($_POST, ['id'])) {
        $id = (int) $_POST['id'];

        // Get all show IDs for this movie
        $shows_res = mysqli_query($connection, "SELECT id FROM shows WHERE movie_id = $id");
        $show_ids = [];
        while ($row = mysqli_fetch_assoc($shows_res)) {
            $show_ids[] = $row['id'];
        }

        // Delete related data for each show (foreign key constraints)
        if (!empty($show_ids)) {
            $show_ids_str = implode(',', $show_ids);

            // Delete booking_seats for these shows
            mysqli_query($connection, "DELETE FROM booking_seats WHERE booking_id IN (SELECT id FROM bookings WHERE show_id IN ($show_ids_str))");

            // Delete bookings for these shows
            mysqli_query($connection, "DELETE FROM bookings WHERE show_id IN ($show_ids_str)");

            // Delete show_class_pricing for these shows
            mysqli_query($connection, "DELETE FROM show_class_pricing WHERE show_id IN ($show_ids_str)");

            // Delete the shows
            mysqli_query($connection, "DELETE FROM shows WHERE id IN ($show_ids_str)");
        }

        $rev = mysqli_prepare($connection, "DELETE FROM review WHERE movies_id = ?");
        mysqli_stmt_bind_param($rev, "i", $id);
        mysqli_stmt_execute($rev);

        // Delete category mappings
        $delCat = mysqli_prepare($connection, "DELETE FROM movie_category WHERE movi_id = ?");
        mysqli_stmt_bind_param($delCat, "i", $id);
        mysqli_stmt_execute($delCat);

        $stmt = mysqli_prepare($connection, "DELETE FROM movies WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        set_flash('success', 'Movie deleted successfully.');
        redirect_to('admin_panel/admin_movies.php');
    }

    if ($action === 'update_movie' && validate_required($_POST, ['id', 'title', 'duration'])) {
        $id          = (int) $_POST['id'];
        $title       = trim($_POST['title']);
        $duration    = trim($_POST['duration']);
        $release     = !empty($_POST['release_date']) ? $_POST['release_date'] : null;
        $movieStatus = trim($_POST['movie_status'] ?? 'now_showing');
        if (!in_array($movieStatus, ['now_showing', 'upcoming'], true)) $movieStatus = 'now_showing';
        $trailer    = trim($_POST['trailer_link'] ?? '');
        $desc       = trim($_POST['movie_desc'] ?? '');
        $director   = trim($_POST['director'] ?? '');
        $language   = trim($_POST['language'] ?? '');
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

        if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/images/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext            = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
            $posterFilename = uniqid('poster_') . '.' . $ext;
            move_uploaded_file($_FILES['poster']['tmp_name'], $uploadDir . $posterFilename);

            $stmt = mysqli_prepare($connection, "UPDATE movies SET title=?, duration=?, release_date=?, movie_status=?, is_featured=?, poster=?, trailer_link=?, movie_desc=?, director=?, language=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssssisssssi", $title, $duration, $release, $movieStatus, $isFeatured, $posterFilename, $trailer, $desc, $director, $language, $id);
        } else {
            $stmt = mysqli_prepare($connection, "UPDATE movies SET title=?, duration=?, release_date=?, movie_status=?, is_featured=?, trailer_link=?, movie_desc=?, director=?, language=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssssissssi", $title, $duration, $release, $movieStatus, $isFeatured, $trailer, $desc, $director, $language, $id);
        }
        mysqli_stmt_execute($stmt);

        // Update category mappings - delete old, insert new
        $delCat = mysqli_prepare($connection, "DELETE FROM movie_category WHERE movi_id = ?");
        mysqli_stmt_bind_param($delCat, "i", $id);
        mysqli_stmt_execute($delCat);

        if (isset($_POST['categories']) && is_array($_POST['categories'])) {
            $catStmt = mysqli_prepare($connection, "INSERT INTO movie_category (movi_id, cat_id) VALUES (?, ?)");
            foreach ($_POST['categories'] as $cat_id) {
                $cat_id = (int) $cat_id;
                mysqli_stmt_bind_param($catStmt, "ii", $id, $cat_id);
                mysqli_stmt_execute($catStmt);
            }
        }

        set_flash('success', 'Movie updated.');
        redirect_to('admin_panel/admin_movies.php');
    }

    // -------- SHOW MANAGEMENT --------
    if ($action === 'add_show' && validate_required($_POST, ['movie_id', 'theater_id', 'show_date', 'show_time'])) {
        $stmt      = mysqli_prepare($connection, "INSERT INTO shows (movie_id, theater_id, show_date, show_time) VALUES (?, ?, ?, ?)");
        $movieId   = (int) $_POST['movie_id'];
        $theaterId = (int) $_POST['theater_id'];
        $showDate  = $_POST['show_date'];
        $showTime  = $_POST['show_time'];
        mysqli_stmt_bind_param($stmt, "iiss", $movieId, $theaterId, $showDate, $showTime);
        mysqli_stmt_execute($stmt);
        $showId = mysqli_insert_id($connection);

        // Insert pricing for selected classes
        if (isset($_POST['has_class']) && is_array($_POST['has_class'])) {
            foreach ($_POST['has_class'] as $class_id => $checked) {
                $class_id = (int) $class_id;
                $price = isset($_POST['class_price'][$class_id]) ? (float) $_POST['class_price'][$class_id] : 0;
                if ($price > 0) {
                    $priceStmt = mysqli_prepare($connection, "INSERT INTO show_class_pricing (show_id, class_id, price) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE price = VALUES(price)");
                    mysqli_stmt_bind_param($priceStmt, "iid", $showId, $class_id, $price);
                    mysqli_stmt_execute($priceStmt);
                }
            }
        }

        set_flash('success', 'Show and pricing added successfully.');
        redirect_to('admin_panel/admin_shows.php');
    }

    if ($action === 'delete_show' && validate_required($_POST, ['id'])) {
        $id = (int) $_POST['id'];

        // First delete related booking_seats (foreign key constraint)
        mysqli_query($connection, "DELETE FROM booking_seats WHERE booking_id IN (SELECT id FROM bookings WHERE show_id = $id)");

        // Then delete related bookings
        $delBookings = mysqli_prepare($connection, "DELETE FROM bookings WHERE show_id = ?");
        mysqli_stmt_bind_param($delBookings, "i", $id);
        mysqli_stmt_execute($delBookings);

        // Delete related show_class_pricing
        $delPricing = mysqli_prepare($connection, "DELETE FROM show_class_pricing WHERE show_id = ?");
        mysqli_stmt_bind_param($delPricing, "i", $id);
        mysqli_stmt_execute($delPricing);

        // Now delete the show
        $stmt = mysqli_prepare($connection, "DELETE FROM shows WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        set_flash('success', 'Show deleted.');
        redirect_to('admin_panel/admin_shows.php');
    }

    if ($action === 'update_show' && validate_required($_POST, ['id', 'movie_id', 'theater_id', 'show_date', 'show_time'])) {
        $id        = (int) $_POST['id'];
        $movieId   = (int) $_POST['movie_id'];
        $theaterId = (int) $_POST['theater_id'];
        $showDate  = $_POST['show_date'];
        $showTime  = $_POST['show_time'];

        $stmt = mysqli_prepare($connection, "UPDATE shows SET movie_id = ?, theater_id = ?, show_date = ?, show_time = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "iissi", $movieId, $theaterId, $showDate, $showTime, $id);
        mysqli_stmt_execute($stmt);

        // Update pricing for all classes
        if (isset($_POST['class_price']) && is_array($_POST['class_price'])) {
            foreach ($_POST['class_price'] as $class_id => $price) {
                $class_id = (int) $class_id;
                $price = (float) $price;
                $priceStmt = mysqli_prepare($connection, "INSERT INTO show_class_pricing (show_id, class_id, price) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE price = VALUES(price)");
                mysqli_stmt_bind_param($priceStmt, "iid", $id, $class_id, $price);
                mysqli_stmt_execute($priceStmt);
            }
        }

        set_flash('success', 'Show updated.');
        redirect_to('admin_panel/admin_shows.php');
    }

    if ($action === 'set_show_pricing' && validate_required($_POST, ['show_id'])) {
        $showId       = (int) $_POST['show_id'];
        $gold_price   = isset($_POST['gold_price'])     ? (float) $_POST['gold_price']     : 0;
        $platinum_price = isset($_POST['platinum_price']) ? (float) $_POST['platinum_price'] : 0;

        $stmt1 = mysqli_prepare($connection, "INSERT INTO show_class_pricing (show_id, class_id, price) VALUES (?, 1, ?) ON DUPLICATE KEY UPDATE price = VALUES(price)");
        mysqli_stmt_bind_param($stmt1, "id", $showId, $gold_price);
        mysqli_stmt_execute($stmt1);

        $stmt2 = mysqli_prepare($connection, "INSERT INTO show_class_pricing (show_id, class_id, price) VALUES (?, 2, ?) ON DUPLICATE KEY UPDATE price = VALUES(price)");
        mysqli_stmt_bind_param($stmt2, "id", $showId, $platinum_price);
        mysqli_stmt_execute($stmt2);

        set_flash('success', 'Show pricing updated.');
        redirect_to('admin_panel/admin_shows.php');
    }

    // -------- USER MANAGEMENT --------
    if ($action === 'toggle_user_status' && validate_required($_POST, ['id', 'status'])) {
        $id     = (int) $_POST['id'];
        $status = $_POST['status'] === 'Active' ? 'Active' : 'Inactive';
        $stmt   = mysqli_prepare($connection, "UPDATE users SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        mysqli_stmt_execute($stmt);
        set_flash('success', 'User status updated.');
        redirect_to('admin_panel/admin_users.php');
    }

    // -------- CATEGORY MANAGEMENT --------
    if ($action === 'add_category' && validate_required($_POST, ['category_name'])) {
        $catName = trim($_POST['category_name']);
        $status  = trim($_POST['status'] ?? 'Active');
        $stmt    = mysqli_prepare($connection, "INSERT INTO category (category_name, status) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $catName, $status);
        mysqli_stmt_execute($stmt);
        set_flash('success', 'Category added.');
        redirect_to('admin_panel/admin_categories.php');
    }

    if ($action === 'update_category' && validate_required($_POST, ['id', 'category_name'])) {
        $id      = (int) $_POST['id'];
        $catName = trim($_POST['category_name']);
        $status  = trim($_POST['status'] ?? 'Active');
        $stmt    = mysqli_prepare($connection, "UPDATE category SET category_name = ?, status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $catName, $status, $id);
        mysqli_stmt_execute($stmt);
        set_flash('success', 'Category updated.');
        redirect_to('admin_panel/admin_categories.php');
    }

    if ($action === 'delete_category' && validate_required($_POST, ['id'])) {
        $id = (int) $_POST['id'];
        // First delete movie_category mappings
        $delMap = mysqli_prepare($connection, "DELETE FROM movie_category WHERE cat_id = ?");
        mysqli_stmt_bind_param($delMap, "i", $id);
        mysqli_stmt_execute($delMap);
        // Then delete category
        $stmt = mysqli_prepare($connection, "DELETE FROM category WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        set_flash('success', 'Category deleted.');
        redirect_to('admin_panel/admin_categories.php');
    }

    // Inline category actions for admin_movies.php
    if ($action === 'add_category_inline' && validate_required($_POST, ['category_name'])) {
        $catName = trim($_POST['category_name']);
        $status  = 'Active';
        $stmt    = mysqli_prepare($connection, "INSERT INTO category (category_name, status) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $catName, $status);
        mysqli_stmt_execute($stmt);
        set_flash('success', 'Category added.');
        redirect_to('admin_panel/admin_movies.php');
    }

    if ($action === 'delete_category_inline' && validate_required($_POST, ['id'])) {
        $id = (int) $_POST['id'];
        // First delete movie_category mappings
        $delMap = mysqli_prepare($connection, "DELETE FROM movie_category WHERE cat_id = ?");
        mysqli_stmt_bind_param($delMap, "i", $id);
        mysqli_stmt_execute($delMap);
        // Then delete category
        $stmt = mysqli_prepare($connection, "DELETE FROM category WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        set_flash('success', 'Category deleted.');
        redirect_to('admin_panel/admin_movies.php');
    }

    // -------- CLASS MANAGEMENT --------
    if ($action === 'add_class' && validate_required($_POST, ['class_name'])) {
        $className = trim($_POST['class_name']);
        $stmt      = mysqli_prepare($connection, "INSERT INTO classes (class_name) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $className);
        mysqli_stmt_execute($stmt);
        set_flash('success', 'Class added.');
        redirect_to('admin_panel/admin_classes.php');
    }

    if ($action === 'update_class' && validate_required($_POST, ['id', 'class_name'])) {
        $id        = (int) $_POST['id'];
        $className = trim($_POST['class_name']);
        $stmt      = mysqli_prepare($connection, "UPDATE classes SET class_name = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $className, $id);
        mysqli_stmt_execute($stmt);
        set_flash('success', 'Class updated.');
        redirect_to('admin_panel/admin_classes.php');
    }

    if ($action === 'delete_class' && validate_required($_POST, ['id'])) {
        $id   = (int) $_POST['id'];
        $stmt = mysqli_prepare($connection, "DELETE FROM classes WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        set_flash('success', 'Class deleted.');
        redirect_to('admin_panel/admin_classes.php');
    }

    // -------- REVIEW MANAGEMENT --------
    if ($action === 'delete_review' && validate_required($_POST, ['id'])) {
        $id   = (int) $_POST['id'];
        $stmt = mysqli_prepare($connection, "DELETE FROM review WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        set_flash('success', 'Review deleted.');
        redirect_to('admin_panel/admin_reviews.php');
    }

    // -------- CAROUSEL MANAGEMENT --------
    if ($action === 'add_carousel' && validate_required($_POST, ['title', 'movie_id'])) {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            set_flash('danger', 'Please upload a valid image.');
            redirect_to('admin_panel/admin_carousal.php');
        }

        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            set_flash('danger', 'Only JPG, JPEG, PNG, and WEBP images are allowed.');
            redirect_to('admin_panel/admin_carousal.php');
        }

        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = uniqid('carousel_', true) . '.' . $ext;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
            set_flash('danger', 'Failed to upload carousel image.');
            redirect_to('admin_panel/admin_carousal.php');
        }

        $title        = trim($_POST['title']);
        $movieId      = isset($_POST['movie_id']) ? (int) $_POST['movie_id'] : 0;
        if ($movieId <= 0) {
            set_flash('danger', 'Please select a valid movie.');
            redirect_to('admin_panel/admin_carousal.php');
        }
        $status       = isset($_POST['status']) && (int) $_POST['status'] === 0 ? 0 : 1;
        $displayOrder = isset($_POST['display_order']) ? (int) $_POST['display_order'] : 0;
        $stmt         = mysqli_prepare($connection, "INSERT INTO carousel (movie_id, title, image, status, display_order) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issii", $movieId, $title, $filename, $status, $displayOrder);
        try {
            mysqli_stmt_execute($stmt);
        } catch (Throwable $e) {
            set_flash('danger', 'Unable to add slide. Please select an existing movie.');
            redirect_to('admin_panel/admin_carousal.php');
        }
        set_flash('success', 'Carousel slide added successfully.');
        redirect_to('admin_panel/admin_carousal.php');
    }

    if ($action === 'update_carousel' && validate_required($_POST, ['id', 'title', 'movie_id'])) {
        $id      = (int) $_POST['id'];
        $title   = trim($_POST['title']);
        $movieId = isset($_POST['movie_id']) ? (int) $_POST['movie_id'] : 0;
        if ($movieId <= 0) {
            set_flash('danger', 'Please select a valid movie.');
            redirect_to('admin_panel/admin_carousal.php');
        }
        $status       = isset($_POST['status']) && (int) $_POST['status'] === 0 ? 0 : 1;
        $displayOrder = isset($_POST['display_order']) ? (int) $_POST['display_order'] : 0;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $allowed, true)) {
                set_flash('danger', 'Only JPG, JPEG, PNG, and WEBP images are allowed.');
                redirect_to('admin_panel/admin_carousal.php');
            }

            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $oldStmt = mysqli_prepare($connection, "SELECT image FROM carousel WHERE id = ?");
            mysqli_stmt_bind_param($oldStmt, "i", $id);
            mysqli_stmt_execute($oldStmt);
            $oldRes = mysqli_stmt_get_result($oldStmt);
            $oldRow = $oldRes ? mysqli_fetch_assoc($oldRes) : null;

            $filename = uniqid('carousel_', true) . '.' . $ext;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                set_flash('danger', 'Failed to upload carousel image.');
                redirect_to('admin_panel/admin_carousal.php');
            }

            $stmt = mysqli_prepare($connection, "UPDATE carousel SET movie_id = ?, title = ?, image = ?, status = ?, display_order = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "issiii", $movieId, $title, $filename, $status, $displayOrder, $id);
            try {
                mysqli_stmt_execute($stmt);
            } catch (Throwable $e) {
                set_flash('danger', 'Unable to update slide. Please verify selected movie.');
                redirect_to('admin_panel/admin_carousal.php');
            }

            if ($oldRow && !empty($oldRow['image'])) {
                $oldPath = $uploadDir . $oldRow['image'];
                if (is_file($oldPath)) unlink($oldPath);
            }
        } else {
            try {
                $stmt = mysqli_prepare($connection, "UPDATE carousel SET movie_id = ?, title = ?, status = ?, display_order = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "isiii", $movieId, $title, $status, $displayOrder, $id);
                mysqli_stmt_execute($stmt);
            } catch (Throwable $e) {
                set_flash('danger', 'Unable to update slide. Please verify selected movie.');
                redirect_to('admin_panel/admin_carousal.php');
            }
        }

        set_flash('success', 'Carousel slide updated.');
        redirect_to('admin_panel/admin_carousal.php');
    }

    if ($action === 'delete_carousel' && validate_required($_POST, ['id'])) {
        $id      = (int) $_POST['id'];
        $oldStmt = mysqli_prepare($connection, "SELECT image FROM carousel WHERE id = ?");
        mysqli_stmt_bind_param($oldStmt, "i", $id);
        mysqli_stmt_execute($oldStmt);
        $oldRes = mysqli_stmt_get_result($oldStmt);
        $oldRow = $oldRes ? mysqli_fetch_assoc($oldRes) : null;

        $stmt = mysqli_prepare($connection, "DELETE FROM carousel WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        if ($oldRow && !empty($oldRow['image'])) {
            $oldPath = __DIR__ . '/uploads/' . $oldRow['image'];
            if (is_file($oldPath)) unlink($oldPath);
        }

        set_flash('success', 'Carousel slide deleted.');
        redirect_to('admin_panel/admin_carousal.php');
    }
}

// ============================================
// AUTH HANDLERS
// ============================================
if (isset($_POST['signupBtn'])) {
    if (!validate_required($_POST, ['username', 'email', 'phone', 'password', 'confirm_password'])) {
        echo "<script>alert('All fields are required.'); window.location.href = 'register.php';</script>";
        exit();
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email.'); window.location.href = 'register.php';</script>";
        exit();
    }
    if ($_POST['password'] !== $_POST['confirm_password']) {
        echo "<script>alert('Passwords do not match.'); window.location.href = 'register.php';</script>";
        exit();
    }

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $role     = 'user';
    $stmt     = mysqli_prepare($connection, "INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $username, $email, $phone, $password, $role);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Registration successful!'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Registration failed.'); window.location.href = 'register.php';</script>";
    }
    exit();
}

if (isset($_POST['loginBtn'])) {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $stmt     = mysqli_prepare($connection, "SELECT * FROM users WHERE email = ? AND password = ?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        if ($user['role'] === 'admin') {
            echo "<script>alert('Welcome Admin!'); window.location.href = 'admin_panel/admin_dashboard.php';</script>";
        } else {
            $redirect = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'index.php';
            unset($_SESSION['redirect_url']);
            echo "<script>alert('Login successful!'); window.location.href = '$redirect';</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password!'); window.location.href = 'login.php';</script>";
    }
    exit();
}

if (isset($_POST['forgotBtn'])) {
    $email = trim($_POST['email']);
    $stmt  = mysqli_prepare($connection, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['reset_email'] = $email;
        redirect_to('reset_password.php');
    } else {
        echo "<script>alert('Email not found!'); window.location.href = 'forgot_password.php';</script>";
        exit();
    }
}

if (isset($_POST['resetBtn'])) {
    $newPass     = trim($_POST['new_password']);
    $confirmPass = trim($_POST['confirm_new_password']);
    $email       = isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : '';
    if ($newPass !== $confirmPass || $email === '') {
        echo "<script>alert('Invalid reset request.'); window.location.href = 'reset_password.php';</script>";
        exit();
    }
    $stmt = mysqli_prepare($connection, "UPDATE users SET password = ? WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "ss", $newPass, $email);
    if (mysqli_stmt_execute($stmt)) {
        unset($_SESSION['reset_email']);
        echo "<script>alert('Password updated successfully!'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Error updating password.'); window.location.href = 'reset_password.php';</script>";
    }
    exit();
}

if (isset($_POST['submit_review'])) {
    $movie_id    = isset($_POST['movie_id'])    ? (int) $_POST['movie_id']    : 0;
    $rating      = isset($_POST['rating'])      ? (int) $_POST['rating']      : 0;
    $review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';

    if (empty($movie_id) || empty($rating) || empty($review_text)) {
        echo "<script>alert('All fields are required.'); window.location.href = 'detail.php?id=" . $movie_id . "';</script>";
        exit();
    }

    $users_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

    if ($users_id) {
        $stmt = mysqli_prepare($connection, "INSERT INTO review (users_id, movies_id, review, rating, created_at) VALUES (?, ?, ?, ?, NOW())");
        mysqli_stmt_bind_param($stmt, "iisi", $users_id, $movie_id, $review_text, $rating);
    } else {
        $stmt = mysqli_prepare($connection, "INSERT INTO review (movies_id, review, rating, created_at) VALUES (?, ?, ?, NOW())");
        mysqli_stmt_bind_param($stmt, "isi", $movie_id, $review_text, $rating);
    }

    if (mysqli_stmt_execute($stmt)) {
        $update_avg = mysqli_prepare($connection, "UPDATE movies SET rating = (SELECT AVG(rating) FROM review WHERE movies_id = ?) WHERE id = ?");
        mysqli_stmt_bind_param($update_avg, "ii", $movie_id, $movie_id);
        mysqli_stmt_execute($update_avg);
        echo "<script>alert('Review submitted successfully!'); window.location.href = 'detail.php?id=" . $movie_id . "';</script>";
    } else {
        echo "<script>alert('Failed to submit review.'); window.location.href = 'detail.php?id=" . $movie_id . "';</script>";
    }
    exit();
}

if (isset($_POST['subscribe_btn'])) {
    $email = trim($_POST['subscribe_email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Thank you for subscribing! Check your email for updates.'); window.history.back();</script>";
    } else {
        echo "<script>alert('Please enter a valid email address.'); window.history.back();</script>";
    }
    exit();
}