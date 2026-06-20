@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
    <div class="auth-shell">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-main">Reset Password</h2>
                            <p class="text-muted">Enter your email to reset your password.</p>
                        </div>

                        <form action="{{ route('password.email') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">EMAIL ADDRESS</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="name@example.com" required autofocus>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">Check Email</button>
                        </form>

                        <div class="mt-4 text-center">
                            <p class="text-muted small mb-0">Remembered your password? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign In</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
