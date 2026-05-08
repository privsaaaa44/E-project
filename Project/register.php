<?php
include_once 'connection.php';
include_once 'header.php';
?>

<div class="section-padding" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-main">Join Cinevo</h2>
                        <p class="text-muted">Create an account to start booking movies.</p>
                    </div>
                    
                    <form action="code.php" method="POST" id="registrationForm">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">FULL NAME</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="John Doe" required minlength="3" maxlength="50" pattern="[A-Za-z\s]+">
                            <div class="form-text text-muted">Letters only (3-50 characters)</div>
                            <div class="invalid-feedback">Please enter a valid name (letters only, 3-50 characters).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">EMAIL ADDRESS</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" required>
                            <div class="invalid-feedback">Please enter a valid email.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">PHONE NUMBER</label>
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="+92 300 1234567" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">PASSWORD</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                                <div class="invalid-feedback">At least 6 characters.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">CONFIRM</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••" required>
                                <div class="invalid-feedback">Passwords must match.</div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label text-muted small" for="terms">I agree to the <a href="#" class="text-primary text-decoration-none">Terms & Conditions</a></label>
                        </div>
                        
                        <input type="hidden" name="signupBtn" value="1">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" id="signupBtn">
                            Create Account
                        </button>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <p class="text-muted small mb-0">Already have an account? <a href="login.php" class="text-primary fw-bold text-decoration-none">Sign In</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#registrationForm').on('submit', function(e) {
        var isValid = true;
        
        var username = $('#username').val().trim();
        var nameRegex = /^[A-Za-z\s]+$/;
        if (username.length < 3 || username.length > 50 || !nameRegex.test(username)) {
            $('#username').addClass('is-invalid');
            isValid = false;
        } else {
            $('#username').removeClass('is-invalid');
        }

        var email = $('#email').val().trim();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            $('#email').addClass('is-invalid');
            isValid = false;
        } else {
            $('#email').removeClass('is-invalid');
        }

        var password = $('#password').val();
        var confirm = $('#confirm_password').val();
        if (password.length < 6) {
            $('#password').addClass('is-invalid');
            isValid = false;
        } else {
            $('#password').removeClass('is-invalid');
        }

        if (password !== confirm) {
            $('#confirm_password').addClass('is-invalid');
            isValid = false;
        } else {
            $('#confirm_password').removeClass('is-invalid');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>

<?php include_once 'footer.php'; ?>