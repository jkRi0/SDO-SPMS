<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/audit.php';

require_login();

$user = current_user();
$db = get_db();

$errors = [];
$success = '';

$smartPollingEnabled = 1;
try {
    $db->exec('CREATE TABLE IF NOT EXISTS user_preferences (
        user_id INT(11) NOT NULL,
        smart_polling_enabled TINYINT(1) NOT NULL DEFAULT 1,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');

    $prefStmt = $db->prepare('SELECT smart_polling_enabled FROM user_preferences WHERE user_id = ? LIMIT 1');
    $prefStmt->execute([(int)($user['id'] ?? 0)]);
    $pref = $prefStmt->fetch();
    $smartPollingEnabled = ($pref && isset($pref['smart_polling_enabled'])) ? (int)$pref['smart_polling_enabled'] : 1;
} catch (Exception $e) {
    $smartPollingEnabled = 1;
}

$sessions = [];
try {
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
} catch (Exception $e) {
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'set_smart_polling') {
        $enabled = !empty($_POST['smart_polling_enabled']) ? 1 : 0;
        try {
            $stmt = $db->prepare('INSERT INTO user_preferences (user_id, smart_polling_enabled) VALUES (?, ?) ON DUPLICATE KEY UPDATE smart_polling_enabled = VALUES(smart_polling_enabled)');
            $stmt->execute([(int)($user['id'] ?? 0), $enabled]);
            $smartPollingEnabled = $enabled;
            $_SESSION['smart_polling_enabled'] = $enabled;
            $success = 'Smart Polling preference updated.';
        } catch (Exception $e) {
            $errors[] = 'Error updating Smart Polling preference.';
        }
    } elseif ($action === 'revoke_session') {
        $sid = (string)($_POST['session_id'] ?? '');
        if ($sid !== '' && !empty($user['id'])) {
            try {
                $stmt = $db->prepare('UPDATE user_sessions SET revoked_at = NOW() WHERE user_id = ? AND session_id = ?');
                $stmt->execute([$user['id'], $sid]);

                if (!empty($_SESSION['session_id']) && hash_equals((string)$_SESSION['session_id'], $sid)) {
                    $_SESSION = [];
                    session_destroy();
                    header('Location: login.php');
                    exit;
                }

                $success = 'Session removed.';
            } catch (Exception $e) {
                $errors[] = 'Error removing session.';
            }
        }
    } elseif ($action === 'delete_session') {
        $sid = (string)($_POST['session_id'] ?? '');
        if ($sid !== '' && !empty($user['id'])) {
            try {
                $stmt = $db->prepare('DELETE FROM user_sessions WHERE user_id = ? AND session_id = ?');
                $stmt->execute([$user['id'], $sid]);

                if (!empty($_SESSION['session_id']) && hash_equals((string)$_SESSION['session_id'], $sid)) {
                    $_SESSION = [];
                    session_destroy();
                    header('Location: login.php');
                    exit;
                }

                $success = 'Session deleted.';
            } catch (Exception $e) {
                $errors[] = 'Error deleting session.';
            }
        }
    } else {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $oldUsername = $user['username'] ?? '';

    if ($username === '' || $current === '') {
        $errors[] = 'Please fill in all required fields.';
    } else {
        $new = (string)$new;
        $confirm = (string)$confirm;

        if (($new !== '' || $confirm !== '') && $new !== $confirm) {
            $errors[] = 'New password and confirmation do not match.';
        }
    }

    if (!$errors) {
        try {
            $stmt = $db->prepare('SELECT password_hash FROM users WHERE id = ?');
            $stmt->execute([$user['id']]);
            $row = $stmt->fetch();

            if (!$row || !password_verify($current, $row['password_hash'])) {
                $errors[] = 'Current password is incorrect.';
            } else {
                $check = $db->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
                $check->execute([$username, $user['id']]);
                if ($check->fetch()) {
                    $errors[] = 'Username is already taken.';
                } else {
                    if ($new !== '') {
                        $newHash = password_hash($new, PASSWORD_DEFAULT);
                        $update = $db->prepare('UPDATE users SET username = ?, password_hash = ? WHERE id = ?');
                        $update->execute([$username, $newHash, $user['id']]);
                    } else {
                        $update = $db->prepare('UPDATE users SET username = ? WHERE id = ?');
                        $update->execute([$username, $user['id']]);
                    }

                    $pwChanged = ($new !== '');
                    if ($oldUsername !== $username || $pwChanged) {
                        $details = json_encode([
                            'old_username' => $oldUsername,
                            'new_username' => $username,
                            'password_changed' => $pwChanged,
                        ]);
                        create_log($db, $user['id'], 'update_account', 'user', $user['id'], $details);
                    }

                    $_SESSION['username'] = $username;
                    $success = 'Your account details have been updated successfully.';
                }
            }
        } catch (Exception $e) {
            $errors[] = 'Error updating account: ' . $e->getMessage();
        }
    }
    }
}

