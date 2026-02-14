<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/audit.php';

require_login();
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo 'Access denied.';
    exit;
}

$db = get_db();
$filters = [];
if (!empty($_GET['user_id'])) $filters['user_id'] = (int)$_GET['user_id'];
if (!empty($_GET['action'])) $filters['action'] = $_GET['action'];
if (!empty($_GET['from'])) $filters['from'] = $_GET['from'];
if (!empty($_GET['to'])) $filters['to'] = $_GET['to'];

$logs = fetch_logs($db, $filters);
$users = $db->query('SELECT id, username FROM users ORDER BY username')->fetchAll();

include __DIR__ . '/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Activity Logs</h2>
    <p class="page-subtitle">Recent administrative actions</p>
</div>

<div class="admin-toolbar mb-3">
    <div>
        <div class="admin-breadcrumb">Admin <small class="text-muted">/ Activity Logs</small></div>
        <h5 class="mb-0">Activity Logs</h5>
    </div>
    <div class="btn-group">
        <a href="admin_logs.php" class="btn btn-outline-secondary"><i class="fas fa-sync-alt"></i> Refresh</a>
        <a href="export_logs.php" class="btn btn-outline-secondary"><i class="fas fa-file-export me-1"></i> Export</a>
    </div>
</div>

<div class="row">
    <div class="col-12">

        <div class="card mb-3">
            <div class="card-body">
                <form method="get" class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-control">
                            <option value="">All</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?php echo $u['id']; ?>" <?php echo (!empty($filters['user_id']) && $filters['user_id'] == $u['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($u['username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label">Action</label>
                        <input type="text" name="action" value="<?php echo htmlspecialchars($filters['action'] ?? ''); ?>" class="form-control" placeholder="create_user">
                    </div>
                    <div class="col-auto">
                        <label class="form-label">From</label>
                        <input type="date" name="from" value="<?php echo htmlspecialchars($filters['from'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-auto">
                        <label class="form-label">To</label>
                        <input type="date" name="to" value="<?php echo htmlspecialchars($filters['to'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table table-striped table-compact">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Target</th>
                        <th>Details</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $l): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($l['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($l['username'] ?? 'System'); ?></td>
                            <td><?php echo htmlspecialchars($l['action']); ?></td>
                            <td><?php echo htmlspecialchars($l['target_type'] . ' #' . $l['target_id']); ?></td>
                            <td><?php echo htmlspecialchars($l['details']); ?></td>
                            <td><?php echo htmlspecialchars($l['ip']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>