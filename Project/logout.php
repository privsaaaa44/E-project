<?php
session_start();
session_destroy();

// Hosting-safe redirect - relative URL ke bajaye absolute URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
header("Location: " . $protocol . "://" . $host . $dir . "/login.php");
exit();
?>
