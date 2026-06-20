<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cinevo - Online Movie Booking')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/modern.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .auth-shell { min-height: 80vh; display: flex; align-items: center; }
        .bg-gradient-dark { background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(15,23,42,.82) 100%); }
    </style>
</head>
<body>
    @include('partials.navigation')

    <main>
        @include('partials.flash')
        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
