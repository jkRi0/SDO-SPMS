<?php

function proponent_notifications_ensure_table(PDO $db)
{
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS proponent_notifications (\n"
            . "  id INT(11) NOT NULL AUTO_INCREMENT,\n"
            . "  proponent_id INT(11) NOT NULL,\n"
            . "  transaction_id INT(11) DEFAULT NULL,\n"
            . "  title VARCHAR(255) NOT NULL,\n"
            . "  message TEXT NOT NULL,\n"
            . "  link VARCHAR(255) DEFAULT NULL,\n"
            . "  is_read TINYINT(1) NOT NULL DEFAULT 0,\n"
            . "  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n"
            . "  PRIMARY KEY (id),\n"
            . "  KEY idx_proponent_created (proponent_id, created_at)\n"
            . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function create_proponent_notification_once(PDO $db, $proponent_id, $transaction_id, $title, $message, $link = null, $dedupeWindowSeconds = 120, $fromDept = null)
{
    try {
        proponent_notifications_ensure_table($db);
        $stmtCheck = $db->prepare('SELECT id FROM proponent_notifications 
                                   WHERE proponent_id = ? AND transaction_id = ? AND title = ? 
                                   AND created_at >= (NOW() - INTERVAL ? SECOND)
                                   LIMIT 1');
        $stmtCheck->execute([(int)$proponent_id, (int)$transaction_id, (string)$title, (int)$dedupeWindowSeconds]);
        if ($stmtCheck->fetch()) {
            return true;
        }

        $stmt = $db->prepare('INSERT INTO proponent_notifications (proponent_id, transaction_id, title, message, link, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([(int)$proponent_id, (int)$transaction_id, (string)$title, (string)$message, (string)$link]);

        // Email Notification for Proponent
        $userStmt = $db->prepare('
            SELECT u.email 
            FROM users u
            WHERE u.proponent_id = ? 
              AND u.email IS NOT NULL 
              AND u.email != "" 
            LIMIT 1
        ');
        $userStmt->execute([(int)$proponent_id]);
        $proponentEmail = $userStmt->fetchColumn();

        if ($proponentEmail) {
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
            
            send_supplier_email($proponentEmail, $emailSubject, $emailBody, null, $replyToEmail, $replyToName);
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function create_proponent_notification(PDO $db, $proponent_id, $transaction_id, $title, $message, $link = null, $fromDept = null)
{
    try {
        proponent_notifications_ensure_table($db);
        $stmt = $db->prepare('INSERT INTO proponent_notifications (proponent_id, transaction_id, title, message, link, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([(int)$proponent_id, (int)$transaction_id, (string)$title, (string)$message, (string)$link]);

        // Email Notification for Proponent
        $userStmt = $db->prepare('
            SELECT u.email 
            FROM users u
            WHERE u.proponent_id = ? 
              AND u.email IS NOT NULL 
              AND u.email != "" 
            LIMIT 1
        ');
        $userStmt->execute([(int)$proponent_id]);
        $proponentEmail = $userStmt->fetchColumn();

        if ($proponentEmail) {
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

            send_supplier_email($proponentEmail, $emailSubject, $emailBody, null, $replyToEmail, $replyToName);
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function fetch_proponent_notifications(PDO $db, $proponent_id, $limit = 10)
{
    $limit = (int)$limit;
    if ($limit <= 0) $limit = 10;
    if ($limit > 50) $limit = 50;

    $stmt = $db->prepare('SELECT id, title, message, link, is_read, created_at FROM proponent_notifications WHERE proponent_id = ? ORDER BY created_at DESC LIMIT ' . $limit);
    $stmt->execute([(int)$proponent_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function mark_proponent_notification_read(PDO $db, $id)
{
    $stmt = $db->prepare('UPDATE proponent_notifications SET is_read = 1 WHERE id = ?');
    $stmt->execute([(int)$id]);
}
