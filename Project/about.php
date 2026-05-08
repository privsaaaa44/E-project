<?php
include_once "header.php";
?>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="fw-bold">About Cinevo</h1>
        <p class="text-muted lead">Redefining the cinematic experience since 2024.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5 align-items-center mb-5">
        <div class="col-lg-6">
            <div class="movie-card shadow-lg border-0 rounded-4 overflow-hidden">
                <img src="images/image.png" class="w-100" alt="About Cinevo">
            </div>
        </div>
        <div class="col-lg-6">
            <span class="text-primary fw-bold small text-uppercase">OUR STORY</span>
            <h2 class="display-5 fw-bold mb-4 mt-2">Passion for Great Cinema</h2>
            <p class="lead text-muted mb-4">
                Cinevo started with a simple mission: to make movie booking seamless, fast, and enjoyable. We believe that the magic of cinema starts the moment you decide to watch a film.
            </p>
            <p class="text-muted mb-4">
                Our platform brings together the best theaters, the latest blockbusters, and an intuitive interface to help you find and book your favorite shows in seconds. From blockbuster hits to indie gems, we've got it all.
            </p>
            <div class="row g-4 mt-2">
                <div class="col-6">
                    <h4 class="fw-bold text-primary mb-0">100+</h4>
                    <span class="small text-muted">Partner Theaters</span>
                </div>
                <div class="col-6">
                    <h4 class="fw-bold text-primary mb-0">1M+</h4>
                    <span class="small text-muted">Happy Moviegoers</span>
                </div>
            </div>
        </div>
    </div>

    <div class="section-padding py-5">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100 rounded-4">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px;">
                        <i class="fa fa-ticket fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Easy Booking</h5>
                    <p class="text-muted small mb-0">Book your tickets in just a few clicks with our streamlined checkout process.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100 rounded-4">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px;">
                        <i class="fa fa-couch fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Best Seats</h5>
                    <p class="text-muted small mb-0">Choose from a wide variety of seat classes including Gold, Platinum, and Box Office.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100 rounded-4">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px;">
                        <i class="fa fa-star fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Real Reviews</h5>
                    <p class="text-muted small mb-0">Read authentic reviews from fellow moviegoers before you book your show.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-primary-subtle { background-color: #dbeafe !important; }
</style>

<?php include_once "footer.php"; ?>