@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="auth-shell">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-main">Join Cinevo</h2>
                            <p class="text-muted">Create an account to start booking movies.</p>
                        </div>

                        <form action="{{ route('register.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">FULL NAME</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="John Doe" required minlength="3" maxlength="50" pattern="[A-Za-z\s]+">
                                <div class="form-text text-muted">Letters only, 3-50 characters</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">EMAIL ADDRESS</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="name@example.com" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">PHONE NUMBER</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+92 300 1234567" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted">PASSWORD</label>
                                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                                    <div class="form-text text-muted">At least 8 characters with letters and numbers</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted">CONFIRM</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Password" required>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input type="checkbox" class="form-check-input" id="terms" name="terms" value="1" required>
                                <label class="form-check-label text-muted small" for="terms">I agree to the Terms & Conditions</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">Create Account</button>
                        </form>

                        <div class="mt-4 text-center">
                            <p class="text-muted small mb-0">Already have an account? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign In</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
