<?php
if (PHP_SAPI !== 'cli') {
    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($remoteAddr, ['127.0.0.1', '::1'], true)) {
        http_response_code(403);
        exit('Forbidden');
    }
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

// Test without requiring login - for debugging
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set a test session
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin@deped.gov';
$_SESSION['role'] = 'procurement';
$_SESSION['supplier_id'] = null;

// Now include the dashboard
include __DIR__ . '/../dashboard.php';
?>
