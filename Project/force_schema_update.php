<?php
include_once 'connection.php';
echo "Ensuring bookings table has has_kids column...\n";
$q = "ALTER TABLE bookings ADD COLUMN has_kids TINYINT(1) NOT NULL DEFAULT 0";
if (mysqli_query($connection, $q)) {
    echo "Column added successfully.\n";
} else {
    echo "Error (it might already exist): " . mysqli_error($connection) . "\n";
}

echo "Ensuring shows table has show_date and show_time...\n";
// Sometimes these might be missing if SQL was not imported correctly
$q2 = "ALTER TABLE shows ADD COLUMN show_date DATE NULL";
mysqli_query($connection, $q2);
$q3 = "ALTER TABLE shows ADD COLUMN show_time TIME NULL";
mysqli_query($connection, $q3);

echo "Done.\n";
?>
