<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo 'Access denied.';
    exit;
}

$db = get_db();
require_once __DIR__ . '/audit.php';
$action = $_POST['action'] ?? '';

try {
    if ($action === 'create') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role_id = (int)($_POST['role_id'] ?? 0);
        $supplier_id = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;

        if ($username === '' || $password === '' || $role_id <= 0) throw new Exception('Missing required fields.');

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO users (username, password_hash, role_id, supplier_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([$username, $hash, $role_id, $supplier_id]);
        $newId = $db->lastInsertId();
        create_log($db, $_SESSION['user_id'] ?? null, 'create_user', 'user', $newId, json_encode(['username' => $username, 'role_id' => $role_id, 'supplier_id' => $supplier_id]));
        $_SESSION['flash'] = 'User created.';
        $_SESSION['flash_type'] = 'success';
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role_id = (int)($_POST['role_id'] ?? 0);
        $supplier_id = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;

        if ($id <= 0 || $username === '' || $role_id <= 0) throw new Exception('Missing required fields.');

        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare('UPDATE users SET username = ?, password_hash = ?, role_id = ?, supplier_id = ? WHERE id = ?');
            $stmt->execute([$username, $hash, $role_id, $supplier_id, $id]);
        } else {
            $stmt = $db->prepare('UPDATE users SET username = ?, role_id = ?, supplier_id = ? WHERE id = ?');
            $stmt->execute([$username, $role_id, $supplier_id, $id]);
        }
        create_log($db, $_SESSION['user_id'] ?? null, 'update_user', 'user', $id, json_encode(['username' => $username, 'role_id' => $role_id, 'supplier_id' => $supplier_id]));
        $_SESSION['flash'] = 'User updated.';
        $_SESSION['flash_type'] = 'success';
    } elseif ($action === 'reset_password') {
        $id = (int)($_POST['id'] ?? 0);
        $adminPassword = $_POST['admin_password'] ?? '';
        
        if ($id <= 0 || $adminPassword === '') throw new Exception('Missing required fields.');
        if ($id == ($_SESSION['user_id'] ?? 0)) throw new Exception('You cannot reset your own password.');
        
        // Verify admin password
        $adminStmt = $db->prepare('SELECT password_hash FROM users WHERE id = ?');
        $adminStmt->execute([$_SESSION['user_id']]);
        $adminRow = $adminStmt->fetch();
        
        if (!$adminRow || !password_verify($adminPassword, $adminRow['password_hash'])) {
            throw new Exception('Admin password verification failed.');
        }
        
        // Get user info for audit
        $userStmt = $db->prepare('SELECT username FROM users WHERE id = ?');
        $userStmt->execute([$id]);
        $userRow = $userStmt->fetch();
        
        if (!$userRow) throw new Exception('User not found.');
        
        // Reset password to default
        $defaultPassword = '12345';
        $hash = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([$hash, $id]);
        
        create_log($db, $_SESSION['user_id'] ?? null, 'reset_password', 'user', $id, json_encode([
            'username' => $userRow['username'],
            'new_password' => '12345'
        ]));
        $_SESSION['flash'] = 'Password reset successfully. New password: 12345';
        $_SESSION['flash_type'] = 'success';
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception('Invalid user id.');
        if ($id == ($_SESSION['user_id'] ?? 0)) throw new Exception('You cannot delete your own account.');

        $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
        create_log($db, $_SESSION['user_id'] ?? null, 'delete_user', 'user', $id, null);
        $_SESSION['flash'] = 'User deleted.';
        $_SESSION['flash_type'] = 'success';
    }
} catch (Exception $e) {
    $_SESSION['flash'] = 'Error: ' . $e->getMessage();
    $_SESSION['flash_type'] = 'danger';
}

header('Location: admin_users.php');
exit;
