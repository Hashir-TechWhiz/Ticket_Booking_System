<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require '../vendor/autoload.php';

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); 
$dotenv->load();

function sendEmail($toEmail, $toName, $status, $adminMessage)
{
    $mail = new PHPMailer(true);
    try {
        // SMTP settings using environment variables
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD']; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email content
        $mail->setFrom($_ENV['SMTP_USERNAME'], 'Go Sri Lanka');
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);

        // Set subject and message
        $subject = "Trip Request " . ucfirst($status);
        $message = "<p>Dear $toName,</p>";
        $message .= "<p>Your trip request has been <b>" . ucfirst($status) . "</b>.</p>";
        $message .= "<p>" . nl2br($adminMessage) . "</p>";
        $message .= "<p>Thank you for choosing Go Sri Lanka!</p>";

        $mail->Subject = $subject;
        $mail->Body    = $message;

        // Send email
        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}