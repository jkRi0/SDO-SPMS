<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();

$user = current_user();
$role = $_SESSION['role'] ?? '';

$db = get_db();

// Determine display name for welcome message
$welcomeName = ucfirst($role);
if ($role === 'supplier' && !empty($user['supplier_id'])) {
    try {
        $stmtName = $db->prepare('SELECT name FROM suppliers WHERE id = ? LIMIT 1');
        $stmtName->execute([$user['supplier_id']]);
        $rowName = $stmtName->fetch();
        if ($rowName && !empty($rowName['name'])) {
            $welcomeName = $rowName['name'];
        }
    } catch (Exception $e) {
        // Fallback to role-based name if lookup fails
    }
}

// Subtitle text
$subtitle = ucfirst($role) . ' Dashboard';
if ($role === 'supplier') {
    $subtitle = 'Supplier Dashboard';
}

$pageTitle = $subtitle . ' - ' . strtoupper((string)$role) . ' - STMS';
if ($role === 'supplier') {
    $pageTitle = $subtitle . ' - STMS';
}

include __DIR__ . '/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2 class="page-title">Welcome, <?php echo htmlspecialchars($welcomeName); ?></h2>
        <p class="page-subtitle"><?php echo htmlspecialchars($subtitle); ?></p>
    </div>
    <?php if ($role !== 'admin'): ?>
        <div>
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                <i class="fas fa-comment-alt"></i> Send Feedback
            </button>
        </div>
    <?php endif; ?>
</div>

<?php if ($role === 'procurement'): ?>
    <?php
    $activePOs = 0;
    $pendingReview = 0;
    $approved = 0;
    $stmtStats = $db->query('SELECT proc_status, proc_date, supply_status, acct_pre_status, budget_status, acct_post_status, cashier_status FROM transactions');
    $rowsStats = $stmtStats ? $stmtStats->fetchAll(PDO::FETCH_ASSOC) : [];
    foreach ($rowsStats as $t) {
        $stage = '';
        if (!empty($t['cashier_status'])) {
            $stage = 'Approved';
        } elseif (!empty($t['supply_status']) || !empty($t['acct_pre_status']) || !empty($t['budget_status']) || !empty($t['acct_post_status'])) {
            $stage = 'Pending';
        } elseif (!empty($t['proc_status']) && empty($t['supply_status'])) {
            $stage = 'Active';
        }

        if ($stage === 'Active') $activePOs++;
        if ($stage === 'Pending') $pendingReview++;
        if ($stage === 'Approved') $approved++;
    }
    ?>
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-content">
                <div class="stat-label">Active PO's</div>
                <div class="stat-number" id="statActive"><?php echo $activePOs; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-file-invoice"></i></div>
        </div>
        <div class="stat-card orange">
            <div class="stat-content">
                <div class="stat-label">Pending Review</div>
                <div class="stat-number" id="statPending"><?php echo $pendingReview; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-hourglass-half"></i></div>
        </div>
        <div class="stat-card green">
            <div class="stat-content">
                <div class="stat-label">Approved</div>
                <div class="stat-number" id="statApproved"><?php echo $approved; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>

    <div class="section-header mt-4">
        <h5 class="section-title">Procurement - Transactions</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPOModal"><i class="fas fa-plus"></i> New Transaction</button>
    </div>
    <div id="transactionsContainer">
        <?php include __DIR__ . '/partials/partials_transactions_table.php'; ?>
    </div>

