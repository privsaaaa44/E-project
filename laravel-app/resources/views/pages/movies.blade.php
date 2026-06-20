@extends('layouts.app')

@section('title', 'Movies')

@section('content')
    <div class="hero-section text-center py-5">
        <div class="container">
            <h1 class="fw-bold">Explore Movies</h1>
            <p class="text-muted lead">Find your next favorite cinematic experience.</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center mb-5">
            <div class="col-md-8 col-lg-6">
                <form action="{{ route('movies.index') }}" method="GET" class="input-group shadow-sm rounded-pill overflow-hidden bg-white border">
                    <span class="input-group-text bg-transparent border-0 ps-4 text-muted"><i class="fa fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-0 py-3 shadow-none" placeholder="Search by title, director or category..." value="{{ $search }}">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Search</button>
                </form>
                @if($search !== '')
                    <div class="text-center mt-3">
                        <span class="text-muted small">Showing results for "{{ $search }}"</span>
                        <a href="{{ route('movies.index') }}" class="text-primary small ms-2 text-decoration-none fw-bold">Clear</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-4">
            @forelse($movies as $movie)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="movie-card">
                        <div class="position-relative">
                            <img src="{{ asset('images/'.($movie->poster ?: 'banner1.jpg')) }}" alt="{{ $movie->title }}">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-white text-primary rounded-pill px-3 py-2 shadow-sm border small fw-bold">
                                    {{ ucfirst(str_replace('_', ' ', $movie->movie_status ?? 'now_showing')) }}
                                </span>
                            </div>
                        </div>
                        <div class="movie-card-body">
                            <div class="movie-meta mb-1">{{ $movie->categories ?: 'Movie' }} - {{ $movie->duration ?? 'N/A' }}</div>
                            <h3 class="movie-title">{{ $movie->title }}</h3>
                            <div class="movie-rating"><i class="fa fa-star me-1"></i>{{ number_format((float) $movie->rating, 1) }}</div>
                            <div class="mt-auto pt-2">
                                <a href="#" class="btn-view-details mb-2">View Details</a>
                                @if(($movie->movie_status ?? '') === 'now_showing')
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
                    <i class="fa fa-film fa-4x text-muted opacity-25 mb-4"></i>
                    <h3 class="fw-bold">No movies found</h3>
                    <p class="text-muted">Try a different search term or browse our collection.</p>
                    <a href="{{ route('movies.index') }}" class="btn btn-outline-primary rounded-pill px-4">Browse All</a>
                </div>
            @endforelse
        </div>

        <div class="mt-5">
            {{ $movies->links() }}
        </div>
    </div>
@endsection
