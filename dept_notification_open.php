<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/dept_notifications.php';

require_login();

$role = $_SESSION['role'] ?? '';
$allowedRoles = ['procurement', 'supply', 'accounting', 'budget', 'cashier'];
if (!in_array($role, $allowedRoles, true)) {
    http_response_code(403);
    echo 'Access denied.';
    exit;
}

$notifId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($notifId <= 0) {
    header('Location: dashboard.php');
    exit;
}

try {
    $db = get_db();

    dept_notifications_ensure_table($db);

    if (!dept_notifications_table_exists($db)) {
        header('Location: dashboard.php');
        exit;
    }

    $stmt = $db->prepare('SELECT role, link FROM department_notifications WHERE id = ? LIMIT 1');
    $stmt->execute([$notifId]);
    $notif = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$notif || ($notif['role'] ?? '') !== $role) {
        http_response_code(403);
        echo 'Access denied.';
        exit;
    }

    mark_dept_notification_read($db, $notifId);

    $target = $notif['link'] ?: 'dashboard.php';
    header('Location: ' . $target);
    exit;
} catch (Exception $e) {
    header('Location: dashboard.php');
    exit;
}
