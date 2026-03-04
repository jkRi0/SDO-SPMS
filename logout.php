<?php
require_once __DIR__ . '/config.php';

try {
    require_once __DIR__ . '/db.php';
    if (!empty($_SESSION['user_id'])) {
        $db = get_db();
        $stmt = $db->prepare('UPDATE users SET active_session_id = NULL WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
    }
} catch (Exception $e) {
    // ignore
}

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;

