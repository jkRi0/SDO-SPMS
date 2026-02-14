<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

header('Content-Type: application/json');

require_login();

$db = get_db();
$user = current_user();
$role = $_SESSION['role'] ?? null;

try {
    $type = trim($_POST['type'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($type === '' || $message === '') {
        echo json_encode([
            'success' => false,
            'message' => 'Please select a type and enter a message.'
        ]);
        exit;
    }

    $stmt = $db->prepare('INSERT INTO feedback (user_id, role, type, message, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([
        $user['id'] ?? null,
        $role,
        $type,
        $message
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Feedback saved successfully.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error saving feedback: ' . $e->getMessage()
    ]);
}
