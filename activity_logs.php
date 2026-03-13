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
if (!empty($_GET['role'])) $filters['role'] = $_GET['role'];
if (!empty($_GET['date'])) $filters['date'] = $_GET['date'];

$filters['exclude_actions'] = ['login', 'logout', 'login_failed'];

$logs = fetch_logs($db, $filters);
$roles = $db->query('SELECT name FROM roles ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);

include __DIR__ . '/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Activity Logs</h2>
    <p class="page-subtitle">System activity history</p>
</div>

<div class="admin-toolbar mb-3">
    <div>
        <div class="admin-breadcrumb">Admin <small class="text-muted">/ Activity Logs</small></div>
        <h5 class="mb-0">Activity Logs</h5>
    </div>
    <div class="btn-group">
        <a href="dashboard.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row">
    <div class="col-12">

        <div class="card mb-3">
            <div class="card-body">
                <form method="get" class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label">Department</label>
                        <select name="role" class="form-control">
                            <option value="">All</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?php echo htmlspecialchars($r); ?>" <?php echo (!empty($filters['role']) && $filters['role'] === $r) ? 'selected' : ''; ?>><?php echo htmlspecialchars($r); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" value="<?php echo htmlspecialchars($filters['date'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table table-striped table-compact" id="activityLogsTable">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Target</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody id="activityLogsBody">
                    <?php foreach ($logs as $l): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($l['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($l['username'] ?? 'System'); ?></td>
                            <td><?php echo htmlspecialchars($l['action']); ?></td>
                            <td><?php echo htmlspecialchars(($l['target_type'] ? $l['target_type'] : '') . ($l['target_id'] ? ' #' . $l['target_id'] : '')); ?></td>
                            <td><?php echo htmlspecialchars(format_log_details($l['action'], $l['details'] ?? null)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
// Auto-refresh only the logs table body every 3 seconds
document.addEventListener('DOMContentLoaded', function () {
    var tbody = document.getElementById('activityLogsBody');
    if (!tbody) return;

    function refreshLogs() {
        if (window.SMART_POLLING_ENABLED && (document.visibilityState !== 'visible' || !document.hasFocus())) {
            return;
        }

        var url = 'partials/activity_logs_partial.php' + window.location.search;

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

    setInterval(refreshLogs, window.POLL_INTERVALS.ACTIVITY_LOGS_TABLE);
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
