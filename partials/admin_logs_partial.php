<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../audit.php';

require_login();
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied.');
}

$db = get_db();
$filters = [];
if (!empty($_GET['role'])) $filters['role'] = $_GET['role'];
if (!empty($_GET['date'])) $filters['date'] = $_GET['date'];

$filters['actions'] = ['login', 'logout', 'login_failed'];

$logs = fetch_logs($db, $filters);

foreach ($logs as $l): ?>
<tr>
    <td><?php echo htmlspecialchars($l['created_at']); ?></td>
    <td>
        <?php
        $logUsername = $l['username'] ?? 'System';
        if (($l['action'] ?? '') === 'login_failed' && empty($l['username']) && !empty($l['details'])) {
            $decoded = json_decode($l['details'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded['attempted_username'])) {
                $logUsername = (string)$decoded['attempted_username'];
            }
        }
        echo htmlspecialchars($logUsername);
        ?>
    </td>
    <td><?php echo htmlspecialchars($l['action']); ?></td>
    <td><?php echo htmlspecialchars(format_log_details($l['action'], $l['details'] ?? null)); ?></td>
</tr>
<?php endforeach; ?>
