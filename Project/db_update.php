<?php
include_once 'connection.php';
$queries = [
    "ALTER TABLE movies ADD COLUMN director VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE movies ADD COLUMN rating VARCHAR(50) DEFAULT NULL",
    "ALTER TABLE movies ADD COLUMN language VARCHAR(100) DEFAULT NULL"
];

foreach ($queries as $q) {
    if (mysqli_query($connection, $q)) {
        echo "Success: $q\n";
    } else {
        echo "Error or already exists: " . mysqli_error($connection) . "\n";
    }
}
?>
