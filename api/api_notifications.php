<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';

header('Content-Type: application/json');

try {
    require_login();
    $user = current_user();
    $role = $_SESSION['role'] ?? '';

    if ($role !== 'supplier' || empty($user['supplier_id'])) {
        echo json_encode(['success' => false, 'message' => 'Not allowed']);
        exit;
    }

    $db = get_db();
    $stmt = $db->prepare('SELECT id, title, message, link, is_read, created_at FROM notifications WHERE supplier_id = ? ORDER BY created_at DESC LIMIT 10');
    $stmt->execute([$user['supplier_id']]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmtUnread = $db->prepare('SELECT COUNT(*) FROM notifications WHERE supplier_id = ? AND is_read = 0');
    $stmtUnread->execute([$user['supplier_id']]);
    $unread = (int)$stmtUnread->fetchColumn();

    echo json_encode([
        'success' => true,
        'unread_count' => $unread,
        'notifications' => $rows,
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error loading notifications']);
}
