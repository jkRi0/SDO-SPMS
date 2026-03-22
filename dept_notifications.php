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

function create_dept_notification_once(PDO $db, $role, $transaction_id, $title, $message, $link = null, $dedupeWindowSeconds = 120, $fromDept = null)
{
    try {
        dept_notifications_ensure_table($db);
        $stmtCheck = $db->prepare('SELECT id FROM department_notifications 
                                   WHERE role = ? AND transaction_id = ? AND title = ? 
                                   AND created_at >= (NOW() - INTERVAL ? SECOND)
                                   LIMIT 1');
        $stmtCheck->execute([(string)$role, (int)$transaction_id, (string)$title, (int)$dedupeWindowSeconds]);
        if ($stmtCheck->fetch()) {
            return true;
        }

        $stmt = $db->prepare('INSERT INTO department_notifications (role, transaction_id, title, message, link, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([(string)$role, (int)$transaction_id, (string)$title, (string)$message, (string)$link]);

        // Email Notification for Department
        $userStmt = $db->prepare('
            SELECT u.email 
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE r.name = ? 
              AND u.email IS NOT NULL 
              AND u.email != "" 
            LIMIT 1
        ');
        $userStmt->execute([(string)$role]);
        $deptEmail = $userStmt->fetchColumn();

        if ($deptEmail) {
            $emailSubject = $title;
            $emailBody = '<p>' . htmlspecialchars($message) . '</p>';
            if ($link) {
                $emailBody .= '<p><a href="' . htmlspecialchars(BASE_URL . $link) . '">View details in STMS Portal</a></p>';
            }
            
            $replyToEmail = null;
            $replyToName = null;
            if ($fromDept) {
                $fromUserStmt = $db->prepare('
                    SELECT u.email 
                    FROM users u
                    JOIN roles r ON u.role_id = r.id
                    WHERE r.name = ? 
                      AND u.email IS NOT NULL 
                      AND u.email != "" 
                    LIMIT 1
                ');
                $fromUserStmt->execute([(string)$fromDept]);
                $replyToEmail = $fromUserStmt->fetchColumn();
                if ($replyToEmail) {
                    $replyToName = ucwords($fromDept) . ' Unit';
                }
            }
            
            send_supplier_email($deptEmail, $emailSubject, $emailBody, null, $replyToEmail, $replyToName);
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
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

function create_dept_notification(PDO $db, $role, $transaction_id, $title, $message, $link = null, $fromDept = null)
{
    try {
        dept_notifications_ensure_table($db);
        $stmt = $db->prepare('INSERT INTO department_notifications (role, transaction_id, title, message, link, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([(string)$role, (int)$transaction_id, (string)$title, (string)$message, (string)$link]);

        // Email Notification for Department
        $userStmt = $db->prepare('
            SELECT u.email 
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE r.name = ? 
              AND u.email IS NOT NULL 
              AND u.email != "" 
            LIMIT 1
        ');
        $userStmt->execute([(string)$role]);
        $deptEmail = $userStmt->fetchColumn();

        if ($deptEmail) {
            $emailSubject = $title;
            $emailBody = '<p>' . htmlspecialchars($message) . '</p>';
            if ($link) {
                $emailBody .= '<p><a href="' . htmlspecialchars(BASE_URL . $link) . '">View details in STMS Portal</a></p>';
            }

            $replyToEmail = null;
            $replyToName = null;
            if ($fromDept) {
                $fromUserStmt = $db->prepare('
                    SELECT u.email 
                    FROM users u
                    JOIN roles r ON u.role_id = r.id
                    WHERE r.name = ? 
                      AND u.email IS NOT NULL 
                      AND u.email != "" 
                    LIMIT 1
                ');
                $fromUserStmt->execute([(string)$fromDept]);
                $replyToEmail = $fromUserStmt->fetchColumn();
                if ($replyToEmail) {
                    $replyToName = ucwords($fromDept) . ' Unit';
                }
            }

            send_supplier_email($deptEmail, $emailSubject, $emailBody, null, $replyToEmail, $replyToName);
        }

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
