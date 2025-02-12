<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require '../vendor/autoload.php';

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); 
$dotenv->load();

function sendEmail($toEmail, $toName, $status, $adminMessage = "", $price = null, $contactNumber = null)
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
        $mail->setFrom($_ENV['SMTP_USERNAME'], 'Your Company');
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);

        // Set subject and message
        $subject = "Trip Request " . ucfirst($status);
        $message = "<p>Dear $toName,</p>";

        if ($status == 'Approved') {
            $message .= "<p>Your trip request has been <b>Approved</b>!</p>
                        <p>Price: <b>Rs. $price</b></p>
                        <p>Contact Number: <b>$contactNumber</b></p>";
        } else {
            $message .= "<p>Unfortunately, your trip request has been <b>Rejected</b>.</p>
                        <p>Reason: <b>$adminMessage</b></p>";
        }

        $message .= "<p>Thank you for using our service.</p>";

        $mail->Subject = $subject;
        $mail->Body    = $message;

        // Send email
        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
