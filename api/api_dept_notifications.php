<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../dept_notifications.php';

header('Content-Type: application/json');

try {
    require_login();
    $role = $_SESSION['role'] ?? '';

    $allowedRoles = ['procurement', 'supply', 'accounting', 'budget', 'cashier'];
    if (!in_array($role, $allowedRoles, true)) {
        echo json_encode(['success' => false, 'message' => 'Not allowed']);
        exit;
    }

    $db = get_db();

    dept_notifications_ensure_table($db);

    if (!dept_notifications_table_exists($db)) {
        echo json_encode(['success' => true, 'unread_count' => 0, 'notifications' => []]);
        exit;
    }

    $rows = fetch_dept_notifications($db, $role, 10);

    $stmtUnread = $db->prepare('SELECT COUNT(*) FROM department_notifications WHERE role = ? AND is_read = 0');
    $stmtUnread->execute([(string)$role]);
    $unread = (int)$stmtUnread->fetchColumn();

    echo json_encode([
        'success' => true,
        'unread_count' => $unread,
        'notifications' => $rows,
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error loading notifications']);
}