try {
    $stmtSessions = $db->prepare('SELECT session_id, device_label, user_agent, created_at, last_seen, revoked_at
                                  FROM user_sessions
                                  WHERE user_id = ?
                                  ORDER BY (revoked_at IS NULL) DESC, last_seen DESC, created_at DESC
                                  LIMIT 20');
    $stmtSessions->execute([$user['id']]);
    $sessions = $stmtSessions->fetchAll();
} catch (Exception $e) {
    $sessions = [];
}

include __DIR__ . '/header.php';
?>

<div class="container mt-4" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h3 class="mb-0">Account Settings</h3>
        <a class="btn btn-outline-secondary btn-sm" href="dashboard.php">Back</a>
    </div>

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-start gap-3" style="flex-wrap: wrap;">
            <div>
                <div class="fw-semibold">Smart Polling</div>
                <div class="text-muted small">When enabled, auto-refresh (semi-realtime) runs only while this tab is visible and this window is focused. Pros: reduces CPU/bandwidth usage and avoids background refresh. Cons: when you switch tabs/minimize, updates pause and may only appear when you return.</div>
            </div>
            <form method="post" class="m-0">
                <input type="hidden" name="action" value="set_smart_polling">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="smartPollingSwitch" name="smart_polling_enabled" value="1" <?php echo $smartPollingEnabled ? 'checked' : ''; ?> onchange="this.form.submit();">
                    <label class="form-check-label" for="smartPollingSwitch"></label>
                </div>
            </form>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="new_password" name="new_password">
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
        </div>
        <button type="submit" class="btn btn-primary">Update Account</button>
        <a href="dashboard.php" class="btn btn-link">Back to Dashboard</a>
    </form>
    <br><br>
    <div class="mt-4">
        <h5 class="mb-2">Devices / Sessions</h5>

        <?php if (!$sessions): ?>
            <div class="text-muted small">No sessions found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-striped align-middle">
                    <thead>
                    <tr>
                        <th>Device</th>
                        <th>Last Seen</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($sessions as $s): ?>
                        <?php
                        $sid = (string)($s['session_id'] ?? '');
                        $isCurrent = !empty($_SESSION['session_id']) && hash_equals((string)$_SESSION['session_id'], $sid);
                        $ua = (string)($s['user_agent'] ?? '');
                        $device = (string)($s['device_label'] ?? '');
                        if ($device === '') {
                            $device = 'Unknown device';

                            if ($ua !== '') {
                                $browser = 'Browser';
                                if (stripos($ua, 'Edg/') !== false) {
                                    $browser = 'Edge';
                                } elseif (stripos($ua, 'Chrome/') !== false && stripos($ua, 'Chromium') === false) {
                                    $browser = 'Chrome';
                                } elseif (stripos($ua, 'Firefox/') !== false) {
                                    $browser = 'Firefox';
                                } elseif (stripos($ua, 'Safari/') !== false && stripos($ua, 'Chrome/') === false) {
                                    $browser = 'Safari';
                                }

                                $os = 'Unknown OS';
                                if (stripos($ua, 'Windows') !== false) {
                                    $os = 'Windows';
                                } elseif (stripos($ua, 'Android') !== false) {
                                    $os = 'Android';
                                } elseif (stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false) {
                                    $os = 'iOS';
                                } elseif (stripos($ua, 'Mac OS X') !== false) {
                                    $os = 'macOS';
                                } elseif (stripos($ua, 'Linux') !== false) {
                                    $os = 'Linux';
                                }

                                $device = $browser . ' on ' . $os;
                            }
                        }
                        $revoked = !empty($s['revoked_at']);
                        $deviceShort = mb_strlen($device) > 44 ? (mb_substr($device, 0, 41) . '...') : $device;
                        ?>
                        <tr>
                            <td>
                                <div class="fw-semibold" title="<?php echo htmlspecialchars($ua); ?>"><?php echo htmlspecialchars($deviceShort); ?></div>
                                <div class="text-muted small" style="max-width: 520px;">
                                    <span class="font-monospace"><?php echo htmlspecialchars($sid !== '' ? substr($sid, 0, 10) . '…' : ''); ?></span>
                                </div>
                                <?php if ($isCurrent): ?>
                                    <span class="badge bg-primary ms-1">This device</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars((string)($s['last_seen'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars((string)($s['created_at'] ?? '')); ?></td>
                            <td>
                                <?php if ($revoked): ?>
                                    <span class="badge bg-secondary">Logged out</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if (!$revoked): ?>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="action" value="revoke_session">
                                        <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($sid); ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Log out</button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this session entry?');">
                                        <input type="hidden" name="action" value="delete_session">
                                        <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($sid); ?>">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
