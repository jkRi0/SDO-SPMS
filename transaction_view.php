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

$updatesByStage = [
    'procurement' => [],
    'supply' => [],
    'accounting_pre' => [],
    'accounting_post' => [],
    'budget' => [],
    'cashier' => [],
];

try {
    $logStmt = $db->prepare('SELECT transaction_id, stage, status, remarks, created_at FROM transaction_updates WHERE transaction_id = ? ORDER BY created_at ASC');
    $logStmt->execute([$id]);
    $logs = $logStmt->fetchAll();
    foreach ($logs as $log) {
        $stageKey = $log['stage'];
        if (isset($updatesByStage[$stageKey])) {
            $updatesByStage[$stageKey][] = $log;
        }
    }
} catch (Exception $e) {
    // If history table doesn't exist yet, just skip history without breaking the page
}

$error = '';
$success = '';
if (isset($_GET['updated']) && $_GET['updated'] === '1') {
    $success = 'Transaction updated successfully.';
}

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

// Helper to compute elapsed time in fixed format: 00 d : 00 h : 00 m
if (!function_exists('format_elapsed_time')) {
    function format_elapsed_time($seconds)
    {
        $seconds = (int)$seconds;
        if ($seconds < 0) {
            $seconds = 0;
        }
        $days = intdiv($seconds, 86400);
        $seconds %= 86400;
        $hours = intdiv($seconds, 3600);
        $seconds %= 3600;
        $minutes = intdiv($seconds, 60);
        $seconds %= 60;

        return sprintf('%02d : %02d : %02d : %02d ', $days, $hours, $minutes, $seconds);
    }
}

