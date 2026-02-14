<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();

$user = current_user();
$role = $_SESSION['role'] ?? '';

$db = get_db();

include __DIR__ . '/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Welcome, <?php echo htmlspecialchars(ucfirst($role)); ?></h2>
    <p class="page-subtitle"><?php echo htmlspecialchars(ucfirst($role)); ?> Dashboard</p>
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
    <?php include __DIR__ . '/partials_transactions_table.php'; ?>

<?php elseif ($role === 'admin'): ?>
    <div class="alert alert-info">
        No specific dashboard implemented for this role yet.
    </div>

<?php elseif ($role === 'supply'): ?>
    <h8 class="mb-3">Supply Unit - For Verification</h8>
    <?php include __DIR__ . '/partials_transactions_table.php'; ?>

<?php elseif ($role === 'accounting'): ?>
    <h8 class="mb-3">Accounting Unit - All Transactions</h8>
    <?php include __DIR__ . '/partials_transactions_table.php'; ?>

<?php elseif ($role === 'budget'): ?>
    <h8 class="mb-3">Budget Unit - For DV Preparation</h8>
    <?php include __DIR__ . '/partials_transactions_table.php'; ?>

<?php elseif ($role === 'cashier'): ?>
    <h8 class="mb-3">Cashier Unit - For Payment / OR</h8>
    <?php include __DIR__ . '/partials_transactions_table.php'; ?>

<?php elseif ($role === 'supplier'): ?>
    <h8 class="mb-3">My Transactions</h8>
    <?php include __DIR__ . '/partials_transactions_table.php'; ?>

<?php else: ?>
    <div class="alert alert-info">
        No specific dashboard implemented for this role yet.
    </div>
<?php endif; ?>



<?php include __DIR__ . '/footer.php'; ?>

