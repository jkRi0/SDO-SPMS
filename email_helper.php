<?php
require_once __DIR__ . '/config.php';

// Try Composer autoload first
$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
} else {
    // Fallback to manual PHPMailer includes if available
    $phpMailerBase = __DIR__ . '/phpmailer/src';
    if (file_exists($phpMailerBase . '/PHPMailer.php')) {
        require_once $phpMailerBase . '/PHPMailer.php';
        require_once $phpMailerBase . '/SMTP.php';
        require_once $phpMailerBase . '/Exception.php';
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!function_exists('send_supplier_email')) {
    function send_supplier_email($toEmail, $subject, $htmlBody)
    {
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!class_exists(PHPMailer::class)) {
            // PHPMailer not available
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            // Recipients
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($toEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Optionally log error
            // error_log('Mail error: ' . $e->getMessage());
            return false;
        }
    }
}
