<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/audit.php';

require_login();
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied.');
}

$db = get_db();
$filters = [];
if (!empty($_GET['user_id'])) $filters['user_id'] = (int)$_GET['user_id'];
if (!empty($_GET['action'])) $filters['action'] = $_GET['action'];
if (!empty($_GET['from'])) $filters['from'] = $_GET['from'];
if (!empty($_GET['to'])) $filters['to'] = $_GET['to'];

$logs = fetch_logs($db, $filters);

foreach ($logs as $l): ?>
<tr>
    <td><?php echo htmlspecialchars($l['created_at']); ?></td>
    <td><?php echo htmlspecialchars($l['username'] ?? 'System'); ?></td>
    <td><?php echo htmlspecialchars($l['target_type'] . ' #' . $l['target_id']); ?></td>
</tr>
<?php endforeach; ?>
