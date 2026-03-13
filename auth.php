<?php
require_once __DIR__ . '/db.php';

/**
 * Redirect to login page if user is not authenticated.
 */
function require_login()
{
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    try {
        $db = get_db();
        $db->exec('CREATE TABLE IF NOT EXISTS user_preferences (
            user_id INT(11) NOT NULL,
            smart_polling_enabled TINYINT(1) NOT NULL DEFAULT 1,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');

        if (!array_key_exists('smart_polling_enabled', $_SESSION)) {
            try {
                $prefStmt = $db->prepare('SELECT smart_polling_enabled FROM user_preferences WHERE user_id = ? LIMIT 1');
                $prefStmt->execute([(int)$_SESSION['user_id']]);
                $pref = $prefStmt->fetch();
                $_SESSION['smart_polling_enabled'] = ($pref && isset($pref['smart_polling_enabled'])) ? (int)$pref['smart_polling_enabled'] : 1;
            } catch (Exception $e) {
                $_SESSION['smart_polling_enabled'] = 1;
            }
        }

        $db->exec('CREATE TABLE IF NOT EXISTS user_sessions (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT(11) NOT NULL,
            session_id VARCHAR(128) NOT NULL,
            device_label VARCHAR(100) DEFAULT NULL,
            ip VARCHAR(45) DEFAULT NULL,
            user_agent VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            last_seen TIMESTAMP NULL DEFAULT NULL,
            revoked_at TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_session_id (session_id),
            KEY idx_user_last_seen (user_id, last_seen)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');

        $sid = session_id();
        $check = $db->prepare('SELECT revoked_at FROM user_sessions WHERE session_id = ? LIMIT 1');
        $check->execute([$sid]);
        $row = $check->fetch();
        if ($row && !empty($row['revoked_at'])) {
            $_SESSION = [];
            session_destroy();
            header('Location: login.php');
            exit;
        }

        $ua = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
        $touch = $db->prepare('INSERT INTO user_sessions (user_id, session_id, user_agent, last_seen)
                               VALUES (?, ?, ?, NOW())
                               ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), user_agent = VALUES(user_agent), last_seen = NOW()');
        $touch->execute([(int)$_SESSION['user_id'], $sid, $ua]);

        $_SESSION['session_id'] = $sid;
    } catch (Exception $e) {
    }
}

/**
 * Redirect if the user does not have one of the allowed roles.
 *
 * @param array $allowed_roles
 */
function require_role(array $allowed_roles)
{
    require_login();
    if (empty($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles, true)) {
        http_response_code(403);
        echo 'Access denied.';
        exit;
    }
}

/**
 * Returns the current logged in user record.
 */
function current_user()
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    static $cachedUser = null;
    if ($cachedUser !== null) {
        return $cachedUser;
    }

    $db = get_db();
    $stmt = $db->prepare('SELECT u.*, r.name AS role_name 
                          FROM users u 
                          LEFT JOIN roles r ON u.role_id = r.id 
                          WHERE u.id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $cachedUser = $stmt->fetch();

    return $cachedUser;
}

