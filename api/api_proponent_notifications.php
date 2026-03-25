<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../proponent_notifications.php';

header('Content-Type: application/json');

try {
    require_login();
    $role = $_SESSION['role'] ?? '';
    $proponentId = $_SESSION['proponent_id'] ?? null;

    if ($role !== 'proponent' || empty($proponentId)) {
        echo json_encode(['success' => false, 'message' => 'Not allowed']);
        exit;
    }

    $db = get_db();

    proponent_notifications_ensure_table($db);

    $rows = fetch_proponent_notifications($db, $proponentId, 10);

    $stmtUnread = $db->prepare('SELECT COUNT(*) FROM proponent_notifications WHERE proponent_id = ? AND is_read = 0');
    $stmtUnread->execute([(int)$proponentId]);
    $unread = (int)$stmtUnread->fetchColumn();

    echo json_encode([
        'success' => true,
        'unread_count' => $unread,
        'notifications' => $rows,
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error loading notifications']);
}