<?php elseif ($role === 'admin'): ?>
    <?php
    $recentLoginLogs = [];
    try {
        $stmtLoginLogs = $db->prepare('SELECT al.created_at, al.action, al.details, u.username
                                       FROM activity_logs al
                                       LEFT JOIN users u ON al.user_id = u.id
                                       WHERE al.action IN (?,?,?)
                                       ORDER BY al.created_at DESC
                                       LIMIT 8');
        $stmtLoginLogs->execute(['login', 'logout', 'login_failed']);
        $recentLoginLogs = $stmtLoginLogs->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $recentLoginLogs = [];
    }

    $onlineUsers = [];
    $onlineCount = 0;
    try {
        $ttl = defined('SESSION_TTL_SECONDS') ? (int)SESSION_TTL_SECONDS : 600;
        $stmtOnline = $db->prepare('SELECT u.id, u.username, r.name AS role_name, MAX(us.last_seen) AS last_seen
                                    FROM user_sessions us
                                    JOIN users u ON us.user_id = u.id
                                    LEFT JOIN roles r ON u.role_id = r.id
                                    WHERE us.revoked_at IS NULL
                                      AND us.last_seen IS NOT NULL
                                      AND us.last_seen >= (NOW() - INTERVAL ? SECOND)
                                    GROUP BY u.id, u.username, r.name
                                    ORDER BY last_seen DESC
                                    LIMIT 8');
        $stmtOnline->execute([$ttl]);
        $onlineUsers = $stmtOnline->fetchAll();
        $onlineCount = count($onlineUsers);
    } catch (Exception $e) {
        $onlineUsers = [];
        $onlineCount = 0;
    }

    $totalSuppliers = 0;
    $totalTransactions = 0;
    try {
        $stmtTotalSuppliers = $db->query('SELECT COUNT(*) FROM suppliers');
        $totalSuppliers = $stmtTotalSuppliers ? (int)$stmtTotalSuppliers->fetchColumn() : 0;
    } catch (Exception $e) {
        $totalSuppliers = 0;
    }
    try {
        $stmtTotalTransactions = $db->query('SELECT COUNT(*) FROM transactions');
        $totalTransactions = $stmtTotalTransactions ? (int)$stmtTotalTransactions->fetchColumn() : 0;
    } catch (Exception $e) {
        $totalTransactions = 0;
    }
    ?>

    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title mb-1">Department Accounts</h5>
                    <p class="card-text text-muted" style="font-size: 0.9rem;">
                        View and update usernames and passwords for all department and supplier accounts.
                    </p>
                    <div class="mt-auto">
                        <a href="admin_users.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-users-cog me-1"></i> Manage Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title mb-1">Activity Logs</h5>
                    <p class="card-text text-muted" style="font-size: 0.9rem;">
                        Review all system activities (transactions, updates, account changes, etc.).
                    </p>
                    <div class="mt-auto">
                        <a href="activity_logs.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-clipboard-list me-1"></i> View Activity
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1">Login Logs</h5>
                            <?php if (empty($recentLoginLogs)): ?>
                                <div class="text-muted small">No login activity yet.</div>
                            <?php else: ?>
                                <div class="table-responsive mt-auto" style="max-height: 320px; overflow-y: auto;">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th>User</th>
                                            <th>Event</th>
                                            <th>Time</th>
                                        </tr>
                                        </thead>
                                        <tbody id="loginLogsTbody">
                                        <?php foreach ($recentLoginLogs as $ll): ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    $loginLogUsername = $ll['username'] ?? 'System';
                                                    if (($ll['action'] ?? '') === 'login_failed' && empty($ll['username']) && !empty($ll['details'])) {
                                                        $decodedLL = json_decode($ll['details'], true);
                                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedLL) && !empty($decodedLL['attempted_username'])) {
                                                            $loginLogUsername = (string)$decodedLL['attempted_username'];
                                                        }
                                                    }
                                                    echo htmlspecialchars($loginLogUsername);
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($ll['action'] ?? ''); ?></td>
                                                <td class="text-muted small"><?php echo htmlspecialchars(date('m/d/Y H:i', strtotime($ll['created_at']))); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Online Users</h6>
                                <span class="badge bg-success" id="onlineUsersBadge"><?php echo (int)$onlineCount; ?></span>
                            </div>
                            <?php if (empty($onlineUsers)): ?>
                                <div class="text-muted small">No active users detected.</div>
                            <?php else: ?>
                                <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th>User</th>
                                            <th>Role</th>
                                            <th>Last Seen</th>
                                        </tr>
                                        </thead>
                                        <tbody id="onlineUsersTbody">
                                        <?php foreach ($onlineUsers as $ou): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($ou['username']); ?></td>
                                                <td><?php echo htmlspecialchars($ou['role_name'] ?? ''); ?></td>
                                                <td class="text-muted small"><?php echo htmlspecialchars($ou['last_seen']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-header mt-4">
        <h5 class="section-title">All Transactions</h5>
    </div>
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-content">
                <div class="stat-label">Total Suppliers</div>
                <div class="stat-number" id="statTotalSuppliers"><?php echo (int)$totalSuppliers; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-industry"></i></div>
        </div>
        <div class="stat-card green">
            <div class="stat-content">
                <div class="stat-label">Total Transactions</div>
                <div class="stat-number" id="statTotalTransactions"><?php echo (int)$totalTransactions; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-file-invoice"></i></div>
        </div>
    </div>
    <div id="transactionsContainer">
        <?php include __DIR__ . '/partials/partials_transactions_table.php'; ?>
    </div>

<?php elseif ($role === 'supply'): ?>
    <?php
    $activePOs = 0;
    $pendingReview = 0;
    $approved = 0;
    $has = function ($v): bool {
        return trim((string)($v ?? '')) !== '';
    };
    $stmtStats = $db->query('SELECT proc_date, supply_status, cashier_status FROM transactions WHERE proc_date IS NOT NULL');
    $rowsStats = $stmtStats ? $stmtStats->fetchAll(PDO::FETCH_ASSOC) : [];
    foreach ($rowsStats as $t) {
        $stage = '';
        if ($has($t['cashier_status'])) {
            $stage = 'Approved';
        } elseif ($has($t['supply_status']) && !$has($t['cashier_status'])) {
            $stage = 'Pending';
        } elseif (!empty($t['proc_date']) && !$has($t['supply_status'])) {
            $stage = 'Active';
        }

        if ($stage === 'Active') $activePOs++;
        if ($stage === 'Pending') $pendingReview++;
        if ($stage === 'Approved') $approved++;
    }
    ?>
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-content">
                <div class="stat-label">Active PO's</div>
                <div class="stat-number" id="statActive"><?php echo $activePOs; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-file-invoice"></i></div>
        </div>
        <div class="stat-card orange">
            <div class="stat-content">
                <div class="stat-label">Pending Review</div>
                <div class="stat-number" id="statPending"><?php echo $pendingReview; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-hourglass-half"></i></div>
        </div>
        <div class="stat-card green">
            <div class="stat-content">
                <div class="stat-label">Approved</div>
                <div class="stat-number" id="statApproved"><?php echo $approved; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <h8 class="mb-3">Supply Unit - For Verification</h8>
    <div id="transactionsContainer">
        <?php include __DIR__ . '/partials/partials_transactions_table.php'; ?>
    </div>

<?php elseif ($role === 'accounting'): ?>
    <?php
    $activePOs = 0;
    $pendingReview = 0;
    $approved = 0;
    $has = function ($v): bool {
        return trim((string)($v ?? '')) !== '';
    };
    $stmtStats = $db->query("SELECT supply_status, acct_pre_status, budget_status, acct_post_status, cashier_status FROM transactions WHERE (NULLIF(TRIM(supply_status), '') IS NOT NULL)");
    $rowsStats = $stmtStats ? $stmtStats->fetchAll(PDO::FETCH_ASSOC) : [];
    foreach ($rowsStats as $t) {
        $stage = '';
        if ($has($t['cashier_status'])) {
            $stage = 'Approved';
        } elseif (($has($t['acct_pre_status']) && !$has($t['budget_status'])) || ($has($t['acct_post_status']) && !$has($t['cashier_status']))) {
            $stage = 'Pending';
        } elseif (($has($t['supply_status']) && !$has($t['acct_pre_status'])) || ($has($t['budget_status']) && !$has($t['acct_post_status']))) {
            $stage = 'Active';
        }

        if ($stage === 'Active') $activePOs++;
        if ($stage === 'Pending') $pendingReview++;
        if ($stage === 'Approved') $approved++;
    }
    ?>
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-content">
                <div class="stat-label">Active PO's</div>
                <div class="stat-number" id="statActive"><?php echo $activePOs; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-file-invoice"></i></div>
        </div>
        <div class="stat-card orange">
            <div class="stat-content">
                <div class="stat-label">Pending Review</div>
                <div class="stat-number" id="statPending"><?php echo $pendingReview; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-hourglass-half"></i></div>
        </div>
        <div class="stat-card green">
            <div class="stat-content">
                <div class="stat-label">Approved</div>
                <div class="stat-number" id="statApproved"><?php echo $approved; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <h8 class="mb-3">Accounting Unit - All Transactions</h8>
    <div id="transactionsContainer">
        <?php include __DIR__ . '/partials/partials_transactions_table.php'; ?>
    </div>

<?php elseif ($role === 'budget'): ?>
    <?php
    $activePOs = 0;
    $pendingReview = 0;
    $approved = 0;
    $has = function ($v): bool {
        return trim((string)($v ?? '')) !== '';
    };
    $stmtStats = $db->query("SELECT acct_pre_status, budget_status, cashier_status FROM transactions WHERE (NULLIF(TRIM(acct_pre_status), '') IS NOT NULL)");
    $rowsStats = $stmtStats ? $stmtStats->fetchAll(PDO::FETCH_ASSOC) : [];
    foreach ($rowsStats as $t) {
        $stage = '';
        if ($has($t['cashier_status'])) {
            $stage = 'Approved';
        } elseif ($has($t['budget_status']) && !$has($t['cashier_status'])) {
            $stage = 'Pending';
        } elseif ($has($t['acct_pre_status']) && !$has($t['budget_status'])) {
            $stage = 'Active';
        }

        if ($stage === 'Active') $activePOs++;
        if ($stage === 'Pending') $pendingReview++;
        if ($stage === 'Approved') $approved++;
    }
    ?>
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-content">
                <div class="stat-label">Active PO's</div>
                <div class="stat-number" id="statActive"><?php echo $activePOs; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-file-invoice"></i></div>
        </div>
        <div class="stat-card orange">
            <div class="stat-content">
                <div class="stat-label">Pending Review</div>
                <div class="stat-number" id="statPending"><?php echo $pendingReview; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-hourglass-half"></i></div>
        </div>
        <div class="stat-card green">
            <div class="stat-content">
                <div class="stat-label">Approved</div>
                <div class="stat-number" id="statApproved"><?php echo $approved; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <h8 class="mb-3">Budget Unit - For DV Preparation</h8>
    <div id="transactionsContainer">
        <?php include __DIR__ . '/partials/partials_transactions_table.php'; ?>
    </div>

<?php elseif ($role === 'cashier'): ?>
    <?php
    $activePOs = 0;
    $pendingReview = 0;
    $approved = 0;
    $has = function ($v): bool {
        return trim((string)($v ?? '')) !== '';
    };
    $stmtStats = $db->query("SELECT acct_post_status, cashier_status FROM transactions WHERE (NULLIF(TRIM(acct_post_status), '') IS NOT NULL)");
    $rowsStats = $stmtStats ? $stmtStats->fetchAll(PDO::FETCH_ASSOC) : [];
    foreach ($rowsStats as $t) {
        $stage = '';
        $cashierUpper = strtoupper(trim((string)($t['cashier_status'] ?? '')));
        if ($cashierUpper === 'COMPLETED') {
            $stage = 'Approved';
        } elseif ($has($t['cashier_status'])) {
            $stage = 'Pending';
        } elseif ($has($t['acct_post_status']) && !$has($t['cashier_status'])) {
            $stage = 'Active';
        }

        if ($stage === 'Active') $activePOs++;
        if ($stage === 'Pending') $pendingReview++;
        if ($stage === 'Approved') $approved++;
    }
    ?>
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-content">
                <div class="stat-label">Active PO's</div>
                <div class="stat-number" id="statActive"><?php echo $activePOs; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-file-invoice"></i></div>
        </div>
        <div class="stat-card orange">
            <div class="stat-content">
                <div class="stat-label">Pending Review</div>
                <div class="stat-number" id="statPending"><?php echo $pendingReview; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-hourglass-half"></i></div>
        </div>
        <div class="stat-card green">
            <div class="stat-content">
                <div class="stat-label">Approved</div>
                <div class="stat-number" id="statApproved"><?php echo $approved; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <h8 class="mb-3">Cashier Unit - For Payment / OR</h8>
    <div id="transactionsContainer">
        <?php include __DIR__ . '/partials/partials_transactions_table.php'; ?>
    </div>

<?php elseif ($role === 'supplier'): ?>
    <h8 class="mb-3">My Transactions</h8>
    <div id="transactionsContainer">
        <?php include __DIR__ . '/partials/partials_transactions_table.php'; ?>
    </div>

<?php else: ?>
    <div class="alert alert-info">
        No specific dashboard implemented for this role yet.
    </div>
<?php endif; ?>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header" style="border-bottom: 1px solid #e5e7eb;">
                <h5 class="modal-title" id="feedbackModalLabel">Send Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3" style="font-size: 0.9rem;">Tell us about any bugs, suggestions, or other comments you have about the system.</p>
                <div id="feedbackAlert"></div>
                <form id="feedbackForm">
                    <div class="mb-3">
                        <label for="feedbackType" class="form-label" style="font-size: 0.9rem; font-weight: 600;">Type</label>
                        <select id="feedbackType" name="type" class="form-control" required>
                            <option value="">-- Select type --</option>
                            <option value="Bug">Bug</option>
                            <option value="Suggestion">Suggestion</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="feedbackMessage" class="form-label" style="font-size: 0.9rem; font-weight: 600;">Message</label>
                        <textarea id="feedbackMessage" name="message" class="form-control" rows="4" required
                                  placeholder="Describe the issue, suggestion, or comment..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e5e7eb;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" form="feedbackForm">Submit Feedback</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Feedback submit handler
    var feedbackForm = document.getElementById('feedbackForm');
    var feedbackAlert = document.getElementById('feedbackAlert');
    if (feedbackForm && feedbackAlert) {
        feedbackForm.addEventListener('submit', function (e) {
            e.preventDefault();

            feedbackAlert.innerHTML = '';

            var formData = new FormData(feedbackForm);

            fetch('api/api_submit_feedback.php', {
                method: 'POST',
                body: formData
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data.success) {
                        feedbackAlert.innerHTML = '<div class="alert alert-success mb-2" role="alert">' + (data.message || 'Thank you, your feedback has been recorded.') + '</div>';
                        feedbackForm.reset();
                    } else {
                        feedbackAlert.innerHTML = '<div class="alert alert-danger mb-2" role="alert">' + (data.message || 'Unable to save feedback.') + '</div>';
                    }
                })
                .catch(function () {
                    feedbackAlert.innerHTML = '<div class="alert alert-danger mb-2" role="alert">An unexpected error occurred while sending feedback.</div>';
                });
        });
    }

    // Dashboard transactions auto-refresh (only table body)
    var txBody = document.getElementById('transactionsBody');
    if (txBody) {
        var statActive = document.getElementById('statActive');
        var statPending = document.getElementById('statPending');
        var statApproved = document.getElementById('statApproved');

        var txRefreshInFlight = false;
        var statsRefreshInFlight = false;

        var dtInstance = null;
        function ensureDtInstance() {
            if (!window.jQuery || !jQuery.fn || !jQuery.fn.dataTable) {
                dtInstance = null;
                return;
            }
            if (jQuery.fn.dataTable.isDataTable('#transactionsTable')) {
                try {
                    dtInstance = jQuery('#transactionsTable').DataTable();
                } catch (e) {
                    dtInstance = null;
                }
            } else {
                dtInstance = null;
            }
        }

        ensureDtInstance();

        function refreshDashboardStats() {
            if (!statActive || !statPending || !statApproved) {
                return;
            }
            if (document.visibilityState !== 'visible') {
                return;
            }
            if (statsRefreshInFlight) {
                return;
            }
            statsRefreshInFlight = true;
            fetch('api/api_dashboard_stats.php', { cache: 'no-store' })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (!data || !data.success) return;
                    statActive.textContent = String(data.active ?? 0);
                    statPending.textContent = String(data.pending ?? 0);
                    statApproved.textContent = String(data.approved ?? 0);
                })
                .catch(function () {
                })
                .finally(function () {
                    statsRefreshInFlight = false;
                });
        }

        function refreshDashboardTransactions() {
            if (document.visibilityState !== 'visible') {
                return;
            }

            if (txRefreshInFlight) {
                return;
            }
            txRefreshInFlight = true;

            fetch('partials/transactions_rows_partial.php', { cache: 'no-store' })
                .then(function (res) {
                    if (!res.ok) throw new Error('Network error');
                    return res.text();
                })
                .then(function (html) {
                    ensureDtInstance();
                    if (dtInstance && window.jQuery) {
                        var currentSearch = dtInstance.search();
                        var currentPage = dtInstance.page();
                        var currentLength = dtInstance.page.len();
                        var currentOrder = dtInstance.order();

                        var rows = jQuery(html).toArray();
                        dtInstance.clear();
                        dtInstance.rows.add(rows);
                        dtInstance.order(currentOrder);
                        dtInstance.page.len(currentLength);
                        dtInstance.search(currentSearch);
                        dtInstance.page(currentPage).draw(false);
                    } else {
                        txBody.innerHTML = html;
                    }

                    refreshDashboardStats();
                })
                .catch(function () {
                    // fail silently
                })
                .finally(function () {
                    txRefreshInFlight = false;
                });
        }

        refreshDashboardStats();
        setInterval(function () {
            refreshDashboardTransactions();
            refreshDashboardStats();
        }, window.POLL_INTERVALS.DASHBOARD);
    }

    // Admin "Online Users" auto-refresh
    var onlineUsersBadge = document.getElementById('onlineUsersBadge');
    var onlineUsersTbody = document.getElementById('onlineUsersTbody');
    if (onlineUsersBadge && onlineUsersTbody) {
        function refreshOnlineUsers() {
            if (document.visibilityState !== 'visible') {
                return;
            }

            fetch('api/api_online_users.php', { cache: 'no-store' })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (!data || !data.success) return;

                    var users = data.users || [];
                    onlineUsersBadge.textContent = (data.count != null) ? String(data.count) : String(users.length);

                    onlineUsersTbody.innerHTML = '';

                    if (users.length === 0) {
                        var trEmpty = document.createElement('tr');
                        var tdEmpty = document.createElement('td');
                        tdEmpty.colSpan = 3;
                        tdEmpty.className = 'text-muted small';
                        tdEmpty.textContent = 'No active users detected.';
                        trEmpty.appendChild(tdEmpty);
                        onlineUsersTbody.appendChild(trEmpty);
                        return;
                    }

                    users.forEach(function (u) {
                        var tr = document.createElement('tr');

                        var tdUser = document.createElement('td');
                        tdUser.textContent = u.username || '';

                        var tdRole = document.createElement('td');
                        tdRole.textContent = u.role_name || '';

                        var tdLast = document.createElement('td');
                        tdLast.className = 'text-muted small';
                        tdLast.textContent = u.last_seen || '';

                        tr.appendChild(tdUser);
                        tr.appendChild(tdRole);
                        tr.appendChild(tdLast);

                        onlineUsersTbody.appendChild(tr);
                    });
                })
                .catch(function () {
                    // ignore errors
                });
        }

        refreshOnlineUsers();
        setInterval(refreshOnlineUsers, window.POLL_INTERVALS.DASHBOARD_ONLINE_USERS);
    }

    // Admin "Login Logs" auto-refresh
    var loginLogsTbody = document.getElementById('loginLogsTbody');
    if (loginLogsTbody) {
        function refreshLoginLogs() {
            if (document.visibilityState !== 'visible') {
                return;
            }

            fetch('api/api_login_logs.php?limit=8', { cache: 'no-store' })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (!data || !data.success) return;

                    var logs = data.logs || [];
                    loginLogsTbody.innerHTML = '';

                    if (logs.length === 0) {
                        var trEmpty = document.createElement('tr');
                        var tdEmpty = document.createElement('td');
                        tdEmpty.colSpan = 3;
                        tdEmpty.className = 'text-muted small';
                        tdEmpty.textContent = 'No login activity yet.';
                        trEmpty.appendChild(tdEmpty);
                        loginLogsTbody.appendChild(trEmpty);
                        return;
                    }

                    logs.forEach(function (l) {
                        var tr = document.createElement('tr');

                        var tdUser = document.createElement('td');
                        tdUser.textContent = l.username || 'System';

                        var tdEvent = document.createElement('td');
                        tdEvent.textContent = l.event || '';

                        var tdTime = document.createElement('td');
                        tdTime.className = 'text-muted small';
                        tdTime.textContent = l.time || '';

                        tr.appendChild(tdUser);
                        tr.appendChild(tdEvent);
                        tr.appendChild(tdTime);

                        loginLogsTbody.appendChild(tr);
                    });
                })
                .catch(function () {
                    // ignore errors
                });
        }

        refreshLoginLogs();
        setInterval(refreshLoginLogs, window.POLL_INTERVALS.DASHBOARD_LOGIN_LOGS);
    }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
