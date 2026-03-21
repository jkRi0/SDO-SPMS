<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/dept_notifications.php';

require_login();

$role = $_SESSION['role'] ?? '';
$allowedRoles = ['procurement', 'supply', 'accounting', 'budget', 'cashier'];
if (!in_array($role, $allowedRoles, true)) {
    header('Location: dashboard.php');
    exit;
}

$db = get_db();
dept_notifications_ensure_table($db);

$notifications = [];
if (dept_notifications_table_exists($db)) {
    $notifications = fetch_dept_notifications($db, $role, 50);
}

$pageTitle = 'All Notifications';
include __DIR__ . '/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>All Notifications</h3>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if (empty($notifications)): ?>
        <div class="alert alert-info">No notifications found.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notifications as $n): ?>
                                <tr class="<?php echo empty($n['is_read']) ? 'table-primary' : ''; ?>">
                                    <td>
                                        <?php if (!empty($n['link'])): ?>
                                            <a href="<?php echo htmlspecialchars($n['link']); ?>" class="text-decoration-none fw-semibold">
                                                <?php echo htmlspecialchars($n['title']); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($n['title']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo nl2br(htmlspecialchars($n['message'])); ?></td>
                                    <td><?php echo date('M j, Y H:i', strtotime($n['created_at'])); ?></td>
                                    <td>
                                        <?php if (empty($n['is_read'])): ?>
                                            <span class="badge bg-primary">Unread</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Read</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (empty($n['is_read'])): ?>
                                            <a href="dept_notification_open.php?id=<?php echo (int)$n['id']; ?>" class="btn btn-sm btn-outline-primary">Mark as Read</a>
                                        <?php else: ?>
                                            <span class="text-muted">Already read</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
