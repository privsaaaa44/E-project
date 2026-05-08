<?php
include_once "header.php";
include "connection.php";

$id = $_GET['id'];

$query = "SELECT * FROM movies WHERE id = $id";
$result = mysqli_query($connection, $query);
$row = mysqli_fetch_assoc($result);
?>

<div class="container mt-5">

  <!-- MOVIE TITLE -->
  <h1 class="mb-3"><?php echo $row['title']; ?></h1>

  <!-- POSTER -->
  <img src="img/<?php echo $row['poster']; ?>" class="img-fluid mb-3" style="border-radius:10px;">

  <!-- DESCRIPTION -->
  <p>
    <?php echo $row['movie_desc']; ?>
  </p>

  <!-- DURATION -->
  <p><b>Duration:</b> <?php echo $row['duration']; ?></p>

  <!-- TRAILER -->
  <div class="mt-4">
    <h4>Watch Trailer</h4>

    <iframe width="100%" height="400"
      src="<?php echo $row['trailer_link']; ?>"
      frameborder="0"
      allowfullscreen>
    </iframe>
  </div>

  <!-- GET TICKET BUTTON -->
  <a href="booking.php?movie_id=<?php echo $row['id']; ?>" class="btn btn-warning mt-4">
    Get Ticket
  </a>

</div>

<?php include_once "footer.php"; ?>