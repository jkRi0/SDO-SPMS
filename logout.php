<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/audit.php';

try {
    require_once __DIR__ . '/db.php';
    if (!empty($_SESSION['user_id'])) {
        $db = get_db();

        create_log($db, $_SESSION['user_id'], 'logout', 'user', $_SESSION['user_id'], 'User logged out');

        $stmt = $db->prepare('UPDATE users SET active_session_id = NULL, active_session_last_seen = NULL WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
    }
} catch (Exception $e) {
    // ignore
}

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;

