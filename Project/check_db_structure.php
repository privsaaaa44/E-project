<?php
include_once "connection.php";
echo "Checking bookings table...\n";
$res = mysqli_query($connection, "DESCRIBE bookings");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error: " . mysqli_error($connection) . "\n";
}
?>
