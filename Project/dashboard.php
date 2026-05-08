<?php
include_once 'header.php';

// Access control: only admin can view this page
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<div class="container-xl p_3">
    <div class="row">
        <div class="col-md-12">
            <h1 class="font_50">Admin Dashboard</h1>
            <p class="lead">Welcome, <?php echo $_SESSION['user_name']; ?>! You have successfully logged in as an Admin.</p>
            <hr>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card p-4 bg-light shadow_box">
                        <h3>Total Users</h3>
                        <p class="fs-2 col_oran fw-bold">150</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 bg-light shadow_box">
                        <h3>Total Movies</h3>
                        <p class="fs-2 col_oran fw-bold">45</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 bg-light shadow_box">
                        <h3>Pending Tickets</h3>
                        <p class="fs-2 col_oran fw-bold">12</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
