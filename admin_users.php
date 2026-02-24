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

// Fetch roles and optionally filter users by role (unit)
$roles = $db->query('SELECT id, name FROM roles ORDER BY name')->fetchAll();
$filterRole = isset($_GET['role_id']) && is_numeric($_GET['role_id']) ? (int)$_GET['role_id'] : null;
if ($filterRole) {
    $stmt = $db->prepare('SELECT u.id, u.username, u.supplier_id, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE r.id = ? ORDER BY u.id DESC');
    $stmt->execute([$filterRole]);
    $users = $stmt->fetchAll();
} else {
    $users = $db->query('SELECT u.id, u.username, u.supplier_id, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id DESC')->fetchAll();
}

include __DIR__ . '/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Admin - User Management</h2>
    <p class="page-subtitle">Manage system users and roles</p>
</div>

<div class="admin-toolbar mb-3">
    <div>
        <div class="admin-breadcrumb">Admin <small class="text-muted">/ Manage Users</small></div>
        <h5 class="mb-0">User Accounts</h5>
    </div>
    <div class="btn-group">
        <a href="dashboard.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-success py-2"><?php echo htmlspecialchars($_SESSION['flash']); ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="card admin-card mt-3">
    <div class="card-body">

<div class="row">
    <div class="col-12">

    <div class="section-header mt-3">
        <h5 class="section-title">Users</h5>
        <div class="d-flex align-items-end gap-3 w-100">
            <form method="get" class="d-flex" style="min-width:260px;">
                <div>
                    <label class="form-label mb-1">Filter by Unit</label>
                    <select name="role_id" class="form-control form-select" onchange="this.form.submit()">
                        <option value="">All Units</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?php echo $r['id']; ?>" <?php echo (isset($_GET['role_id']) && $_GET['role_id'] == $r['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($r['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <div class="ms-auto d-flex gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal"><i class="fas fa-user-plus"></i> Add User</button>
            </div>
        </div>
    </div>

<div class="table-wrapper mt-3">
    <table class="table table-striped table-compact" id="adminUsersTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Role</th>
                <th>Supplier ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="adminUsersBody">
            <?php foreach ($users as $u): ?>
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

                <!-- Edit User Modal -->
                <div class="modal fade" id="editUserModal<?php echo $u['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit User #<?php echo $u['id']; ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="admin_user_action.php" method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($u['username']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password (leave blank to keep)</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role_id" class="form-control" required>
                                            <?php foreach ($roles as $r): ?>
                                                <option value="<?php echo $r['id']; ?>" <?php echo ($r['name'] === $u['role_name']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($r['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Supplier ID (optional)</label>
                                        <input type="number" name="supplier_id" class="form-control" value="<?php echo htmlspecialchars($u['supplier_id'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="admin_user_action.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role_id" class="form-control" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supplier ID (optional)</label>
                        <input type="number" name="supplier_id" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

    </div>
</div>

<script>
// Auto-refresh only the users table body every 3 seconds
document.addEventListener('DOMContentLoaded', function () {
    var tbody = document.getElementById('adminUsersBody');
    if (!tbody) return;

    function refreshUsers() {
        if (document.visibilityState !== 'visible') {
            return;
        }

        var url = 'admin_users_partial.php' + window.location.search;

        fetch(url, { cache: 'no-store' })
            .then(function (res) {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.text();
            })
            .then(function (html) {
                tbody.innerHTML = html;
            })
            .catch(function () {
                // Fail silently; keep last known data
            });
    }

    setInterval(refreshUsers, 3000);
});
</script>

<?php include __DIR__ . '/footer.php'; ?>