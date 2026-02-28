<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();

$user = current_user();
$role = $_SESSION['role'] ?? '';

$notifId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($notifId <= 0) {
    header('Location: dashboard.php');
    exit;
}

try {
    $db = get_db();
    $stmt = $db->prepare('SELECT supplier_id, link FROM notifications WHERE id = ? LIMIT 1');
    $stmt->execute([$notifId]);
    $notif = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$notif) {
        header('Location: dashboard.php');
        exit;
    }

    // Only the owning supplier (or non-supplier roles) can mark/read this notification
    if ($role === 'supplier') {
        if (empty($user['supplier_id']) || (int)$user['supplier_id'] !== (int)$notif['supplier_id']) {
            http_response_code(403);
            echo 'Access denied.';
            exit;
        }
    }

    // Mark as read
    $upd = $db->prepare('UPDATE notifications SET is_read = 1 WHERE id = ?');
    $upd->execute([$notifId]);

    $target = $notif['link'] ?: 'dashboard.php';
    header('Location: ' . $target);
    exit;
} catch (Exception $e) {
    header('Location: dashboard.php');
    exit;
}
