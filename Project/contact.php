<?php
include_once "header.php";
include_once "connection.php";

// Config file load karo (agar pehle se nahi hua)
if (!defined('MAIL_USERNAME')) {
    require_once __DIR__ . '/config.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$message_sent = false;
$error_message = '';

if (isset($_POST['contact_submit'])) {
    // __DIR__ se autoload.php ka sahi path
    $autoload = __DIR__ . '/phpmailer_library/vendor/autoload.php';
    
    if (!file_exists($autoload)) {
        $error_message = 'PHPMailer library not found. Please contact the administrator.';
    } else {
        require $autoload;

        $name    = htmlspecialchars(trim($_POST['name']));
        $email   = htmlspecialchars(trim($_POST['email']));
        $phone   = htmlspecialchars(trim($_POST['phone'] ?? ''));
        $subject = htmlspecialchars(trim($_POST['subject']));
        $message = htmlspecialchars(trim($_POST['message']));

        $mail = new PHPMailer(true);

        try {
            // Server settings - config.php se
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = MAIL_PORT;

            // 1. Admin ko mail bhejo
            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME . ' Contact Form');
            $mail->addAddress(MAIL_TO_ADMIN, 'Admin');
            $mail->addReplyTo($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'New Contact: ' . $subject;
            $mail->Body    = "
                <h3>New Contact Message - Cinevo</h3>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Phone:</strong> {$phone}</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <p><strong>Message:</strong><br>" . nl2br($message) . "</p>
            ";
            $mail->send();

            // 2. User ko auto-reply
            $mail->clearAddresses();
            $mail->clearReplyTos();
            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME . ' Support');
            $mail->addAddress($email, $name);
            $mail->Subject = 'Thank you for contacting us - Cinevo';
            $mail->Body    = 'Hi ' . $name . ',<br><br>Welcome to <b>Cinevo</b>! Thank you for getting in touch. We have received your message and will get back to you shortly.<br><br>Best regards,<br>Cinevo Team';
            $mail->send();

            $message_sent = true;
        } catch (Exception $e) {
            $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>

<div class="hero-section text-center py-5 bg-light">
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
                
                <?php if ($message_sent): ?>
                    <div class="alert alert-success">Your message has been sent successfully! We will get back to you soon.</div>
                <?php elseif ($error_message): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <form action="contact.php" method="POST">
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
                            <button type="submit" name="contact_submit" class="btn btn-primary px-5 py-3 fw-bold shadow-sm rounded-pill">
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

<?php include_once "footer.php"; ?>
