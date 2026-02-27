<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Simple one-time admin setup. Run only when initializing the system.
$db = get_db();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $message = 'Please provide username and password.';
    } else {
        // Ensure admin role exists
        $stmt = $db->prepare('SELECT id FROM roles WHERE name = ?');
        $stmt->execute(['admin']);
        $role = $stmt->fetch();
        if (!$role) {
            $db->prepare('INSERT INTO roles (name) VALUES (?)')->execute(['admin']);
            $stmt->execute(['admin']);
            $role = $stmt->fetch();
        }
        $role_id = $role['id'] ?? null;

        // Check if admin user exists
        $stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $existing = $stmt->fetch();
        if ($existing) {
            $message = 'A user with that username already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO users (username, password_hash, role_id) VALUES (?, ?, ?)');
            $stmt->execute([$username, $hash, $role_id]);
            $message = 'Admin user created. You can now login.';
        }
    }
}

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Setup - STMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f5f7fa; display:flex; align-items:center; justify-content:center; min-height:100vh;">
    <div class="card" style="width:520px; border-radius:12px;">
        <div class="card-body">
            <h5 class="card-title">Admin Setup</h5>
            <p class="text-muted">Create an initial admin user to manage the system.</p>
            <?php if ($message): ?>
                <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    <button class="btn btn-primary" type="submit">Create Admin</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
