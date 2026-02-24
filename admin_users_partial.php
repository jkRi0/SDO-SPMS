<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied.');
}

$db = get_db();

$roles = $db->query('SELECT id, name FROM roles ORDER BY name')->fetchAll();
$rolesById = [];
foreach ($roles as $r) {
    $rolesById[$r['id']] = $r['name'];
}

$filterRole = isset($_GET['role_id']) && is_numeric($_GET['role_id']) ? (int)$_GET['role_id'] : null;
if ($filterRole) {
    $stmt = $db->prepare('SELECT u.id, u.username, u.supplier_id, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE r.id = ? ORDER BY u.id DESC');
    $stmt->execute([$filterRole]);
    $users = $stmt->fetchAll();
} else {
    $users = $db->query('SELECT u.id, u.username, u.supplier_id, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id DESC')->fetchAll();
}

foreach ($users as $u): ?>
<tr>
    <td><?php echo htmlspecialchars($u['id']); ?></td>
    <td><?php echo htmlspecialchars($u['username']); ?></td>
    <td><?php echo htmlspecialchars($u['role_name']); ?></td>
    <td><?php echo htmlspecialchars($u['supplier_id'] ?? ''); ?></td>
    <td>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $u['id']; ?>">Edit</button>
        <?php if ($u['id'] != ($_SESSION['user_id'] ?? 0)): ?>
            <form action="admin_user_action.php" method="post" style="display:inline-block;" onsubmit="return confirm('Delete this user?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
            </form>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
