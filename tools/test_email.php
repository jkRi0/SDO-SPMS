<?php
if (PHP_SAPI !== 'cli') {
    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($remoteAddr, ['127.0.0.1', '::1'], true)) {
        http_response_code(403);
        exit('Forbidden');
    }
}

require_once __DIR__ . '/../email_helper.php';

// CHANGE THIS to the Gmail address you want to test with
$testRecipient = 'alphanum0001@gmail.com'; // or set a specific email string

$ok = send_supplier_email(
    $testRecipient,
    'Test email from SDO-STMS',
    '<p>This is a <strong>test email</strong> from SDO-STMS using PHPMailer + Gmail SMTP.</p>'
);

header('Content-Type: text/plain; charset=utf-8');

echo "send_supplier_email returned: ";
var_dump($ok);

echo "\n\nIf true, check the inbox (and Spam/Promotions) for: " . $testRecipient . "\n";
