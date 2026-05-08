<?php
require_once '../code.php';
require_admin();

$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Dashboard';

switch ($current_page) {
    case 'admin_theaters.php': $page_title = 'Manage Theaters'; break;
    case 'admin_movies.php': $page_title = 'Manage Movies'; break;
    case 'admin_shows.php': $page_title = 'Manage Shows'; break;
    case 'admin_users.php': $page_title = 'Users'; break;
    case 'admin_reports.php': $page_title = 'Reports'; break;
    case 'admin_categories.php': $page_title = 'Categories'; break;
    case 'admin_classes.php': $page_title = 'Seat Classes'; break;
    case 'admin_reviews.php': $page_title = 'Reviews'; break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Cinevo</title>
    <!-- Fonts & Libraries (Local for Offline) -->
    <link href="../css/lib/inter-font.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/lib/fontawesome.all.min.css">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/lib/select2.min.css" rel="stylesheet" />
    <link href="admin_modern.css" rel="stylesheet">
    <script src="../js/lib/chart.js"></script>
</head>
<body>
    <nav id="sidebar">
        <div class="sidebar-header">
            <i class="fa fa-film me-2"></i> Cinevo Admin
        </div>
        <ul class="list-unstyled components">
            <li class="<?php echo ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>">
                <a href="admin_dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
            </li>
            <li class="<?php echo ($current_page == 'admin_theaters.php') ? 'active' : ''; ?>">
                <a href="admin_theaters.php"><i class="fas fa-building"></i> Theaters</a>
            </li>
            <li class="<?php echo ($current_page == 'admin_movies.php') ? 'active' : ''; ?>">
                <a href="admin_movies.php"><i class="fas fa-film"></i> Movies</a>
            </li>
            <li class="<?php echo ($current_page == 'admin_categories.php') ? 'active' : ''; ?>">
                <a href="admin_categories.php"><i class="fas fa-tags"></i> Categories</a>
            </li>
            <li class="<?php echo ($current_page == 'admin_shows.php') ? 'active' : ''; ?>">
                <a href="admin_shows.php"><i class="fas fa-calendar-alt"></i> Shows</a>
            </li>
            <li class="<?php echo ($current_page == 'admin_classes.php') ? 'active' : ''; ?>">
                <a href="admin_classes.php"><i class="fas fa-chair"></i> Seat Classes</a>
            </li>
            <li class="<?php echo ($current_page == 'admin_users.php') ? 'active' : ''; ?>">
                <a href="admin_users.php"><i class="fas fa-users"></i> Users</a>
            </li>
            <li class="<?php echo ($current_page == 'admin_reports.php') ? 'active' : ''; ?>">
                <a href="admin_reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
            </li>
            <hr class="mx-3 opacity-10">
            <li class="mt-2">
                <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a>
            </li>
        </ul>
    </nav>

    <div id="page-container">
        <header class="top-header">
            <h5 class="m-0 fw-bold"><?php echo $page_title; ?></h5>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <div class="fw-bold small"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                    <div class="text-muted small">Administrator</div>
                </div>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle text-decoration-none" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:38px;height:38px;font-weight:bold;"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-3">
                        <li><a class="dropdown-item py-2" href="../logout.php"><i class="fas fa-sign-out-alt me-2 text-danger"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main id="content">
            <?php $flash = get_flash(); ?>
            <?php if ($flash): ?>
                <div class="alert alert-<?php echo ($flash['type'] === 'success' || $flash['type'] === 'info') ? 'success' : 'danger'; ?> border-0 shadow-sm mb-4">
                    <?php echo htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
