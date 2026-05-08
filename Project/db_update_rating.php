<?php
include_once 'connection.php';
mysqli_query($connection, "UPDATE movies SET rating = average_rating WHERE average_rating > 0");
mysqli_query($connection, "ALTER TABLE movies DROP COLUMN average_rating");
echo "Done! Average rating removed and rating updated.";
?>
