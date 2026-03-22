<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';

require_login();

header('Content-Type: application/json');

try {
    $db = get_db();
    $role = $_SESSION['role'] ?? '';

    echo json_encode([
        'success' => true,
        'active' => 0,
        'pending' => 0,
        'approved' => 0,
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load dashboard stats.',
    ]);
}
