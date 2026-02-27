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

include __DIR__ . '/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2 class="page-title">Welcome, <?php echo htmlspecialchars($welcomeName); ?></h2>
        <p class="page-subtitle"><?php echo htmlspecialchars($subtitle); ?></p>
    </div>
    <div>
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#feedbackModal">
            <i class="fas fa-comment-alt"></i> Send Feedback
        </button>
    </div>
</div>

<?php if ($role === 'procurement'): ?>
    <?php
    // Procurement dashboard stats - Real-time accurate counts
    // Active PO's: Transactions with procurement status (currently in procurement phase)
    $activePOs = (int)$db->query('SELECT COUNT(*) AS c FROM transactions WHERE proc_status IS NOT NULL AND supply_status IS NULL')->fetch()['c'];
    
    // Pending Review: Transactions that have moved past procurement but not yet completed (supply or accounting stages)
    $pendingReview = (int)$db->query('SELECT COUNT(*) AS c FROM transactions WHERE (supply_status IS NOT NULL OR acct_pre_status IS NOT NULL OR budget_status IS NOT NULL OR acct_post_status IS NOT NULL) AND cashier_status IS NULL')->fetch()['c'];
    
    // Approved: Transactions completed (have cashier status which means fully processed)
    $approved = (int)$db->query('SELECT COUNT(*) AS c FROM transactions WHERE cashier_status IS NOT NULL')->fetch()['c'];
    ?>
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-content">
                <div class="stat-label">Active PO's</div>
                <div class="stat-number"><?php echo $activePOs; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-file-invoice"></i></div>
        </div>
        <div class="stat-card orange">
            <div class="stat-content">
                <div class="stat-label">Pending Review</div>
                <div class="stat-number"><?php echo $pendingReview; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-hourglass-half"></i></div>
        </div>
        <div class="stat-card green">
            <div class="stat-content">
                <div class="stat-label">Approved</div>
                <div class="stat-number"><?php echo $approved; ?></div>
            </div>
            <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>

    <div class="section-header mt-4">
        <h5 class="section-title">Procurement - Transactions</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPOModal"><i class="fas fa-plus"></i> New Transaction</button>
    </div>
    <div id="transactionsContainer">
        <?php include __DIR__ . '/partials_transactions_table.php'; ?>
    </div>

<?php elseif ($role === 'admin'): ?>
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
                    <h5 class="card-title mb-1">System Activity & Login Logs</h5>
                    <p class="card-text text-muted" style="font-size: 0.9rem;">
                        Review recent admin actions and system activity logs, including logins.
                    </p>
                    <div class="mt-auto">
                        <a href="admin_logs.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-1"></i> View Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($role === 'supply'): ?>
    <h8 class="mb-3">Supply Unit - For Verification</h8>
    <div id="transactionsContainer">
        <?php include __DIR__ . '/partials_transactions_table.php'; ?>
    </div>

<?php elseif ($role === 'accounting'): ?>
    <h8 class="mb-3">Accounting Unit - All Transactions</h8>
    <div id="transactionsContainer">
        <?php include __DIR__ . '/partials_transactions_table.php'; ?>
    </div>

<?php elseif ($role === 'budget'): ?>
    <h8 class="mb-3">Budget Unit - For DV Preparation</h8>
    <div id="transactionsContainer">
        <?php include __DIR__ . '/partials_transactions_table.php'; ?>
    </div>

<?php elseif ($role === 'cashier'): ?>
    <h8 class="mb-3">Cashier Unit - For Payment / OR</h8>
    <div id="transactionsContainer">
        <?php include __DIR__ . '/partials_transactions_table.php'; ?>
    </div>

<?php elseif ($role === 'supplier'): ?>
    <h8 class="mb-3">My Transactions</h8>
    <div id="transactionsContainer">
        <?php include __DIR__ . '/partials_transactions_table.php'; ?>
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

            fetch('api_submit_feedback.php', {
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
        function refreshDashboardTransactions() {
            if (document.visibilityState !== 'visible') {
                return;
            }

            fetch('transactions_rows_partial.php', { cache: 'no-store' })
                .then(function (res) {
                    if (!res.ok) throw new Error('Network error');
                    return res.text();
                })
                .then(function (html) {
                    var currentSearch = '';
                    var currentPage = 0;
                    var currentLength = 10;
                    var currentOrder = [[5, 'desc']];

                    // If DataTables is active, capture state and destroy before we touch the DOM
                    if (window.jQuery && jQuery.fn.DataTable) {
                        var existing = null;
                        try {
                            existing = jQuery('#transactionsTable').DataTable();
                        } catch (e) {
                            existing = null;
                        }
                        if (existing) {
                            currentSearch = existing.search();
                            currentPage = existing.page();
                            currentLength = existing.page.len();
                            currentOrder = existing.order();
                            existing.destroy();
                        }
                    }

                    // Replace tbody with fresh rows
                    txBody.innerHTML = html;

                    // Re-initialize DataTables and restore previous state
                    if (window.jQuery && jQuery.fn.DataTable) {
                        var dtNew = jQuery('#transactionsTable').DataTable({
                            responsive: true,
                            pageLength: currentLength,
                            lengthMenu: [10, 25, 50, 100],
                            columnDefs: [{ orderable: false, targets: -1 }],
                            // Default sort: Created column (index 5)
                            order: currentOrder,
                            language: { searchPlaceholder: 'Search...', search: '' }
                        });

                        if (currentSearch) {
                            dtNew.search(currentSearch);
                        }
                        dtNew.page(currentPage).draw(false);
                    }
                })
                .catch(function () {
                    // Silent fail – keep last known data
                });
        }

        setInterval(refreshDashboardTransactions, 5000);
    }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
