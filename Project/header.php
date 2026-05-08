<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cinevo - Online Movie Booking</title>
    <!-- Fonts (Local for Offline) -->
    <link href="css/lib/inter-font.css" rel="stylesheet">
    <!-- Frameworks (Local for Offline) -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <!-- Custom Modern Theme -->
    <link href="css/modern.css" rel="stylesheet">
    <!-- Libraries (Local for Offline) -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 (Local for Offline) -->
    <script src="js/lib/sweetalert2.all.min.js"></script>
</head>

<body>

    <?php $page = basename($_SERVER['PHP_SELF']); ?>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="fa fa-film me-2"></i> Cinevo
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'index.php') ? 'active' : ''; ?>"
                            href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'movies.php') ? 'active' : ''; ?>"
                            href="movies.php">Movies</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'bookings.php') ? 'active' : ''; ?>"
                            href="bookings.php">Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'about.php') ? 'active' : ''; ?>"
                            href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'contact.php') ? 'active' : ''; ?>"
                            href="contact.php">Contact</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <?php if (isset($_SESSION['user_name'])): ?>
                        <div class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" id="userDropdown"
                                role="button" data-bs-toggle="dropdown">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px;">
                                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                                </div>
                                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-2">
                                <li><a class="dropdown-item py-2" href="my_bookings.php"><i
                                            class="fa fa-ticket me-2 text-success"></i> My Bookings</a></li>
                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                                    <li>
                                        <hr class="dropdown-divider opacity-50">
                                    </li>
                                    <li><a class="dropdown-item py-2" href="admin_panel/admin_dashboard.php"><i
                                                class="fa fa-th-large me-2 text-primary"></i> Admin Panel</a></li>
                                <?php endif; ?>
                                <li>
                                    <hr class="dropdown-divider opacity-50">
                                </li>
                                <li><a class="dropdown-item py-2 text-danger" href="logout.php"><i
                                            class="fa fa-sign-out me-2"></i> Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary px-4">Login</a>
                        <a href="register.php" class="btn btn-primary px-4">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>