<?php
include_once "connection.php";

echo "<h2>Database Debugger</h2>";

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$table = "bookings";
$result = mysqli_query($connection, "SHOW COLUMNS FROM $table");

if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $val) echo "<td>$val</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error showing columns: " . mysqli_error($connection);
}

// Also check shows table
echo "<h3>Shows Table</h3>";
$res2 = mysqli_query($connection, "SELECT COUNT(*) as total FROM shows");
$row2 = mysqli_fetch_assoc($res2);
echo "Total shows in DB: " . ($row2['total'] ?? 0);
?>
