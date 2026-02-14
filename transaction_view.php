<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();

$db = get_db();
$role = $_SESSION['role'] ?? '';
$supplierId = $_SESSION['supplier_id'] ?? null;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// Load transaction
$stmt = $db->prepare('SELECT t.*, s.name AS supplier_name 
                      FROM transactions t 
                      JOIN suppliers s ON t.supplier_id = s.id 
                      WHERE t.id = ?');
$stmt->execute([$id]);
$transaction = $stmt->fetch();

if (!$transaction) {
    header('Location: dashboard.php');
    exit;
}

if ($role === 'supplier' && $supplierId && $transaction['supplier_id'] != $supplierId) {
    http_response_code(403);
    echo 'Access denied.';
    exit;
}

$error = '';
$success = '';

// Prepare coverage display string from coverage_start / coverage_end (MM/DD/YYYY)
$coverageDisplay = 'N/A';
if (!empty($transaction['coverage_start']) && !empty($transaction['coverage_end'])) {
    $startFmt = date('m/d/Y', strtotime($transaction['coverage_start']));
    $endFmt   = date('m/d/Y', strtotime($transaction['coverage_end']));
    $coverageDisplay = $startFmt . ' to ' . $endFmt;
} elseif (!empty($transaction['coverage_start'])) {
    $coverageDisplay = date('m/d/Y', strtotime($transaction['coverage_start']));
} elseif (!empty($transaction['coverage_end'])) {
    $coverageDisplay = date('m/d/Y', strtotime($transaction['coverage_end']));
}

// Compute last update date from stage dates (proc, supply, accounting, budget, cashier)
$lastUpdateDisplay = 'N/A';
$stageDates = [
    $transaction['proc_date'] ?? null,
    $transaction['supply_date'] ?? null,
    $transaction['acct_pre_date'] ?? null,
    $transaction['budget_dv_date'] ?? null,
    $transaction['acct_post_date'] ?? null,
    $transaction['cashier_payment_date'] ?? null,
];

$validDates = array_filter($stageDates, function ($d) {
    return !empty($d);
});

if (!empty($validDates)) {
    $latest = null;
    $latestTs = 0;
    foreach ($validDates as $d) {
        $ts = strtotime($d);
        if ($ts !== false && $ts >= $latestTs) {
            $latestTs = $ts;
            $latest = $d;
        }
    }
    if ($latest !== null) {
        $lastUpdateDisplay = date('m/d/Y', $latestTs);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $fields = [];
        $params = [];

        if ($role === 'procurement') {
            $fields[] = 'proc_status = ?';
            $fields[] = 'proc_remarks = ?';
            $fields[] = 'proc_date = CURDATE()';
            $params[] = trim($_POST['proc_status'] ?? '');
            $params[] = trim($_POST['proc_remarks'] ?? '');
        } elseif ($role === 'supply') {
            $fields[] = 'supply_status = ?';
            $fields[] = 'supply_delivery_receipt = ?';
            $fields[] = 'supply_sales_invoice = ?';
            $fields[] = 'supply_remarks = ?';
            $fields[] = 'supply_date = CURDATE()';
            $params[] = trim($_POST['supply_status'] ?? '');
            $params[] = trim($_POST['supply_delivery_receipt'] ?? '');
            $params[] = trim($_POST['supply_sales_invoice'] ?? '');
            $params[] = trim($_POST['supply_remarks'] ?? '');
        } elseif ($role === 'accounting') {
            // Detect whether acting pre- or post-budget based on posted flag
            $stage = $_POST['stage'] ?? 'pre';
            if ($stage === 'pre') {
                $fields[] = 'acct_pre_status = ?';
                $fields[] = 'acct_pre_remarks = ?';
                $fields[] = 'acct_pre_date = CURDATE()';
                $params[] = trim($_POST['acct_pre_status'] ?? '');
                $params[] = trim($_POST['acct_pre_remarks'] ?? '');
            } else {
                $fields[] = 'acct_post_status = ?';
                $fields[] = 'acct_post_remarks = ?';
                $fields[] = 'acct_post_date = CURDATE()';
                $params[] = trim($_POST['acct_post_status'] ?? '');
                $params[] = trim($_POST['acct_post_remarks'] ?? '');
            }
        } elseif ($role === 'budget') {
            $fields[] = 'budget_dv_number = ?';
            $fields[] = 'budget_dv_date = ?';
            $fields[] = 'budget_status = ?';
            $fields[] = 'budget_demandability = ?';
            $fields[] = 'budget_remarks = ?';
            $params[] = trim($_POST['budget_dv_number'] ?? '');
            $params[] = trim($_POST['budget_dv_date'] ?? '');
            $params[] = trim($_POST['budget_status'] ?? '');
            $params[] = trim($_POST['budget_demandability'] ?? '');
            $params[] = trim($_POST['budget_remarks'] ?? '');
        } elseif ($role === 'cashier') {
            $fields[] = 'cashier_status = ?';
            $fields[] = 'cashier_remarks = ?';
            $fields[] = 'cashier_or_number = ?';
            $fields[] = 'cashier_or_date = ?';
            $fields[] = 'cashier_landbank_ref = ?';
            $fields[] = 'cashier_payment_date = ?';
            $params[] = trim($_POST['cashier_status'] ?? '');
            $params[] = trim($_POST['cashier_remarks'] ?? '');
            $params[] = trim($_POST['cashier_or_number'] ?? '');
            $params[] = trim($_POST['cashier_or_date'] ?? '');
            $params[] = trim($_POST['cashier_landbank_ref'] ?? '');
            $params[] = trim($_POST['cashier_payment_date'] ?? '');
        }

        if ($fields) {
            $params[] = $id;
            $sql = 'UPDATE transactions SET ' . implode(', ', $fields) . ' WHERE id = ?';
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $success = 'Transaction updated successfully.';

            // Reload data
            $stmt = $db->prepare('SELECT t.*, s.name AS supplier_name 
                                  FROM transactions t 
                                  JOIN suppliers s ON t.supplier_id = s.id 
                                  WHERE t.id = ?');
            $stmt->execute([$id]);
            $transaction = $stmt->fetch();
        }
    } catch (Exception $e) {
        $error = 'Error updating transaction.';
    }
}

// Determine if cashier can proceed to Landbank
$canProceedLandbank = false;
if ($role === 'cashier') {
    // Check all statuses are filled and not pending/correction
    $statusesOk = 
        !empty($transaction['proc_status']) && !in_array($transaction['proc_status'], ['FOR CORRECTION', 'PENDING'], true) &&
        !empty($transaction['supply_status']) && !in_array($transaction['supply_status'], ['FOR CORRECTION', 'PENDING'], true) &&
        !empty($transaction['acct_pre_status']) && !in_array($transaction['acct_pre_status'], ['FOR CORRECTION', 'PENDING'], true) &&
        !empty($transaction['budget_status']) && !in_array($transaction['budget_status'], ['FOR CORRECTION', 'PENDING'], true) &&
        !empty($transaction['acct_post_status']) && !in_array($transaction['acct_post_status'], ['FOR CORRECTION', 'PENDING'], true);
    
    // Check budget details are filled
    $budgetOk = !empty($transaction['budget_dv_number']) && !empty($transaction['budget_dv_date']);
    
    $canProceedLandbank = $statusesOk && $budgetOk;
}

include __DIR__ . '/header.php';
?>

<div class="row mb-3">
    <div class="col-md-8">
        <h5>Transaction Details</h5>
        <small class="text-muted">
            PO # <?php echo htmlspecialchars($transaction['po_number']); ?> |
            Supplier: <?php echo htmlspecialchars($transaction['supplier_name']); ?>
        </small>
    </div>
    <div class="col-md-4 text-md-end mt-2 mt-md-0">
        <a href="dashboard.php" class="btn btn-secondary btn-sm">Back to Dashboard</a>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success py-2"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                <div class="info-grid">
                    <div class="info-item info-item-full">
                        <div class="info-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="info-content">
                            <p class="info-label">Program Title</p>
                            <p class="info-value"><?php echo htmlspecialchars($transaction['program_title']); ?></p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="info-content">
                            <p class="info-label">PO Amount</p>
                            <p class="info-value">₱ <?php echo number_format($transaction['amount'], 2); ?></p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-calendar"></i></div>
                        <div class="info-content">
                            <p class="info-label">Date Coverage</p>
                            <p class="info-value"><?php echo $coverageDisplay; ?></p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-clock"></i></div>
                        <div class="info-content">
                            <p class="info-label">Date Created</p>
                            <p class="info-value"><?php echo date('m/d/Y H:i:s', strtotime($transaction['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Update (<?php echo htmlspecialchars(strtoupper($role)); ?>)</h6>
                <?php if ($role === 'supplier'): ?>
                    <p class="text-muted mb-0">Suppliers can only view the status of their transactions.</p>
                <?php else: ?>
                    <form method="post" novalidate>
                        <?php if ($role === 'procurement'): ?>
                            <div class="mb-3">
                                <label class="form-label">Procurement Status</label>
                                <select name="proc_status" class="form-control">
                                    <?php
                                    $currentProcStatus = $transaction['proc_status'] ?? '';
                                    // Empty placeholder
                                    echo '<option value="">-- Select status --</option>';
                                    $procOptions = ['FOR SUPPLY REVIEW', 'COMPLETED'];
                                    foreach ($procOptions as $opt) {
                                        $selected = ($currentProcStatus === $opt) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($opt) . '" ' . $selected . '>' . htmlspecialchars($opt) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Procurement Remarks</label>
                                <textarea name="proc_remarks" class="form-control"
                                          rows="2"><?php echo htmlspecialchars($transaction['proc_remarks'] ?? ''); ?></textarea>
                            </div>
                        <?php elseif ($role === 'supply'): ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Delivery Receipt</label>
                                    <input type="text" name="supply_delivery_receipt" class="form-control"
                                           value="<?php echo htmlspecialchars($transaction['supply_delivery_receipt'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sales Invoice</label>
                                    <input type="text" name="supply_sales_invoice" class="form-control"
                                           value="<?php echo htmlspecialchars($transaction['supply_sales_invoice'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Supply Status</label>
                                <select name="supply_status" class="form-control">
                                    <?php
                                    $currentSupplyStatus = $transaction['supply_status'] ?? '';
                                    // Empty placeholder
                                    echo '<option value="">-- Select status --</option>';
                                    $supplyOptions = ['PARTIAL DELIVER', 'COMPLETED'];
                                    foreach ($supplyOptions as $opt) {
                                        $selected = ($currentSupplyStatus === $opt) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($opt) . '" ' . $selected . '>' . htmlspecialchars($opt) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Supply Remarks</label>
                                <textarea name="supply_remarks" class="form-control"
                                          rows="2"><?php echo htmlspecialchars($transaction['supply_remarks'] ?? ''); ?></textarea>
                            </div>
                        <?php elseif ($role === 'accounting'): ?>
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="stage" id="stagePre"
                                           value="pre" checked>
                                    <label class="form-check-label" for="stagePre">Pre-Budget</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="stage" id="stagePost"
                                           value="post">
                                    <label class="form-check-label" for="stagePost">Post-Budget</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Accounting Status</label>
                                <div class="form-check">
                                    <input class="form-check-input acct-pre-field" type="checkbox" id="acctPreStatus"
                                           name="acct_pre_status" value="PRE-BUDGET FOR VOUCHER"
                                        <?php echo ($transaction['acct_pre_status'] ?? '') === 'PRE-BUDGET FOR VOUCHER' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="acctPreStatus">
                                        PRE-BUDGET FOR VOUCHER
                                    </label>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input acct-post-field" type="checkbox" id="acctPostStatus"
                                           name="acct_post_status" value="POST BUDGET FOR VOUCHER"
                                        <?php echo ($transaction['acct_post_status'] ?? '') === 'POST BUDGET FOR VOUCHER' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="acctPostStatus">
                                        POST BUDGET FOR VOUCHER
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Accounting Remarks</label>
                                <small class="text-muted d-block mb-1">Pre-Budget</small>
                                <textarea name="acct_pre_remarks" class="form-control acct-pre-field"
                                          rows="2"
                                          placeholder="Pre-Budget remarks"><?php echo htmlspecialchars($transaction['acct_pre_remarks'] ?? ''); ?></textarea>
                                <small class="text-muted d-block mt-2 mb-1">Post-Budget</small>
                                <textarea name="acct_post_remarks" class="form-control mt-0 acct-post-field"
                                          rows="2"
                                          placeholder="Post-Budget remarks"><?php echo htmlspecialchars($transaction['acct_post_remarks'] ?? ''); ?></textarea>
                            </div>
                        <?php elseif ($role === 'budget'): ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">DV Number</label>
                                    <input type="text" name="budget_dv_number" class="form-control"
                                           value="<?php echo htmlspecialchars($transaction['budget_dv_number'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">DV Date</label>
                                    <input type="date" name="budget_dv_date" class="form-control"
                                           value="<?php echo htmlspecialchars($transaction['budget_dv_date'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Budget Status</label>
                                <select name="budget_status" class="form-control">
                                    <?php
                                    $currentBudgetStatus = $transaction['budget_status'] ?? '';
                                    // Empty placeholder option
                                    echo '<option value="">-- Select status --</option>';
                                    $budgetStatusOptions = ['FOR PAYMENT', 'ACCOUNTS PAYABLE', 'FOR ORS'];
                                    foreach ($budgetStatusOptions as $opt) {
                                        $selected = ($currentBudgetStatus === $opt) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($opt) . '" ' . $selected . '>' . htmlspecialchars($opt) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Demandability</label>
                                <select name="budget_demandability" class="form-control">
                                    <?php
                                    $currentDemand = $transaction['budget_demandability'] ?? '';
                                    // Empty placeholder option
                                    echo '<option value="">-- Select demandability --</option>';
                                    $demandOptions = ['DUE DEMANDABLE', 'NOT YET DEMANDABLE'];
                                    foreach ($demandOptions as $opt) {
                                        $selected = ($currentDemand === $opt) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($opt) . '" ' . $selected . '>' . htmlspecialchars($opt) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Budget Remarks</label>
                                <textarea name="budget_remarks" class="form-control"
                                          rows="2"><?php echo htmlspecialchars($transaction['budget_remarks'] ?? ''); ?></textarea>
                            </div>
                        <?php elseif ($role === 'cashier'): ?>
                            <div class="mb-3">
                                <label class="form-label">Cashier Status</label>
                                <select name="cashier_status" class="form-control">
                                    <?php
                                    $currentCashierStatus = $transaction['cashier_status'] ?? '';
                                    // Empty placeholder
                                    echo '<option value="">-- Select status --</option>';
                                    $cashierOptions = [
                                        'FOR ACIL',
                                        'FOR APPROVAL ASDS/SDS',
                                        'FOR OR INSUANCE',
                                        'ON PROCESS PAYMENT',
                                    ];
                                    foreach ($cashierOptions as $opt) {
                                        $selected = ($currentCashierStatus === $opt) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($opt) . '" ' . $selected . '>' . htmlspecialchars($opt) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Cashier Remarks</label>
                                <textarea name="cashier_remarks" class="form-control"
                                          rows="2"><?php echo htmlspecialchars($transaction['cashier_remarks'] ?? ''); ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">OR Number</label>
                                    <input type="text" name="cashier_or_number" class="form-control"
                                           value="<?php echo htmlspecialchars($transaction['cashier_or_number'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">OR Date</label>
                                    <input type="date" name="cashier_or_date" class="form-control"
                                           value="<?php echo htmlspecialchars($transaction['cashier_or_date'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Landbank Reference</label>
                                    <input type="text" name="cashier_landbank_ref" class="form-control"
                                           value="<?php echo htmlspecialchars($transaction['cashier_landbank_ref'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Payment Date</label>
                                    <input type="date" name="cashier_payment_date" class="form-control"
                                           value="<?php echo htmlspecialchars($transaction['cashier_payment_date'] ?? ''); ?>">
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($role !== 'supplier'): ?>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Save Updates</button>
                            </div>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="mb-3">
                    <i class="fas fa-stream me-2"></i>Flow Timeline
                </h6>
                <div class="timeline">
                    <!-- Procurement -->
                    <div class="timeline-item <?php echo !empty($transaction['proc_status']) ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo !empty($transaction['proc_status']) ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Procurement</h6>
                            <p class="timeline-status">
                                <?php echo htmlspecialchars($transaction['proc_status'] ?? 'Pending'); ?>
                            </p>
                            <?php if ($transaction['proc_date']): ?>
                                <p class="timeline-meta">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <span>Created: <?php echo date('m/d/Y H:i:s', strtotime($transaction['created_at'])); ?></span>
                                </p>
                            <?php endif; ?>
                            <?php if ($transaction['proc_remarks']): ?>
                                <p class="timeline-remarks"><?php echo htmlspecialchars($transaction['proc_remarks']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Supply Unit -->
                    <div class="timeline-item <?php echo !empty($transaction['supply_status']) ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo !empty($transaction['supply_status']) ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Supply Unit</h6>
                            <p class="timeline-status">
                                <?php echo htmlspecialchars($transaction['supply_status'] ?? 'Pending'); ?>
                            </p>
                            <?php if ($transaction['supply_date']): ?>
                                <p class="timeline-meta">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <span>Created: <?php echo date('m/d/Y H:i:s', strtotime($transaction['created_at'])); ?></span>
                                </p>
                            <?php endif; ?>
                            <?php if ($transaction['supply_remarks']): ?>
                                <p class="timeline-remarks"><?php echo htmlspecialchars($transaction['supply_remarks']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Accounting (Pre-Budget) -->
                    <div class="timeline-item <?php echo !empty($transaction['acct_pre_status']) ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo !empty($transaction['acct_pre_status']) ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Accounting (Pre-Budget)</h6>
                            <p class="timeline-status">
                                <?php echo htmlspecialchars($transaction['acct_pre_status'] ?? 'Pending'); ?>
                            </p>
                            <?php if ($transaction['acct_pre_date']): ?>
                                <p class="timeline-meta">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <span>Created: <?php echo date('m/d/Y H:i:s', strtotime($transaction['created_at'])); ?></span>
                                </p>
                            <?php endif; ?>
                            <?php if ($transaction['acct_pre_remarks']): ?>
                                <p class="timeline-remarks"><?php echo htmlspecialchars($transaction['acct_pre_remarks']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Budget Unit -->
                    <div class="timeline-item <?php echo !empty($transaction['budget_status']) ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo !empty($transaction['budget_status']) ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Budget Unit</h6>
                            <p class="timeline-status">
                                <?php echo htmlspecialchars($transaction['budget_status'] ?? 'Pending'); ?>
                            </p>
                            <?php if ($transaction['budget_dv_number']): ?>
                                <p class="timeline-meta"><i class="fas fa-file-invoice me-1"></i>DV #: <?php echo htmlspecialchars($transaction['budget_dv_number']); ?></p>
                            <?php endif; ?>
                            <?php if ($transaction['budget_dv_date']): ?>
                                <p class="timeline-meta">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <span>Created: <?php echo date('m/d/Y H:i:s', strtotime($transaction['created_at'])); ?></span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Accounting (Post-Budget) -->
                    <div class="timeline-item <?php echo !empty($transaction['acct_post_status']) ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo !empty($transaction['acct_post_status']) ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Accounting (Post-Budget)</h6>
                            <p class="timeline-status">
                                <?php echo htmlspecialchars($transaction['acct_post_status'] ?? 'Pending'); ?>
                            </p>
                            <?php if ($transaction['acct_post_date']): ?>
                                <p class="timeline-meta">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <span>Created: <?php echo date('m/d/Y H:i:s', strtotime($transaction['created_at'])); ?></span>
                                </p>
                            <?php endif; ?>
                            <?php if ($transaction['acct_post_remarks']): ?>
                                <p class="timeline-remarks"><?php echo htmlspecialchars($transaction['acct_post_remarks']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Cashier -->
                    <div class="timeline-item <?php echo !empty($transaction['cashier_status']) ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo !empty($transaction['cashier_status']) ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Cashier</h6>
                            <p class="timeline-status">
                                <?php echo htmlspecialchars($transaction['cashier_status'] ?? 'Pending'); ?>
                            </p>
                            <?php if ($transaction['cashier_or_number']): ?>
                                <p class="timeline-meta"><i class="fas fa-receipt me-1"></i>OR #: <?php echo htmlspecialchars($transaction['cashier_or_number']); ?></p>
                            <?php endif; ?>
                            <?php if ($transaction['cashier_payment_date']): ?>
                                <p class="timeline-meta">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <span>Created: <?php echo date('m/d/Y H:i:s', strtotime($transaction['created_at'])); ?></span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($role === 'cashier'): ?>
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2">Proceed to Landbank</h6>
                    <?php if ($canProceedLandbank): ?>
                        <p class="small text-muted">
                            All requirements from Procurement, Supply, Accounting, and Budget are complete.
                        </p>
                        <a href="<?php echo htmlspecialchars(LANDBANK_URL); ?>" target="_blank"
                           class="btn btn-success w-100">
                            Proceed to Landbank Site
                        </a>
                    <?php else: ?>
                        <div class="alert alert-warning small mb-2">
                            Cannot proceed yet. Please ensure:
                            <ul class="mb-0">
                                <li>Procurement and Supply statuses are set and not pending.</li>
                                <li>Accounting (Pre-Budget) completed and forwarded.</li>
                                <li>Budget DV number and date are filled in.</li>
                                <li>Accounting (Post-Budget) status is "FOR PAYMENT" or "FOR CASHIER – PAYMENT PROCESSING".</li>
                            </ul>
                        </div>
                        <button class="btn btn-secondary w-100" type="button" disabled>Proceed to Landbank Site</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Periodically refresh only the Flow Timeline without reloading the whole page
document.addEventListener('DOMContentLoaded', function () {
    const refreshIntervalMs = 5000; // 5 seconds

    function refreshTimeline() {
        const currentTimeline = document.querySelector('.timeline');
        if (!currentTimeline) return;

        // Fetch the same page and extract the updated timeline HTML
        fetch(window.location.href, { cache: 'no-store' })
            .then(function (response) { return response.text(); })
            .then(function (html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTimeline = doc.querySelector('.timeline');
                if (newTimeline) {
                    currentTimeline.innerHTML = newTimeline.innerHTML;
                }
            })
            .catch(function () {
                // Fail silently if refresh fails; do not break the page
            });
    }

    setInterval(refreshTimeline, refreshIntervalMs);

    // Accounting: only allow editing fields for the selected stage (Pre vs Post)
    function updateAccountingFields() {
        const selectedStage = document.querySelector('input[name="stage"]:checked');
        if (!selectedStage) return;

        const isPre = selectedStage.value === 'pre';

        document.querySelectorAll('.acct-pre-field').forEach(function (el) {
            // keep value & checkmark visible, just make it non-interactive when inactive
            if (el.tagName === 'TEXTAREA') {
                el.readOnly = !isPre;
            }
            el.classList.toggle('acct-readonly', !isPre);
        });
        document.querySelectorAll('.acct-post-field').forEach(function (el) {
            if (el.tagName === 'TEXTAREA') {
                el.readOnly = isPre;
            }
            el.classList.toggle('acct-readonly', isPre);
        });
    }

    // Attach listeners only if the Accounting form is present
    if (document.querySelector('input[name="stage"]')) {
        document.querySelectorAll('input[name="stage"]').forEach(function (radio) {
            radio.addEventListener('change', updateAccountingFields);
        });
        // Initialize state on load
        updateAccountingFields();
    }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
