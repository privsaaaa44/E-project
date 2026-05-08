<?php
include_once "../header.php";
include_once "../connection.php";
?>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="fw-bold">Contact Us</h1>
        <p class="text-muted lead">We'd love to hear from you. Send us a message.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <!-- Contact Form -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm p-4 p-lg-5 rounded-4">
                <h3 class="fw-bold mb-4">Send a Message</h3>
                <form action="mail.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">YOUR NAME</label>
                            <input type="text" name="name" class="form-control py-2" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">EMAIL ADDRESS</label>
                            <input type="email" name="email" class="form-control py-2" placeholder="john@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">PHONE NUMBER</label>
                            <input type="text" name="phone" class="form-control py-2" placeholder="+1 (234) 567-890">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">SUBJECT</label>
                            <input type="text" name="subject" class="form-control py-2" placeholder="Inquiry about..." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">MESSAGE</label>
                            <textarea name="message" class="form-control py-2" rows="5" placeholder="How can we help you?" required></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" name="contact_submitcontact_submit" class="btn btn-primary px-5 py-3 fw-bold shadow-sm rounded-pill">
                                Send Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="col-lg-5">
            <div class="d-flex flex-column gap-4">
                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-map-marker"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Our Location</h6>
                            <p class="text-muted small mb-0">68 Road Brooklyn Street, New York, USA</p>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Email Us</h6>
                            <p class="text-muted small mb-0">support@cinevo.com<br>info@cinevo.com</p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-phone"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Call Us</h6>
                            <p class="text-muted small mb-0">+(000) 345 67 89<br>+(000) 987 65 43</p>
                        </div>
                    </div>
                </div>

                <!-- Google Map Placeholder -->
                <div class="rounded-4 overflow-hidden shadow-sm border" style="height: 250px;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d114964.53925916665!2d-80.29949920266738!3d25.782390733064336!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88d9b0a20ec8c111%3A0xff96f271ddad4f65!2sMiami%2C+FL%2C+USA!5e0!3m2!1sen!2sin!4v1530774403788" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-primary-subtle { background-color: #dbeafe !important; }
</style>

<?php include_once "../footer.php"; ?>