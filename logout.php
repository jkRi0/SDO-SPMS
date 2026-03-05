<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/audit.php';

try {
    require_once __DIR__ . '/db.php';
    if (!empty($_SESSION['user_id'])) {
        $db = get_db();

        create_log($db, $_SESSION['user_id'], 'logout', 'user', $_SESSION['user_id'], 'User logged out');

        try {
            $sid = session_id();
            if (!empty($sid)) {
                $stmt = $db->prepare('UPDATE user_sessions SET revoked_at = NOW() WHERE session_id = ?');
                $stmt->execute([$sid]);
            }
        } catch (Exception $e) {
        }
    }
} catch (Exception $e) {
    // ignore
}

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;

