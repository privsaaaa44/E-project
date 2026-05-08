<?php
include_once 'connection.php';
include_once 'header.php';
?>

<div class="section-padding" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-main">Reset Password</h2>
                        <p class="text-muted" id="stepDescription">Enter your email to reset your password.</p>
                    </div>
                    
                    <!-- Step 1: Email Verification -->
                    <div id="step1">
                        <form action="code.php" method="POST" id="emailForm">
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">EMAIL ADDRESS</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" required>
                            </div>
                            
                            <input type="hidden" name="checkEmailBtn" value="1">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" id="checkEmailBtn">
                                Check Email
                            </button>
                        </form>
                    </div>

                    <!-- Step 2: Password Reset (Hidden by default) -->
                    <div id="step2" style="display: none;">
                        <form action="code.php" method="POST" id="resetForm">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">CONFIRMED EMAIL</label>
                                <input type="email" id="confirmedEmail" name="email" class="form-control bg-light" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">NEW PASSWORD</label>
                                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="••••••••" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">CONFIRM PASSWORD</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••" required>
                            </div>
                            
                            <input type="hidden" name="forgotBtn" value="1">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" id="forgotBtn">
                                Reset Password
                            </button>
                        </form>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <p class="text-muted small mb-0">Remembered your password? <a href="login.php" class="text-primary fw-bold text-decoration-none">Sign In</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#emailForm').on('submit', function (e) {
        var email = $('#email').val().trim();
        if (email === "") {
            e.preventDefault();
            return false;
        }
        
        var btn = $('#checkEmailBtn');
        btn.html('<i class="fa fa-spinner fa-spin me-2"></i>Checking...');
        btn.prop('disabled', true);
        
        // For demonstration/compatibility, showing step 2 after a small delay
        // In actual production this would be handled by server-side code or AJAX
        setTimeout(function() {
            $('#step1').fadeOut(300, function() {
                $('#confirmedEmail').val(email);
                $('#step2').fadeIn(300);
                $('#stepDescription').text('Enter your new password below');
            });
            btn.html('Check Email');
            btn.prop('disabled', false);
        }, 800);
        
        e.preventDefault(); 
    });

    $('#resetForm').on('submit', function (e) {
        var pass = $('#new_password').val();
        var confirm = $('#confirm_password').val();
        
        if (pass.length < 6) {
            alert("Password must be at least 6 characters.");
            e.preventDefault();
            return false;
        }
        
        if (pass !== confirm) {
            alert("Passwords do not match.");
            e.preventDefault();
            return false;
        }
    });
});
</script>

<?php include_once 'footer.php'; ?>
