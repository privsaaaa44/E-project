<footer class="mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <a class="navbar-brand text-primary fw-bold fs-3 mb-3 d-block" href="{{ route('home') }}">
                    <i class="fa fa-film me-2"></i> Cinevo
                </a>
                <p class="text-muted">Experience the best movies in the highest quality. Book your tickets easily and enjoy the show.</p>
                <div class="d-flex gap-3 mt-4">
                    <a href="#" class="text-muted"><i class="fa fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-muted"><i class="fa fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-muted"><i class="fa fa-instagram fa-lg"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 mb-4">
                <h6 class="fw-bold mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('home') }}" class="text-muted text-decoration-none py-1 d-block">Home</a></li>
                    <li><a href="{{ route('movies.index') }}" class="text-muted text-decoration-none py-1 d-block">Movies</a></li>
                    <li><a href="{{ route('bookings.index') }}" class="text-muted text-decoration-none py-1 d-block">Bookings</a></li>
                    <li><a href="{{ route('about') }}" class="text-muted text-decoration-none py-1 d-block">About Us</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-4 mb-4">
                <h6 class="fw-bold mb-3">Movie Genres</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('movies.index', ['search' => 'Action']) }}" class="text-muted text-decoration-none py-1 d-block">Action</a></li>
                    <li><a href="{{ route('movies.index', ['search' => 'Comedy']) }}" class="text-muted text-decoration-none py-1 d-block">Comedy</a></li>
                    <li><a href="{{ route('movies.index', ['search' => 'Drama']) }}" class="text-muted text-decoration-none py-1 d-block">Drama</a></li>
                    <li><a href="{{ route('movies.index', ['search' => 'Thriller']) }}" class="text-muted text-decoration-none py-1 d-block">Thriller</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-12 mb-4">
                <h6 class="fw-bold mb-3 text-main">Subscribe to Newsletter</h6>
                <p class="text-muted small">Get latest movie updates and exclusive offers directly in your inbox.</p>
                <form action="#" method="POST" class="mt-3">
                    @csrf
                    <div class="input-group mb-2 shadow-sm rounded-pill overflow-hidden bg-white border">
                        <input type="email" name="subscribe_email" class="form-control border-0 py-2 ps-4 shadow-none" placeholder="Enter your email" required>
                        <button class="btn btn-primary px-4 fw-bold" type="submit">Subscribe</button>
                    </div>
                    <p class="text-muted small mt-2"><i class="fa fa-info-circle me-1"></i> We respect your privacy.</p>
                </form>
            </div>
        </div>
        <hr class="my-4 opacity-50">
        <div class="text-center text-muted">
            <p class="mb-0">&copy; {{ date('Y') }} Cinevo. All rights reserved.</p>
        </div>
    </div>
</footer>

<div class="modal fade" id="templateVideoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title">Watch Trailer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ratio ratio-16x9">
                    <iframe id="youtubeVideo" src="" title="YouTube video player" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var templateVideoModal = document.getElementById('templateVideoModal');
    if (templateVideoModal) {
        templateVideoModal.addEventListener('hide.bs.modal', function () {
            var iframe = document.getElementById('youtubeVideo');
            iframe.src = '';
        });
    }

    function playTrailer(url) {
        var iframe = document.getElementById('youtubeVideo');
        var embedUrl = url.replace("watch?v=", "embed/");
        iframe.src = embedUrl + "?autoplay=1";
        var modal = new bootstrap.Modal(document.getElementById('templateVideoModal'));
        modal.show();
    }
</script>
