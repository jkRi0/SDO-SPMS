<?php

function dept_notifications_ensure_table(PDO $db)
{
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS department_notifications (\n"
            . "  id INT(11) NOT NULL AUTO_INCREMENT,\n"
            . "  role VARCHAR(50) NOT NULL,\n"
            . "  transaction_id INT(11) DEFAULT NULL,\n"
            . "  title VARCHAR(255) NOT NULL,\n"
            . "  message TEXT NOT NULL,\n"
            . "  link VARCHAR(255) DEFAULT NULL,\n"
            . "  is_read TINYINT(1) NOT NULL DEFAULT 0,\n"
            . "  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n"
            . "  PRIMARY KEY (id),\n"
            . "  KEY idx_role_created (role, created_at)\n"
            . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function create_dept_notification_once(PDO $db, $role, $transaction_id, $title, $message, $link = null, $dedupeWindowSeconds = 120)
{
    if (empty($role)) {
        return false;
    }

    $dedupeWindowSeconds = (int)$dedupeWindowSeconds;
    if ($dedupeWindowSeconds <= 0) {
        $dedupeWindowSeconds = 120;
    }

    try {
        $checkStmt = $db->prepare('SELECT id FROM department_notifications WHERE role = ? AND transaction_id <=> ? AND title = ? AND message = ? AND created_at >= (NOW() - INTERVAL ? SECOND) LIMIT 1');
        $checkStmt->execute([
            (string)$role,
            $transaction_id !== null ? (int)$transaction_id : null,
            (string)$title,
            (string)$message,
            $dedupeWindowSeconds,
        ]);
        $exists = $checkStmt->fetch(PDO::FETCH_ASSOC);
        if ($exists) {
            return true;
        }
    } catch (Exception $e) {
    }

    return create_dept_notification($db, $role, $transaction_id, $title, $message, $link);
}

function dept_notifications_table_exists(PDO $db)
{
    try {
        dept_notifications_ensure_table($db);
        $stmt = $db->query("SHOW TABLES LIKE 'department_notifications'");
        $row = $stmt ? $stmt->fetch(PDO::FETCH_NUM) : false;
        return !empty($row);
    } catch (Exception $e) {
        return false;
    }
}

function create_dept_notification(PDO $db, $role, $transaction_id, $title, $message, $link = null)
{
    if (empty($role)) {
        return false;
    }

    try {
        $stmt = $db->prepare('INSERT INTO department_notifications (role, transaction_id, title, message, link, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $role,
            $transaction_id !== null ? (int)$transaction_id : null,
            (string)$title,
            (string)$message,
            $link,
        ]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function fetch_dept_notifications(PDO $db, $role, $limit = 10)
{
    $limit = (int)$limit;
    if ($limit <= 0) $limit = 10;
    if ($limit > 50) $limit = 50;

    $stmt = $db->prepare('SELECT id, title, message, link, is_read, created_at FROM department_notifications WHERE role = ? ORDER BY created_at DESC LIMIT ' . $limit);
    $stmt->execute([(string)$role]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function mark_dept_notification_read(PDO $db, $id)
{
    $stmt = $db->prepare('UPDATE department_notifications SET is_read = 1 WHERE id = ?');
    $stmt->execute([(int)$id]);
}
