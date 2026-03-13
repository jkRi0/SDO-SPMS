<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();

$user = current_user();
$role = $_SESSION['role'] ?? '';

if ($role !== 'supplier' || empty($user['supplier_id'])) {
    http_response_code(403);
    echo 'Access denied.';
    exit;
}

$db = get_db();
$rows = [];
try {
    $stmt = $db->prepare('SELECT id, title, message, link, is_read, created_at FROM notifications WHERE supplier_id = ? ORDER BY created_at DESC LIMIT 500');
    $stmt->execute([$user['supplier_id']]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $rows = [];
}

$pageTitle = 'All Notifications';
include __DIR__ . '/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Notifications</h2>
    <p class="page-subtitle">All notifications</p>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!$rows): ?>
            <div class="text-muted">No notifications found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-compact align-middle">
                    <thead>
                        <tr>
                            <th style="width: 1%;">Status</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th style="width: 1%;">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $n): ?>
                            <tr>
                                <td>
                                    <?php if (empty($n['is_read'])): ?>
                                        <span class="badge bg-danger">Unread</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-semibold">
                                    <a class="text-decoration-none" href="<?php echo !empty($n['link']) ? 'notification_open.php?id=' . (int)$n['id'] : '#'; ?>">
                                        <?php echo htmlspecialchars((string)($n['title'] ?? '')); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars((string)($n['message'] ?? '')); ?></td>
                                <td class="text-muted small" style="white-space: nowrap;">
                                    <?php echo htmlspecialchars(date('m/d/Y H:i', strtotime((string)($n['created_at'] ?? '')))); ?>
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
