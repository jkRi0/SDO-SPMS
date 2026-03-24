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
    function send_supplier_email($toEmail, $subject, $htmlBody, $ccEmails = null, $bccEmails = null, $replyToEmail = null, $replyToName = null)
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
            // Note: Gmail SMTP often requires the From address to match the authenticated account (SMTP_USERNAME).
            // We set From to SMTP_FROM_EMAIL (or SMTP_USERNAME if FROM_EMAIL is empty).
            $fromEmail = !empty(SMTP_FROM_EMAIL) ? SMTP_FROM_EMAIL : SMTP_USERNAME;
            $mail->setFrom($fromEmail, SMTP_FROM_NAME);
            
            if ($replyToEmail && filter_var($replyToEmail, FILTER_VALIDATE_EMAIL)) {
                $mail->addReplyTo($replyToEmail, $replyToName ?: SMTP_FROM_NAME);
            } else {
                $mail->addReplyTo($fromEmail, SMTP_FROM_NAME);
            }
            
            $mail->addAddress($toEmail);

            // Handle CC
            $ccList = [];
            if (is_string($ccEmails)) {
                $parts = preg_split('/[\s,;]+/', $ccEmails, -1, PREG_SPLIT_NO_EMPTY);
                if (is_array($parts)) {
                    $ccList = $parts;
                }
            } elseif (is_array($ccEmails)) {
                $ccList = $ccEmails;
            }
            if (!empty($ccList)) {
                foreach ($ccList as $cc) {
                    $cc = trim((string)$cc);
                    if ($cc === '') {
                        continue;
                    }
                    if (!filter_var($cc, FILTER_VALIDATE_EMAIL)) {
                        return false;
                    }
                    $mail->addCC($cc);
                }
            }

            // Handle BCC
            $bccList = [];
            if (is_string($bccEmails)) {
                $parts = preg_split('/[\s,;]+/', $bccEmails, -1, PREG_SPLIT_NO_EMPTY);
                if (is_array($parts)) {
                    $bccList = $parts;
                }
            } elseif (is_array($bccEmails)) {
                $bccList = $bccEmails;
            }
            if (!empty($bccList)) {
                foreach ($bccList as $bcc) {
                    $bcc = trim((string)$bcc);
                    if ($bcc === '') {
                        continue;
                    }
                    if (!filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
                        return false;
                    }
                    $mail->addBCC($bcc);
                }
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('PHPMailer Error: ' . $e->getMessage());
            return false;
        }
    }
}
