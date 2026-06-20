@extends('layouts.app')

@section('title', 'Cinevo')

@section('content')
    <section class="hero-section py-5 overflow-hidden position-relative">
        <div class="container position-relative">
            <div class="row align-items-center py-5">
                <div class="col-lg-6">
                    <span class="badge bg-primary-subtle text-primary mb-3 px-3 py-2 rounded-pill fw-bold">NEW RELEASES NOW SHOWING</span>
                    <h1 class="display-3 fw-bold mb-3 text-main">Experience Cinema Like Never Before</h1>
                    <p class="lead text-muted mb-4 fs-4">Book tickets for the latest blockbusters in just a few clicks. Minimal, fast, and secure booking for your favorite theaters.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('movies.index') }}" class="btn btn-primary px-5 py-3 fw-bold shadow">Browse Movies</a>
                        <a href="{{ route('about') }}" class="btn btn-outline-primary px-5 py-3 fw-bold">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="{{ asset('images/banner1.jpg') }}" class="img-fluid rounded-4 shadow" alt="Cinema Experience">
                </div>
            </div>
        </div>
    </section>

    @if($featuredMovies->isNotEmpty())
        <section class="py-5 bg-white border-bottom">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0"><i class="fa fa-star text-warning me-2"></i> Featured Blockbusters</h4>
                    <a href="{{ route('movies.index') }}" class="text-primary text-decoration-none small fw-bold">View All <i class="fa fa-arrow-right ms-1"></i></a>
                </div>
                <div class="row g-4">
                    @foreach($featuredMovies as $movie)
                        <div class="col-md-3">
                            <div class="movie-card">
                                <img src="{{ asset('images/'.($movie->poster ?: 'banner1.jpg')) }}" alt="{{ $movie->title }}">
                                <div class="movie-card-body">
                                    <div class="movie-meta">{{ $movie->categories ?: 'Movie' }}</div>
                                    <h3 class="movie-title">{{ $movie->title }}</h3>
                                    <div class="movie-rating"><i class="fa fa-star me-1"></i>{{ number_format((float) $movie->rating, 1) }}</div>
                                    <a href="#" class="btn-view-details mt-auto">View Details</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section-padding">
        <div class="container">
            <div class="text-center mb-4">
                <span class="text-primary fw-bold text-uppercase small">Now Playing</span>
                <h2 class="fw-bold display-6">Explore Latest Movies</h2>
            </div>

            <div class="row g-4">
                @forelse($movies as $movie)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="movie-card">
                            <div class="position-relative">
                                <img src="{{ asset('images/'.($movie->poster ?: 'banner1.jpg')) }}" alt="{{ $movie->title }}">
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-white text-primary rounded-pill px-3 py-2 shadow-sm border">{{ ucfirst(str_replace('_', ' ', $movie->movie_status)) }}</span>
                                </div>
                            </div>
                            <div class="movie-card-body">
                                <div class="movie-meta">{{ $movie->categories ?: 'Movie' }} - {{ $movie->duration }}</div>
                                <h3 class="movie-title">{{ $movie->title }}</h3>
                                <div class="movie-rating"><i class="fa fa-star me-1"></i>{{ number_format((float) $movie->rating, 1) }}</div>
                                <div class="mt-auto pt-2">
                                    <a href="#" class="btn-view-details mb-2">View Details</a>
                                    @if($movie->movie_status === 'now_showing')
                                        <a href="{{ route('bookings.index', ['movie_id' => $movie->id]) }}" class="btn btn-primary w-100 py-2">Book Now</a>
                                    @else
                                        <button class="btn btn-secondary w-100 py-2 opacity-50" disabled>Coming Soon</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No movies found at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
