<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();

$db = get_db();
$role = $_SESSION['role'] ?? '';

// If admin opens feedback page, mark all feedback as read
if ($role === 'admin') {
    try {
        $db->exec('UPDATE feedback SET is_read = 1 WHERE is_read = 0');
    } catch (Exception $e) {
        // Ignore marking errors; just proceed to display
    }
}

// Load feedback entries (most recent first)
$stmt = $db->query('SELECT f.*, u.username FROM feedback f LEFT JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC');
$feedbacks = $stmt->fetchAll();

include __DIR__ . '/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h3 class="mb-0">Feedback</h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">List of submitted bugs, suggestions, and comments.</p>
        </div>
    </div>

    <div class="card table-wrapper">
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover align-middle table-striped">
                <thead class="table-light">
                <tr>
                    <th>Created</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Type</th>
                    <th>Message</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$feedbacks): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No feedback submitted yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($feedbacks as $f): ?>
                        <tr class="<?php echo !empty($f['is_read']) ? '' : 'notif-unread'; ?>">
                            <td><?php echo htmlspecialchars($f['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($f['username'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($f['role'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($f['type']); ?></td>
                            <td style="max-width: 480px; white-space: pre-wrap;">
                                <?php echo nl2br(htmlspecialchars($f['message'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
