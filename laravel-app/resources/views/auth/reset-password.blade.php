@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
    <div class="auth-shell">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-main">New Password</h2>
                            <p class="text-muted small">Setting a password for <strong>{{ $email }}</strong></p>
                        </div>

                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">NEW PASSWORD</label>
                                <input type="password" name="password" class="form-control py-2" placeholder="Password" required>
                                <div class="form-text text-muted">At least 8 characters with letters and numbers</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">CONFIRM PASSWORD</label>
                                <input type="password" name="password_confirmation" class="form-control py-2" placeholder="Password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">Update Password</button>
                        </form>

                        <div class="mt-4 text-center">
                            <a href="{{ route('login') }}" class="text-primary small text-decoration-none fw-bold">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
