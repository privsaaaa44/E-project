<?php
include_once 'header.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}
?>

<div class="section-padding" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-main">New Password</h2>
                        <p class="text-muted small">Setting a password for <strong><?php echo htmlspecialchars($_SESSION['reset_email']); ?></strong></p>
                    </div>
                    
                    <form action="code.php" method="POST" id="resetForm">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">NEW PASSWORD</label>
                            <input type="password" name="new_password" id="new_password" class="form-control py-2" placeholder="••••••••" required>
                            <div class="invalid-feedback">At least 6 characters.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">CONFIRM PASSWORD</label>
                            <input type="password" name="confirm_new_password" id="confirm_new_password" class="form-control py-2" placeholder="••••••••" required>
                            <div class="invalid-feedback">Passwords must match.</div>
                        </div>
                        
                        <input type="hidden" name="resetBtn" value="1">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" id="resetBtn">
                            Update Password
                        </button>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <a href="login.php" class="text-primary small text-decoration-none fw-bold">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#resetForm').on('submit', function(e) {
        var pass = $('#new_password').val();
        var confirm = $('#confirm_new_password').val();
        var isValid = true;

        if (pass.length < 6) {
            $('#new_password').addClass('is-invalid');
            isValid = false;
        } else {
            $('#new_password').removeClass('is-invalid');
        }

        if (pass !== confirm) {
            $('#confirm_new_password').addClass('is-invalid');
            isValid = false;
        } else {
            $('#confirm_new_password').removeClass('is-invalid');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>

<?php include_once 'footer.php'; ?>
