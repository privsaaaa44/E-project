@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="auth-shell">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-main">Welcome Back</h2>
                            <p class="text-muted">Please enter your details to sign in.</p>
                        </div>

                        <form action="{{ route('login.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">EMAIL ADDRESS</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="name@example.com" required autofocus>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small fw-bold text-muted m-0">PASSWORD</label>
                                    <a href="{{ route('password.request') }}" class="small text-primary text-decoration-none">Forgot password?</a>
                                </div>
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>

                            <div class="form-check mb-4">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                                <label class="form-check-label text-muted small" for="remember">Remember me for 30 days</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">Sign In</button>
                        </form>

                        <div class="mt-4 text-center">
                            <p class="text-muted small mb-0">Don't have an account? <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Create an account</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
