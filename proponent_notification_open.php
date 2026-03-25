<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/proponent_notifications.php';

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
    $stmt = $db->prepare('SELECT proponent_id, link FROM proponent_notifications WHERE id = ? LIMIT 1');
    $stmt->execute([$notifId]);
    $notif = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$notif) {
        header('Location: dashboard.php');
        exit;
    }

    // Only the owning proponent (or non-proponent roles) can mark/read this notification
    if ($role === 'proponent') {
        if (empty($user['proponent_id']) || (int)$user['proponent_id'] !== (int)$notif['proponent_id']) {
            http_response_code(403);
            echo 'Access denied.';
            exit;
        }
    }

    // Mark as read
    mark_proponent_notification_read($db, $notifId);

    $target = $notif['link'] ?: 'dashboard.php';
    header('Location: ' . $target);
    exit;
} catch (Exception $e) {
    header('Location: dashboard.php');
    exit;
}
