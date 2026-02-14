<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();

$user = current_user();
$db = get_db();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($current === '' || $new === '' || $confirm === '') {
        $errors[] = 'Please fill in all fields.';
    } elseif ($new !== $confirm) {
        $errors[] = 'New password and confirmation do not match.';
    } else {
        try {
            $stmt = $db->prepare('SELECT password_hash FROM users WHERE id = ?');
            $stmt->execute([$user['id']]);
            $row = $stmt->fetch();

            if (!$row || !password_verify($current, $row['password_hash'])) {
                $errors[] = 'Current password is incorrect.';
            } else {
                $newHash = password_hash($new, PASSWORD_DEFAULT);
                $stmt = $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
                $stmt->execute([$newHash, $user['id']]);
                $success = 'Your password has been updated successfully.';
            }
        } catch (Exception $e) {
            $errors[] = 'Error updating password: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/header.php';
?>

<div class="container mt-4" style="max-width: 480px;">
    <h3 class="mb-3">Change Password</h3>

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
            <label for="current_password" class="form-label">Current Password</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Password</button>
        <a href="dashboard.php" class="btn btn-link">Back to Dashboard</a>
    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
