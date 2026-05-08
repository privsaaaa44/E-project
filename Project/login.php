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
                        <h2 class="fw-bold text-main">Welcome Back</h2>
                        <p class="text-muted">Please enter your details to sign in.</p>
                    </div>
                    
                    <form action="code.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">EMAIL ADDRESS</label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-bold text-muted m-0">PASSWORD</label>
                                <a href="forgot_password.php" class="small text-primary text-decoration-none">Forgot password?</a>
                            </div>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label text-muted small" for="remember">Remember me for 30 days</label>
                        </div>
                        
                        <input type="hidden" name="loginBtn" value="1">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            Sign In
                        </button>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <p class="text-muted small mb-0">Don't have an account? <a href="register.php" class="text-primary fw-bold text-decoration-none">Create an account</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>