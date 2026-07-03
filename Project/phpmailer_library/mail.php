<?php
/**
 * mail.php - PHPMailer Helper File
 * Yeh file direct use nahi hoti, contact.php use karti hai
 * Agar directly test karna ho to niche ka code use karein
 */

// Config load karo
if (!defined('MAIL_USERNAME')) {
    require_once __DIR__ . '/../config.php';
}

// PHPMailer autoload - __DIR__ se correct path
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Helper function: Email bhejne ke liye
 * @param string $to_email  - Receiver email
 * @param string $to_name   - Receiver naam
 * @param string $subject   - Subject
 * @param string $body      - HTML body
 * @return bool|string      - true on success, error message on failure
 */
function send_email($to_email, $to_name, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = MAIL_PORT;

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to_email, $to_name);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