// Helper to get timestamp of latest update in a given stage from $updatesByStage
if (!function_exists('get_last_stage_timestamp')) {
    function get_last_stage_timestamp(array $updatesByStage, $stage)
    {
        if (empty($updatesByStage[$stage])) {
            return null;
        }
        $logs = $updatesByStage[$stage];
        $last = $logs[count($logs) - 1];
        $ts = strtotime($last['created_at']);
        return $ts !== false ? $ts : null;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $fields = [];
        $params = [];

        $logStage = null;
        $logStatus = '';
        $logRemarks = '';

        if ($role === 'procurement') {
            $fields[] = 'proc_status = ?';
            $fields[] = 'proc_remarks = ?';
            $fields[] = 'proc_date = CURDATE()';
            $params[] = trim($_POST['proc_status'] ?? '');
            $params[] = trim($_POST['proc_remarks'] ?? '');
            $logStage = 'procurement';
            $logStatus = $params[0];
            $logRemarks = $params[1];
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
            $logStage = 'supply';
            $logStatus = $params[0];
            $logRemarks = $params[3];
        } elseif ($role === 'accounting') {
            // Single Accounting form: status (For Voucher checkbox), remarks, DV amount
            $acctStatus      = isset($_POST['acct_status']) ? trim($_POST['acct_status']) : '';
            $acctRemarksBase = trim($_POST['acct_remarks'] ?? '');
            $acctDvAmount    = trim($_POST['acct_dv_amount'] ?? '');

            // Combine remarks + DV amount for storage / history
            $combinedRemarks = $acctRemarksBase;
            if ($acctDvAmount !== '') {
                if ($combinedRemarks !== '') {
                    $combinedRemarks .= "\n";
                }
                $combinedRemarks .= 'DV Amount: ' . $acctDvAmount;
            }

            // Decide automatically: before Budget is done = pre-budget; after Budget is done = post-budget
            $budgetDone = !empty($transaction['budget_status'])
                || !empty($transaction['budget_dv_number'])
                || !empty($transaction['budget_dv_date']);

            if (!$budgetDone) {
                // Pre-budget accounting update
                $fields[] = 'acct_pre_status = ?';
                $fields[] = 'acct_pre_remarks = ?';
                $fields[] = 'acct_pre_date = CURDATE()';
                $params[] = $acctStatus;
                $params[] = $combinedRemarks;
                $logStage = 'accounting_pre';
                $logStatus = $acctStatus;
                $logRemarks = $combinedRemarks;
            } else {
                // Post-budget accounting update
                $fields[] = 'acct_post_status = ?';
                $fields[] = 'acct_post_remarks = ?';
                $fields[] = 'acct_post_date = CURDATE()';
                $params[] = $acctStatus;
                $params[] = $combinedRemarks;
                $logStage = 'accounting_post';
                $logStatus = $acctStatus;
                $logRemarks = $combinedRemarks;
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
            $logStage = 'budget';
            $logStatus = $params[2];
            $logRemarks = $params[4];
        } elseif ($role === 'cashier') {
            $fields[] = 'cashier_status = ?';
            $fields[] = 'cashier_remarks = ?';
            $fields[] = 'cashier_or_number = ?';
            $fields[] = 'cashier_or_date = ?';
            $fields[] = 'cashier_landbank_ref = ?';
            $fields[] = 'cashier_payment_date = ?';

            $cashierStatus  = trim($_POST['cashier_status'] ?? '');
            $cashierRemarks = trim($_POST['cashier_remarks'] ?? '');
            $cashierAmount  = trim($_POST['cashier_landbank_ref'] ?? '');
            $cashierOrNum   = trim($_POST['cashier_or_number'] ?? '');
            $cashierOrDate  = trim($_POST['cashier_or_date'] ?? '');
            $cashierPayDate = trim($_POST['cashier_payment_date'] ?? '');

            // Store raw values on the transaction
            $params[] = $cashierStatus;
            $params[] = $cashierRemarks;
            $params[] = $cashierOrNum;
            $params[] = $cashierOrDate;
            $params[] = $cashierAmount;
            $params[] = $cashierPayDate;

            // For history/timeline, append Amount as its own line (if provided)
            $logStage = 'cashier';
            $logStatus = $cashierStatus;
            $logRemarks = $cashierRemarks;
            if ($cashierAmount !== '') {
                if ($logRemarks !== '') {
                    $logRemarks .= "\n";
                }
                $logRemarks .= 'Amount: ' . $cashierAmount;
            }
        }

        if ($fields) {
            $params[] = $id;
            $sql = 'UPDATE transactions SET ' . implode(', ', $fields) . ' WHERE id = ?';
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            // Record history if table exists and there is something meaningful to log
            if ($logStage && ($logStatus !== '' || $logRemarks !== '')) {
                try {
                    $histStmt = $db->prepare('INSERT INTO transaction_updates (transaction_id, stage, status, remarks, created_at) VALUES (?, ?, ?, ?, NOW())');
                    $histStmt->execute([$id, $logStage, $logStatus, $logRemarks]);
                } catch (Exception $e) {
                    // Silently ignore logging errors so they don't break main update
                }
            }

            // Redirect to avoid duplicate submissions on refresh (Post/Redirect/Get)
            header('Location: transaction_view.php?id=' . $id . '&updated=1');
            exit;
        }
    } catch (Exception $e) {
        $error = 'Error updating transaction.';
    }
}

// Determine if cashier can proceed to Landbank
// Temporarily allow cashier to always proceed (no status/DV verification)
$canProceedLandbank = ($role === 'cashier');

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
                            <p class="info-label">PO (Gross Amount)</p>
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

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div class="info-content">
                            <p class="info-label">Expected Date</p>
                            <p class="info-value">
                                <?php
                                $expectedText = $transaction['expected_date'] ?? '';
                                echo $expectedText !== '' ? htmlspecialchars($expectedText) : '—';
                                ?>
                            </p>
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
                                    <textarea name="supply_delivery_receipt" class="form-control" rows="2"><?php echo htmlspecialchars($transaction['supply_delivery_receipt'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sales Invoice</label>
                                    <textarea name="supply_sales_invoice" class="form-control" rows="2"><?php echo htmlspecialchars($transaction['supply_sales_invoice'] ?? ''); ?></textarea>
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
                            <div class="border rounded p-3 mb-2">
                                <h6 class="mb-2">Accounting</h6>
                                <div class="mb-2">
                                    <label class="form-label mb-1">Status</label>
                                    <div class="form-check">
                                        <?php
                                        $currentAcctStatus = ($transaction['acct_post_status'] ?: $transaction['acct_pre_status']) ?? '';
                                        $isCheckedAcct = (strtoupper(trim($currentAcctStatus)) === 'FOR VOUCHER');
                                        ?>
                                        <input class="form-check-input" type="checkbox" id="acctStatus"
                                               name="acct_status" value="FOR VOUCHER" <?php echo $isCheckedAcct ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="acctStatus">For Voucher</label>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label mb-1">Remarks</label>
                                    <textarea name="acct_remarks" class="form-control" rows="2"
                                              placeholder="Enter accounting remarks"></textarea>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label mb-1">DV Amount</label>
                                    <input type="number" step="0.01" min="0" name="acct_dv_amount" class="form-control"
                                           placeholder="Enter DV amount (net)">
                                </div>
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
                                    $demandOptions = ['Due and Demandable', 'Not Yet Due and Demandable'];
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
                                    $cashierStatusOptions = ['For ACIC', 'For OR Issuance', 'For SDS/ASDS Approval'];
                                    foreach ($cashierStatusOptions as $opt) {
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
                                    <label class="form-label">ACIC Number</label>
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
                                    <label class="form-label">Amount</label>
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
                            <?php
                            // Elapsed from transaction created_at to latest Procurement update
                            $elapsedProc = '';
                            if (!empty($updatesByStage['procurement'])) {
                                $logsProc  = $updatesByStage['procurement'];
                                $lastProc  = $logsProc[count($logsProc) - 1];
                                $lastTs    = strtotime($lastProc['created_at']);
                                $createdTs = strtotime($transaction['created_at']);
                                if ($lastTs !== false && $createdTs !== false && $lastTs >= $createdTs) {
                                    $elapsedProc = format_elapsed_time($lastTs - $createdTs);
                                }
                            }
                            ?>
                            <h6 class="timeline-title d-flex justify-content-between align-items-center">
                                <span>Procurement</span>
                                <span class="small text-muted"><?php echo $elapsedProc ? htmlspecialchars($elapsedProc) : ''; ?></span>
                            </h6>

                            <?php if (!empty($updatesByStage['procurement'])): ?>
                                <div class="timeline-history mt-2 p-2 border rounded bg-white">
                                    <div class="small text-muted mb-1">Update history</div>
                                    <?php $countProc = count($updatesByStage['procurement']); ?>
                                    <?php foreach ($updatesByStage['procurement'] as $idx => $u): ?>
                                        <?php
                                        $isLatest = ($idx === $countProc - 1);
                                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');
                                        ?>
                                        <div class="<?php echo $rowClass; ?>">
                                            <div class="text-muted">
                                                <?php echo date('m/d/Y H:i:s', strtotime($u['created_at'])); ?>
                                            </div>
                                            <?php if ($u['status'] !== ''): ?>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($u['status']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($u['remarks'] !== ''): ?>
                                                <div><?php echo nl2br(htmlspecialchars($u['remarks'])); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Supply Unit -->
                    <div class="timeline-item <?php echo !empty($transaction['supply_status']) ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo !empty($transaction['supply_status']) ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <?php
                            // Elapsed from latest Procurement update to latest Supply update
                            $elapsedSupply = '';
                            $prevTs = get_last_stage_timestamp($updatesByStage, 'procurement');
                            $currTs = get_last_stage_timestamp($updatesByStage, 'supply');
                            if ($prevTs !== null && $currTs !== null && $currTs >= $prevTs) {
                                $elapsedSupply = format_elapsed_time($currTs - $prevTs);
                            }
                            ?>
                            <h6 class="timeline-title d-flex justify-content-between align-items-center">
                                <span>Supply Unit</span>
                                <span class="small text-muted"><?php echo $elapsedSupply ? htmlspecialchars($elapsedSupply) : ''; ?></span>
                            </h6>

                            <?php if (!empty($updatesByStage['supply'])): ?>
                                <div class="timeline-history mt-2 p-2 border rounded bg-white">
                                    <div class="small text-muted mb-1">Update history</div>
                                    <?php $countSupply = count($updatesByStage['supply']); ?>
                                    <?php foreach ($updatesByStage['supply'] as $idx => $u): ?>
                                        <?php
                                        $isLatest = ($idx === $countSupply - 1);
                                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');
                                        ?>
                                        <div class="<?php echo $rowClass; ?>">
                                            <div class="text-muted">
                                                <?php echo date('m/d/Y H:i:s', strtotime($u['created_at'])); ?>
                                            </div>
                                            <?php if ($u['status'] !== ''): ?>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($u['status']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($u['remarks'] !== ''): ?>
                                                <div><?php echo nl2br(htmlspecialchars($u['remarks'])); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Accounting -->
                    <div class="timeline-item <?php echo !empty($updatesByStage['accounting_pre']) ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo !empty($updatesByStage['accounting_pre']) ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <?php
                            // Elapsed from latest Supply update to latest Accounting update (pre-budget)
                            $elapsedAcctPre = '';
                            $prevTs = get_last_stage_timestamp($updatesByStage, 'supply');
                            $currTs = get_last_stage_timestamp($updatesByStage, 'accounting_pre');
                            if ($prevTs !== null && $currTs !== null && $currTs >= $prevTs) {
                                $elapsedAcctPre = format_elapsed_time($currTs - $prevTs);
                            }
                            ?>
                            <h6 class="timeline-title d-flex justify-content-between align-items-center">
                                <span>Accounting</span>
                                <span class="small text-muted"><?php echo $elapsedAcctPre ? htmlspecialchars($elapsedAcctPre) : ''; ?></span>
                            </h6>

                            <?php if (!empty($updatesByStage['accounting_pre'])): ?>
                                <div class="timeline-history mt-2 p-2 border rounded bg-white">
                                    <div class="small text-muted mb-1">Update history</div>
                                    <?php $countAcctPre = count($updatesByStage['accounting_pre']); ?>
                                    <?php foreach ($updatesByStage['accounting_pre'] as $idx => $u): ?>
                                        <?php
                                        $isLatest = ($idx === $countAcctPre - 1);
                                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');
                                        ?>
                                        <div class="<?php echo $rowClass; ?>">
                                            <div class="text-muted">
                                                <?php echo date('m/d/Y H:i:s', strtotime($u['created_at'])); ?>
                                            </div>
                                            <?php if ($u['status'] !== ''): ?>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($u['status']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($u['remarks'] !== ''): ?>
                                                <div><?php echo nl2br(htmlspecialchars($u['remarks'])); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Budget Unit -->
                    <div class="timeline-item <?php echo !empty($transaction['budget_status']) ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo !empty($transaction['budget_status']) ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <?php
                            // Elapsed from latest Accounting (Pre-Budget) update to latest Budget update
                            $elapsedBudget = '';
                            $prevTs = get_last_stage_timestamp($updatesByStage, 'accounting_pre');
                            $currTs = get_last_stage_timestamp($updatesByStage, 'budget');
                            if ($prevTs !== null && $currTs !== null && $currTs >= $prevTs) {
                                $elapsedBudget = format_elapsed_time($currTs - $prevTs);
                            }
                            ?>
                            <h6 class="timeline-title d-flex justify-content-between align-items-center">
                                <span>Budget Unit</span>
                                <span class="small text-muted"><?php echo $elapsedBudget ? htmlspecialchars($elapsedBudget) : ''; ?></span>
                            </h6>
                            <?php if ($transaction['budget_dv_number']): ?>
                                <p class="timeline-meta"><i class="fas fa-file-invoice me-1"></i>DV #: <?php echo htmlspecialchars($transaction['budget_dv_number']); ?></p>
                            <?php endif; ?>

                            <?php if (!empty($updatesByStage['budget'])): ?>
                                <div class="timeline-history mt-2 p-2 border rounded bg-white">
                                    <div class="small text-muted mb-1">Update history</div>
                                    <?php $countBudget = count($updatesByStage['budget']); ?>
                                    <?php foreach ($updatesByStage['budget'] as $idx => $u): ?>
                                        <?php
                                        $isLatest = ($idx === $countBudget - 1);
                                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');
                                        ?>
                                        <div class="<?php echo $rowClass; ?>">
                                            <div class="text-muted">
                                                <?php echo date('m/d/Y H:i:s', strtotime($u['created_at'])); ?>
                                            </div>
                                            <?php if ($u['status'] !== ''): ?>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($u['status']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($u['remarks'] !== ''): ?>
                                                <div><?php echo nl2br(htmlspecialchars($u['remarks'])); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Accounting -->
                    <div class="timeline-item <?php echo !empty($updatesByStage['accounting_post']) ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo !empty($updatesByStage['accounting_post']) ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <?php
                            // Elapsed from latest Budget update to latest Accounting update (post-budget)
                            $elapsedAcctPost = '';
                            $prevTs = get_last_stage_timestamp($updatesByStage, 'budget');
                            $currTs = get_last_stage_timestamp($updatesByStage, 'accounting_post');
                            if ($prevTs !== null && $currTs !== null && $currTs >= $prevTs) {
                                $elapsedAcctPost = format_elapsed_time($currTs - $prevTs);
                            }
                            ?>
                            <h6 class="timeline-title d-flex justify-content-between align-items-center">
                                <span>Accounting</span>
                                <span class="small text-muted"><?php echo $elapsedAcctPost ? htmlspecialchars($elapsedAcctPost) : ''; ?></span>
                            </h6>

                            <?php if (!empty($updatesByStage['accounting_post'])): ?>
                                <div class="timeline-history mt-2 p-2 border rounded bg-white">
                                    <div class="small text-muted mb-1">Update history</div>
                                    <?php $countAcctPost = count($updatesByStage['accounting_post']); ?>
                                    <?php foreach ($updatesByStage['accounting_post'] as $idx => $u): ?>
                                        <?php
                                        $isLatest = ($idx === $countAcctPost - 1);
                                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');
                                        ?>
                                        <div class="<?php echo $rowClass; ?>">
                                            <div class="text-muted">
                                                <?php echo date('m/d/Y H:i:s', strtotime($u['created_at'])); ?>
                                            </div>
                                            <?php if ($u['status'] !== ''): ?>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($u['status']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($u['remarks'] !== ''): ?>
                                                <div><?php echo nl2br(htmlspecialchars($u['remarks'])); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Cashier -->
                    <div class="timeline-item <?php echo !empty($transaction['cashier_status']) ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo !empty($transaction['cashier_status']) ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <?php
                            // Elapsed from latest Accounting (Post-Budget) update to latest Cashier update
                            $elapsedCashier = '';
                            $prevTs = get_last_stage_timestamp($updatesByStage, 'accounting_post');
                            $currTs = get_last_stage_timestamp($updatesByStage, 'cashier');
                            if ($prevTs !== null && $currTs !== null && $currTs >= $prevTs) {
                                $elapsedCashier = format_elapsed_time($currTs - $prevTs);
                            }
                            ?>
                            <h6 class="timeline-title d-flex justify-content-between align-items-center">
                                <span>Cashier</span>
                                <span class="small text-muted"><?php echo $elapsedCashier ? htmlspecialchars($elapsedCashier) : ''; ?></span>
                            </h6>
                            <?php if (!empty($updatesByStage['cashier'])): ?>
                                <div class="timeline-history mt-2 p-2 border rounded bg-white">
                                    <div class="small text-muted mb-1">Update history</div>
                                    <?php $countCashier = count($updatesByStage['cashier']); ?>
                                    <?php foreach ($updatesByStage['cashier'] as $idx => $u): ?>
                                        <?php
                                        $isLatest = ($idx === $countCashier - 1);
                                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');
                                        ?>
                                        <div class="<?php echo $rowClass; ?>">
                                            <div class="text-muted">
                                                <?php echo date('m/d/Y H:i:s', strtotime($u['created_at'])); ?>
                                            </div>
                                            <?php if ($u['status'] !== ''): ?>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($u['status']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($u['remarks'] !== ''): ?>
                                                <div><?php echo nl2br(htmlspecialchars($u['remarks'])); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
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
                                <li>Initial Accounting review is completed and forwarded to Budget.</li>
                                <li>Budget DV number and date are filled in.</li>
                                <li>Final Accounting status is set and not "PENDING" or "FOR CORRECTION".</li>
                                <li>Cashier must finish internal payment processing before proceeding to the Landbank site.</li>
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

    // Accounting: keep a notion of which stage (pre vs post) was last edited for logging,
    // but allow editing of both sections at the same time.
    function setAccountingStage(stage) {
        var stageInput = document.getElementById('acctStage');
        if (!stageInput) return;
        stageInput.value = stage;
    }

    // Attach listeners only if the Accounting form is present
    if (document.getElementById('acctStage')) {
        // Default to pre on load
        setAccountingStage('pre');

        document.querySelectorAll('.acct-pre-field').forEach(function (el) {
            ['focus', 'click'].forEach(function (evt) {
                el.addEventListener(evt, function () { setAccountingStage('pre'); });
            });
        });
        document.querySelectorAll('.acct-post-field').forEach(function (el) {
            ['focus', 'click'].forEach(function (evt) {
                el.addEventListener(evt, function () { setAccountingStage('post'); });
            });
        });
    }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
