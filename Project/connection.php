<?php
// Config file include karo (agar pehle se include na ho)
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/config.php';
}

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Character set UTF-8 set karo
mysqli_set_charset($connection, 'utf8mb4');
?>