<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/email_helper.php';
require_once __DIR__ . '/dept_notifications.php';
require_once __DIR__ . '/audit.php';

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

$handoffGraceSeconds = 60;
$handoffOpen = null;
$handoffHistory = [];
$handoffExtras = [
    'procurement' => 0,
    'supply' => 0,
    'accounting_pre' => 0,
    'budget' => 0,
    'accounting_post' => 0,
    'cashier' => 0,
];

try {
    $db->exec('CREATE TABLE IF NOT EXISTS app_settings (
        setting_key VARCHAR(128) NOT NULL,
        setting_value VARCHAR(255) NOT NULL,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (setting_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');

    $stmtGrace = $db->prepare('SELECT setting_value FROM app_settings WHERE setting_key = ?');
    $stmtGrace->execute(['handoff_grace_seconds']);
    $graceVal = $stmtGrace->fetchColumn();
    if ($graceVal !== false && $graceVal !== null && $graceVal !== '') {
        $handoffGraceSeconds = max(0, (int)$graceVal);
    }
} catch (Exception $e) {
}

try {
    $stmtCol = $db->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmtCol->execute(['transactions', 'supply_partial_delivery_date']);
    $hasPartialDate = (int)$stmtCol->fetchColumn() > 0;
    if (!$hasPartialDate) {
        $db->exec('ALTER TABLE transactions ADD COLUMN supply_partial_delivery_date DATE NULL DEFAULT NULL');
    }

    $stmtCol->execute(['transactions', 'supply_delivery_date']);
    $hasDeliveryDate = (int)$stmtCol->fetchColumn() > 0;
    if (!$hasDeliveryDate) {
        $db->exec('ALTER TABLE transactions ADD COLUMN supply_delivery_date DATE NULL DEFAULT NULL');
    }
} catch (Exception $e) {
}

try {
    $db->exec('CREATE TABLE IF NOT EXISTS transaction_handoffs (
        id INT(11) NOT NULL AUTO_INCREMENT,
        transaction_id INT(11) NOT NULL,
        from_dept VARCHAR(32) NOT NULL,
        to_dept VARCHAR(32) NOT NULL,
        forwarded_at DATETIME NOT NULL,
        received_at DATETIME NULL DEFAULT NULL,
        delay_seconds INT(11) NULL DEFAULT NULL,
        exceeded_grace TINYINT(1) NOT NULL DEFAULT 0,
        created_by_user_id INT(11) DEFAULT NULL,
        received_by_user_id INT(11) DEFAULT NULL,
        PRIMARY KEY (id),
        KEY idx_tx_open (transaction_id, received_at),
        KEY idx_tx_time (transaction_id, forwarded_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');

    $stmtOpen = $db->prepare('SELECT *,
                                     UNIX_TIMESTAMP(forwarded_at) AS forwarded_ts,
                                     UNIX_TIMESTAMP(NOW()) AS server_now_ts
                              FROM transaction_handoffs
                              WHERE transaction_id = ? AND received_at IS NULL
                              ORDER BY forwarded_at DESC
                              LIMIT 1');
    $stmtOpen->execute([$id]);
    $handoffOpen = $stmtOpen->fetch(PDO::FETCH_ASSOC);

    $stmtAll = $db->prepare('SELECT *
                             FROM transaction_handoffs
                             WHERE transaction_id = ?
                             ORDER BY forwarded_at ASC, id ASC');
    $stmtAll->execute([$id]);
    $handoffHistory = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

    // Attribute only the overdue beyond grace to both departments involved in a handoff.
    // Compute directly from forwarded_at/received_at to ensure consistency with the UI.
    foreach ($handoffHistory as $h) {
        if (empty($h['received_at']) || empty($h['forwarded_at'])) {
            continue;
        }
        $from = (string)($h['from_dept'] ?? '');
        $to = (string)($h['to_dept'] ?? '');
        $forwardTs = strtotime((string)$h['forwarded_at']);
        $recvTs = strtotime((string)$h['received_at']);
        if ($forwardTs === false || $recvTs === false || $recvTs < $forwardTs) {
            continue;
        }

        $delaySecs = (int)($recvTs - $forwardTs);
        $overdue = max(0, $delaySecs - (int)$handoffGraceSeconds);
        if ($overdue <= 0) {
            continue;
        }
        if (isset($handoffExtras[$from])) {
            $handoffExtras[$from] += $overdue;
        }
        if (isset($handoffExtras[$to])) {
            $handoffExtras[$to] += $overdue;
        }
    }
} catch (Exception $e) {
    $handoffOpen = null;
}

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
if (isset($_GET['notified']) && $_GET['notified'] === '1') {
    $success = 'Supplier has been notified.';
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

if (!function_exists('get_handoff_timestamp')) {
    function get_handoff_timestamp(array $handoffHistory, string $fromDept, string $toDept, string $field): ?int
    {
        $last = null;
        foreach ($handoffHistory as $h) {
            if ((string)($h['from_dept'] ?? '') !== $fromDept || (string)($h['to_dept'] ?? '') !== $toDept) {
                continue;
            }
            if (empty($h[$field])) {
                continue;
            }
            $ts = strtotime((string)$h[$field]);
            if ($ts !== false) {
                $last = $ts;
            }
        }
        return $last;
    }
}

if (!function_exists('get_handoff_timestamp_first')) {
    function get_handoff_timestamp_first(array $handoffHistory, string $fromDept, string $toDept, string $field): ?int
    {
        foreach ($handoffHistory as $h) {
            if ((string)($h['from_dept'] ?? '') !== $fromDept || (string)($h['to_dept'] ?? '') !== $toDept) {
                continue;
            }
            if (empty($h[$field])) {
                continue;
            }
            $ts = strtotime((string)$h[$field]);
            if ($ts !== false) {
                return $ts;
            }
        }
        return null;
    }
}

if (!function_exists('render_handoff_between')) {
    function render_handoff_between(string $fromDept, string $toDept, array $handoffHistory, int $handoffGraceSeconds): void
    {
        $filtered = [];
        foreach ($handoffHistory as $h) {
            if ((string)($h['from_dept'] ?? '') === $fromDept && (string)($h['to_dept'] ?? '') === $toDept) {
                $filtered[] = $h;
            }
        }
        if (empty($filtered)) {
            return;
        }
        $fromLabel = strtoupper($fromDept);
        $toLabel = strtoupper($toDept);
        $countH = count($filtered);
        ?>
        <div class="timeline-item completed handoff-between">
            <div class="timeline-marker">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="timeline-content">
                <h6 class="timeline-title d-flex justify-content-between align-items-center">
                    <span>Handoff: <?php echo htmlspecialchars($fromLabel . ' → ' . $toLabel); ?></span>
                    <span class="small text-muted"></span>
                </h6>
                <div class="timeline-history mt-1 p-2 border rounded bg-white">
                    <?php foreach ($filtered as $idx => $h): ?>
                        <?php
                        $isLatest = ($idx === $countH - 1);
                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');
                        $forwardTs = !empty($h['forwarded_at']) ? strtotime((string)$h['forwarded_at']) : false;
                        $recvTs = !empty($h['received_at']) ? strtotime((string)$h['received_at']) : false;
                        $forwardedAt = $forwardTs !== false ? date('m/d/Y H:i:s', $forwardTs) : '';
                        $receivedAt = $recvTs !== false ? date('m/d/Y H:i:s', $recvTs) : '';

                        $delaySecs = 0;
                        if ($forwardTs !== false) {
                            $endTs = $recvTs !== false ? $recvTs : time();
                            $delaySecs = max(0, (int)$endTs - (int)$forwardTs);
                        }
                        $overdueSecs = max(0, (int)$delaySecs - (int)$handoffGraceSeconds);
                        ?>
                        <div class="<?php echo $rowClass; ?>">
                            <?php if ($forwardedAt !== ''): ?>
                                <div class="text-muted"><?php echo htmlspecialchars($forwardedAt); ?></div>
                            <?php endif; ?>
                            <div class="fw-semibold">FORWARDED</div>
                            <?php if ($receivedAt !== ''): ?>
                                <div class="text-muted mt-1"><?php echo htmlspecialchars($receivedAt); ?></div>
                                <div class="fw-semibold">RECEIVED</div>
                            <?php else: ?>
                                <div class="text-muted mt-1">Pending receive</div>
                            <?php endif; ?>

                            <?php if ($overdueSecs > 0): ?>
                                <div class="text-danger fw-semibold mt-1">Overdue: <?php echo htmlspecialchars(format_elapsed_time($overdueSecs)); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($role === 'admin' && isset($_POST['action']) && (string)($_POST['action'] ?? '') === 'set_handoff_grace') {
        $newGrace = isset($_POST['handoff_grace_seconds']) ? (int)$_POST['handoff_grace_seconds'] : 0;
        if ($newGrace < 0) {
            $newGrace = 0;
        }
        try {
            $stmtSet = $db->prepare('INSERT INTO app_settings (setting_key, setting_value) VALUES (?, ?)
                                     ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
            $stmtSet->execute(['handoff_grace_seconds', (string)$newGrace]);
        } catch (Exception $e) {
        }

        header('Location: transaction_view.php?id=' . (int)$id . '&grace_updated=1');
        exit;
    }

    // Special action: cashier clicking "Notify Supplier" (no transaction field updates)
    if ($role === 'cashier' && isset($_POST['notify_supplier']) && $_POST['notify_supplier'] === '1') {
        try {
            $notifyStmt = $db->prepare('INSERT INTO notifications (supplier_id, transaction_id, title, message, link, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $title = 'Payment status update';
            $defaultMessage = 'Your PO ' . ($transaction['po_number'] ?? '') . ' is now marked as COMPLETED. Please check the portal for details.';
            $message = trim((string)($_POST['notify_message'] ?? ''));
            if ($message === '') {
                $message = $defaultMessage;
            }
            $link = 'transaction_view.php?id=' . $transaction['id'];
            $notifyStmt->execute([
                $transaction['supplier_id'],
                $transaction['id'],
                $title,
                $message,
                $link,
            ]);

            // Also email supplier if an email address is available (e.g. Google OAuth suppliers)
            $emailStmt = $db->prepare('SELECT email FROM suppliers WHERE id = ? LIMIT 1');
            $emailStmt->execute([$transaction['supplier_id']]);
            $supplierRow = $emailStmt->fetch();

            if ($supplierRow && !empty($supplierRow['email'])) {
                $toEmail = strtolower(trim($supplierRow['email']));
                $emailSubject = $title;
                $emailBody = '<p>' . htmlspecialchars($message) . '</p>' .
                    '<p><a href="' . htmlspecialchars(BASE_URL . $link) . '">View details in the STMS portal</a></p>';
                send_supplier_email($toEmail, $emailSubject, $emailBody);
            }
        } catch (Exception $e) {
            // If notification insert fails, do not break the main page
        }

        try {
            create_log($db, $_SESSION['user_id'] ?? null, 'transaction_notify_supplier', 'transaction', (int)$transaction['id'], json_encode([
                'transaction_id' => (int)$transaction['id'],
                'po_number' => (string)($transaction['po_number'] ?? ''),
                'supplier_id' => (int)($transaction['supplier_id'] ?? 0),
                'message' => (string)$message,
            ]));
        } catch (Exception $e) {
        }

        // Redirect back so refresh does not re-send notification
        header('Location: transaction_view.php?id=' . $id . '&notified=1');
        exit;
    }

    // Enforce that only the current owning department can update (and only after receive if it was handed off).
    $budgetDoneForEdit = !empty($transaction['budget_status']) || !empty($transaction['budget_dv_number']) || !empty($transaction['budget_dv_date']);
    $deptForRoleEdit = [
        'procurement' => 'procurement',
        'supply' => 'supply',
        'accounting' => $budgetDoneForEdit ? 'accounting_post' : 'accounting_pre',
        'budget' => 'budget',
        'cashier' => 'cashier',
    ];
    $roleDeptEdit = $deptForRoleEdit[$role] ?? '';

    $workflowOrder = [
        'procurement' => 0,
        'supply' => 1,
        'accounting_pre' => 2,
        'budget' => 3,
        'accounting_post' => 4,
        'cashier' => 5,
    ];

    $ownerDeptEdit = 'procurement';
    if (!empty($handoffOpen) && !empty($handoffOpen['from_dept'])) {
        // While a handoff is pending, the sender still owns the transaction (receiver must click Receive first).
        $ownerDeptEdit = (string)$handoffOpen['from_dept'];
    } elseif (!empty($handoffHistory)) {
        $lastH = end($handoffHistory);
        if (!empty($lastH['received_at']) && !empty($lastH['to_dept'])) {
            $ownerDeptEdit = (string)$lastH['to_dept'];
        }
        reset($handoffHistory);
    }

    $roleRank = $workflowOrder[$roleDeptEdit] ?? null;
    $ownerRank = $workflowOrder[$ownerDeptEdit] ?? null;
    $canEditUpdates = (
        $role !== 'supplier'
        && $role !== 'admin'
        && $roleDeptEdit !== ''
        && $roleRank !== null
        && $ownerRank !== null
        && $roleRank <= $ownerRank
    );
    if (!$canEditUpdates && !isset($_POST['handoff_action'])) {
        $error = 'You cannot edit this transaction until it reaches your department.';
    }

    if (isset($_POST['handoff_action']) && $role !== 'supplier' && $role !== 'admin') {
        $action = (string)($_POST['handoff_action'] ?? '');

        $nextDept = '';
        $fromDept = '';

        $currentDeptByHandoff = '';
        if (!empty($handoffOpen['to_dept'])) {
            $currentDeptByHandoff = (string)$handoffOpen['to_dept'];
        } elseif (!empty($handoffHistory)) {
            $lastH = end($handoffHistory);
            if (!empty($lastH['received_at']) && !empty($lastH['to_dept'])) {
                $currentDeptByHandoff = (string)$lastH['to_dept'];
            }
            reset($handoffHistory);
        }

        // Determine "who should forward" and "who is next" based on current workflow state.
        // These mirror the notifications gating logic (empty -> non-empty transitions) and common stage ownership.
        if ($currentDeptByHandoff !== '') {
            $fromDept = $currentDeptByHandoff;
            $nextMap = [
                'procurement' => 'supply',
                'supply' => 'accounting_pre',
                'accounting_pre' => 'budget',
                'budget' => 'accounting_post',
                'accounting_post' => 'cashier',
                'cashier' => '',
            ];
            $nextDept = $nextMap[$fromDept] ?? '';
        } elseif (!empty($transaction['cashier_status'])) {
            $fromDept = '';
            $nextDept = '';
        } elseif (!empty($transaction['acct_post_status'])) {
            $fromDept = 'accounting_post';
            $nextDept = empty($transaction['cashier_status']) ? 'cashier' : '';
        } elseif (!empty($transaction['budget_status']) || !empty($transaction['budget_dv_number']) || !empty($transaction['budget_dv_date'])) {
            $fromDept = 'budget';
            $nextDept = empty($transaction['acct_post_status']) ? 'accounting_post' : '';
        } elseif (!empty($transaction['acct_pre_status'])) {
            $fromDept = 'accounting_pre';
            $nextDept = empty($transaction['budget_status']) && empty($transaction['budget_dv_number']) && empty($transaction['budget_dv_date']) ? 'budget' : '';
        } elseif (!empty($transaction['supply_status']) || !empty($transaction['supply_date'])) {
            $fromDept = 'supply';
            $nextDept = empty($transaction['acct_pre_status']) ? 'accounting_pre' : '';
        } elseif (!empty($transaction['proc_date']) || !empty($transaction['proc_status'])) {
            $fromDept = 'procurement';
            $nextDept = empty($transaction['supply_status']) && empty($transaction['supply_date']) ? 'supply' : '';
        } else {
            $fromDept = 'procurement';
            $nextDept = 'supply';
        }

        $deptForRole = [
            'procurement' => 'procurement',
            'supply' => 'supply',
            'accounting' => (!empty($transaction['budget_status']) || !empty($transaction['budget_dv_number']) || !empty($transaction['budget_dv_date'])) ? 'accounting_post' : 'accounting_pre',
            'budget' => 'budget',
            'cashier' => 'cashier',
        ];
        $roleDept = $deptForRole[$role] ?? '';

        $isDeptCompleted = function (string $dept) use ($transaction): bool {
            if ($dept === 'procurement') {
                return strtoupper(trim((string)($transaction['proc_status'] ?? ''))) === 'COMPLETED';
            }
            if ($dept === 'supply') {
                return strtoupper(trim((string)($transaction['supply_status'] ?? ''))) === 'COMPLETED';
            }
            if ($dept === 'accounting_pre') {
                return strtoupper(trim((string)($transaction['acct_pre_status'] ?? ''))) === 'COMPLETED';
            }
            if ($dept === 'budget') {
                return strtoupper(trim((string)($transaction['budget_status'] ?? ''))) === 'COMPLETED';
            }
            if ($dept === 'accounting_post') {
                return strtoupper(trim((string)($transaction['acct_post_status'] ?? ''))) === 'COMPLETED';
            }
            if ($dept === 'cashier') {
                return strtoupper(trim((string)($transaction['cashier_status'] ?? ''))) === 'COMPLETED';
            }
            return false;
        };

        try {
            $db->exec('CREATE TABLE IF NOT EXISTS transaction_handoffs (
                id INT(11) NOT NULL AUTO_INCREMENT,
                transaction_id INT(11) NOT NULL,
                from_dept VARCHAR(32) NOT NULL,
                to_dept VARCHAR(32) NOT NULL,
                forwarded_at DATETIME NOT NULL,
                received_at DATETIME NULL DEFAULT NULL,
                delay_seconds INT(11) NULL DEFAULT NULL,
                exceeded_grace TINYINT(1) NOT NULL DEFAULT 0,
                created_by_user_id INT(11) DEFAULT NULL,
                received_by_user_id INT(11) DEFAULT NULL,
                PRIMARY KEY (id),
                KEY idx_tx_open (transaction_id, received_at),
                KEY idx_tx_time (transaction_id, forwarded_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');

            if ($action === 'forward') {
                if ($roleDept !== '' && $fromDept !== '' && $nextDept !== '' && $roleDept === $fromDept && $isDeptCompleted($fromDept)) {
                    $stmtOpen = $db->prepare('SELECT id FROM transaction_handoffs WHERE transaction_id = ? AND received_at IS NULL LIMIT 1');
                    $stmtOpen->execute([$id]);
                    $open = $stmtOpen->fetch(PDO::FETCH_ASSOC);

                    if (!$open) {
                        $stmtIns = $db->prepare('INSERT INTO transaction_handoffs (transaction_id, from_dept, to_dept, forwarded_at, created_by_user_id)
                                                 VALUES (?, ?, ?, NOW(), ?)');
                        $stmtIns->execute([$id, $fromDept, $nextDept, (int)($_SESSION['user_id'] ?? 0)]);

                        try {
                            create_log($db, $_SESSION['user_id'] ?? null, 'transaction_handoff_forward', 'transaction', (int)$id, json_encode([
                                'transaction_id' => (int)$id,
                                'po_number' => (string)($transaction['po_number'] ?? ''),
                                'from_dept' => (string)$fromDept,
                                'to_dept' => (string)$nextDept,
                            ]));
                        } catch (Exception $e) {
                        }

                        try {
                            $poNum = $transaction['po_number'] ?? '';
                            $link = 'transaction_view.php?id=' . (int)$id;
                            $pendingTitle = 'Handoff Forwarded';
                            $pendingMsg = strtoupper($fromDept) . ' forwarded PO ' . $poNum . ' to ' . strtoupper($nextDept) . '. Please receive it.';
                            $notifyRole = $nextDept === 'accounting_pre' || $nextDept === 'accounting_post' ? 'accounting' : $nextDept;
                            create_dept_notification_once($db, $notifyRole, $id, $pendingTitle, $pendingMsg, $link);
                        } catch (Exception $e) {
                        }
                    }
                }
            } elseif ($action === 'receive') {
                if ($roleDept !== '' && $handoffOpen && !empty($handoffOpen['id'])) {
                    $toDept = (string)($handoffOpen['to_dept'] ?? '');
                    $from = (string)($handoffOpen['from_dept'] ?? '');
                    if ($toDept !== '' && $roleDept === $toDept) {
                        $forwardedAt = strtotime((string)($handoffOpen['forwarded_at'] ?? ''));
                        $now = time();
                        $delay = ($forwardedAt !== false && $forwardedAt > 0) ? max(0, $now - $forwardedAt) : 0;
                        $exceeded = ($delay > $handoffGraceSeconds) ? 1 : 0;

                        $stmtUpd = $db->prepare('UPDATE transaction_handoffs
                                                 SET received_at = NOW(), delay_seconds = ?, exceeded_grace = ?, received_by_user_id = ?
                                                 WHERE id = ? AND received_at IS NULL');
                        $stmtUpd->execute([$delay, $exceeded, (int)($_SESSION['user_id'] ?? 0), (int)$handoffOpen['id']]);

                        try {
                            create_log($db, $_SESSION['user_id'] ?? null, 'transaction_handoff_receive', 'transaction', (int)$id, json_encode([
                                'transaction_id' => (int)$id,
                                'po_number' => (string)($transaction['po_number'] ?? ''),
                                'from_dept' => (string)$from,
                                'to_dept' => (string)$toDept,
                                'delay_seconds' => (int)$delay,
                                'exceeded_grace' => (int)$exceeded,
                            ]));
                        } catch (Exception $e) {
                        }

                        try {
                            $poNum = $transaction['po_number'] ?? '';
                            $link = 'transaction_view.php?id=' . (int)$id;
                            $msgOk = 'Transaction was successfully received for PO ' . $poNum . '.';
                            $fromRoleOk = $from === 'accounting_pre' || $from === 'accounting_post' ? 'accounting' : $from;
                            create_dept_notification_once($db, $fromRoleOk, $id, 'Handoff Received', $msgOk, $link);
                        } catch (Exception $e) {
                        }

                        if ($exceeded) {
                            try {
                                $poNum = $transaction['po_number'] ?? '';
                                $link = 'transaction_view.php?id=' . (int)$id;
                                $msg = 'Handoff delay exceeded grace period (' . (int)round($delay / 60) . ' min) for PO ' . $poNum . '.';
                                $fromRole = $from === 'accounting_pre' || $from === 'accounting_post' ? 'accounting' : $from;
                                $toRole = $toDept === 'accounting_pre' || $toDept === 'accounting_post' ? 'accounting' : $toDept;
                                create_dept_notification_once($db, $fromRole, $id, 'Handoff Delay', $msg, $link);
                                create_dept_notification_once($db, $toRole, $id, 'Handoff Delay', $msg, $link);
                            } catch (Exception $e) {
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
        }

        if ($action === 'receive' && !empty($from) && !empty($toDept)) {
            header('Location: transaction_view.php?id=' . $id . '&handoff_received=1&handoff_from=' . urlencode((string)$from) . '&handoff_to=' . urlencode((string)$toDept));
        } else {
            header('Location: transaction_view.php?id=' . $id);
        }
        exit;
    }
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
            $supplyStatus = trim($_POST['supply_status'] ?? '');
            $supplyDeliveryReceipt = trim($_POST['supply_delivery_receipt'] ?? '');
            $supplySalesInvoice = trim($_POST['supply_sales_invoice'] ?? '');
            $supplyRemarks = trim($_POST['supply_remarks'] ?? '');
            $supplyPartialDeliveryDate = trim($_POST['supply_partial_delivery_date'] ?? '');
            $supplyDeliveryDate = trim($_POST['supply_delivery_date'] ?? '');

            if ($supplyDeliveryReceipt !== '' && !preg_match('/^\d+$/', $supplyDeliveryReceipt)) {
                $error = 'Delivery Receipt must be numbers only.';
            } elseif ($supplySalesInvoice !== '' && !preg_match('/^\d+$/', $supplySalesInvoice)) {
                $error = 'Sales Invoice must be numbers only.';
            } else {
                $normalizedSupplyStatus = strtoupper(trim((string)$supplyStatus));
                if ($normalizedSupplyStatus === 'PARTIAL DELIVER') {
                    if ($supplyPartialDeliveryDate === '') {
                        $supplyPartialDeliveryDate = date('Y-m-d');
                    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $supplyPartialDeliveryDate)) {
                        $error = 'Partial Delivery Date is invalid.';
                    }
                }

                if ($normalizedSupplyStatus === 'COMPLETED') {
                    if ($supplyDeliveryDate === '') {
                        $supplyDeliveryDate = date('Y-m-d');
                    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $supplyDeliveryDate)) {
                        $error = 'Delivery Date is invalid.';
                    }
                }

                if ($error === '') {
                    $fields[] = 'supply_status = ?';
                    $fields[] = 'supply_delivery_receipt = ?';
                    $fields[] = 'supply_sales_invoice = ?';
                    $fields[] = 'supply_remarks = ?';
                    $fields[] = 'supply_date = CURDATE()';

                    $params[] = $supplyStatus;
                    $params[] = $supplyDeliveryReceipt;
                    $params[] = $supplySalesInvoice;
                    $params[] = $supplyRemarks;

                    if ($normalizedSupplyStatus === 'PARTIAL DELIVER') {
                        $fields[] = 'supply_partial_delivery_date = ?';
                        $params[] = $supplyPartialDeliveryDate;
                    }
                    if ($normalizedSupplyStatus === 'COMPLETED') {
                        $fields[] = 'supply_delivery_date = ?';
                        $params[] = $supplyDeliveryDate;
                    }

                    $logStage = 'supply';
                    $logStatus = $supplyStatus;
                    $logRemarks = $supplyRemarks;
                }
            }
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
            $budgetDvNumber = trim($_POST['budget_dv_number'] ?? '');
            $budgetDvDate = trim($_POST['budget_dv_date'] ?? '');
            $budgetStatus = trim($_POST['budget_status'] ?? '');
            $budgetDemandability = trim($_POST['budget_demandability'] ?? '');
            $budgetRemarks = trim($_POST['budget_remarks'] ?? '');

            if ($budgetDvNumber !== '' && !preg_match('/^\d+$/', $budgetDvNumber)) {
                $error = 'DV Number must be numbers only.';
            } else {
                $fields[] = 'budget_dv_number = ?';
                $fields[] = 'budget_dv_date = ?';
                $fields[] = 'budget_status = ?';
                $fields[] = 'budget_demandability = ?';
                $fields[] = 'budget_remarks = ?';
                $params[] = $budgetDvNumber;
                $params[] = $budgetDvDate;
                $params[] = $budgetStatus;
                $params[] = $budgetDemandability;
                $params[] = $budgetRemarks;
                $logStage = 'budget';
                $logStatus = $params[2];
                $logRemarks = $params[4];
            }
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

            if ($cashierOrNum !== '' && !preg_match('/^\d+$/', $cashierOrNum)) {
                $error = 'ACIC Number must be numbers only.';
            } else {
                // Store raw values on the transaction
                $params[] = $cashierStatus;
                $params[] = $cashierRemarks;
                $params[] = $cashierOrNum;
                $params[] = $cashierOrDate;
                $params[] = $cashierAmount;
                $params[] = $cashierPayDate;
            }

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
            if (!$canEditUpdates) {
                $error = 'You cannot edit this transaction until it reaches your department.';
            } else {
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

            try {
                $details = [
                    'transaction_id' => (int)$id,
                    'po_number' => (string)($transaction['po_number'] ?? ''),
                    'stage' => (string)($logStage ?? ''),
                    'status' => (string)$logStatus,
                    'remarks' => (string)$logRemarks,
                ];
                if ($role === 'supply') {
                    $details['delivery_receipt'] = (string)($supplyDeliveryReceipt ?? '');
                    $details['sales_invoice'] = (string)($supplySalesInvoice ?? '');
                }
                if ($role === 'accounting') {
                    $details['dv_amount'] = (string)($acctDvAmount ?? '');
                }
                if ($role === 'budget') {
                    $details['dv_number'] = (string)($budgetDvNumber ?? '');
                    $details['dv_date'] = (string)($budgetDvDate ?? '');
                    $details['demandability'] = (string)($budgetDemandability ?? '');
                }
                if ($role === 'cashier') {
                    $details['or_number'] = (string)($cashierOrNum ?? '');
                    $details['or_date'] = (string)($cashierOrDate ?? '');
                    $details['payment_date'] = (string)($cashierPayDate ?? '');
                    $details['landbank_ref'] = (string)($cashierAmount ?? '');
                }
                create_log($db, $_SESSION['user_id'] ?? null, 'transaction_update', 'transaction', (int)$id, json_encode($details));
            } catch (Exception $e) {
            }

            dept_notifications_ensure_table($db);

            $poNum = $transaction['po_number'] ?? '';
            $link = 'transaction_view.php?id=' . (int)$id;

            $statusUpper = strtoupper(trim((string)$logStatus));
            $pendingRoles = [];
            $completedNotifyRoles = [];

            $prevStagesByStage = [
                'procurement' => [],
                'supply' => ['procurement'],
                'accounting_pre' => ['procurement', 'supply'],
                'budget' => ['procurement', 'supply', 'accounting'],
                'accounting_post' => ['procurement', 'supply', 'accounting', 'budget'],
                'cashier' => ['procurement', 'supply', 'accounting', 'budget'],
            ];

            // Pending should fire only when the next department can actually see/act on it.
            // Use status transitions from empty -> non-empty, aligned with partials_transactions_table.php gating fields.
            if ($logStage === 'procurement') {
                $oldProcDate = $transaction['proc_date'] ?? null;
                $newHasMeaningfulUpdate = (trim((string)$logStatus) !== '' || trim((string)$logRemarks) !== '');
                if (empty($oldProcDate) && $newHasMeaningfulUpdate) {
                    $pendingRoles[] = 'supply';
                }
            } elseif ($logStage === 'supply') {
                $old = trim((string)($transaction['supply_status'] ?? ''));
                $new = trim((string)$logStatus);
                if ($old === '' && $new !== '') {
                    $pendingRoles[] = 'accounting';
                }
            } elseif ($logStage === 'accounting_pre') {
                $old = trim((string)($transaction['acct_pre_status'] ?? ''));
                $new = trim((string)$logStatus);
                if ($old === '' && $new !== '') {
                    $pendingRoles[] = 'budget';
                }
            } elseif ($logStage === 'budget') {
                $old = trim((string)($transaction['budget_status'] ?? ''));
                $new = trim((string)$logStatus);
                if ($old === '' && $new !== '') {
                    $pendingRoles[] = 'accounting';
                }
            } elseif ($logStage === 'accounting_post') {
                $old = trim((string)($transaction['acct_post_status'] ?? ''));
                $new = trim((string)$logStatus);
                if ($old === '' && $new !== '') {
                    $pendingRoles[] = 'cashier';
                }
            }

            // Completed should notify the next department in the workflow.
            if ($statusUpper === 'COMPLETED') {
                $nextRoleByStage = [
                    'procurement' => 'supply',
                    'supply' => 'accounting',
                    'accounting_pre' => 'budget',
                    'budget' => 'accounting',
                    'accounting_post' => 'cashier',
                    'cashier' => null,
                ];
                $nextRole = $nextRoleByStage[$logStage] ?? null;
                if ($nextRole) {
                    $completedNotifyRoles = [$nextRole];
                }
            }

            foreach ($pendingRoles as $r) {
                $pendingTitle = 'Pending Transaction';
                $pendingMsg = 'Upcoming PO ' . $poNum . '';
                create_dept_notification_once($db, $r, $id, $pendingTitle, $pendingMsg, $link);
            }

            foreach ($completedNotifyRoles as $stageName) {
                $completedRole = $stageName;
                $completedTitle = ucfirst($role) . ' Completed';
                $completedMsg = ucfirst($role) . ' marked PO ' . $poNum . ' as Completed.';
                create_dept_notification_once($db, $completedRole, $id, $completedTitle, $completedMsg, $link);
            }

            if ($logStage === 'cashier' && $statusUpper === 'COMPLETED') {
                try {
                    $notifyStmt = $db->prepare('INSERT INTO notifications (supplier_id, transaction_id, title, message, link, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
                    $title = 'Transaction completed';
                    $message = 'Your PO ' . $poNum . ' has been completed.';
                    $notifyStmt->execute([
                        $transaction['supplier_id'],
                        $transaction['id'],
                        $title,
                        $message,
                        $link,
                    ]);
                } catch (Exception $e) {
                }
            }

                // Redirect to avoid duplicate submissions on refresh (Post/Redirect/Get)
                header('Location: transaction_view.php?id=' . $id . '&updated=1');
                exit;
            }
        }
    } catch (Exception $e) {
        $error = 'Error updating transaction.';
    }
}

// Determine if cashier can proceed to Landbank
// Temporarily allow cashier to always proceed (no status/DV verification)
$canProceedLandbank = ($role === 'cashier');

$ownerDeptTab = 'procurement';
if (!empty($handoffOpen) && !empty($handoffOpen['from_dept'])) {
    $ownerDeptTab = (string)$handoffOpen['from_dept'];
} elseif (!empty($handoffHistory)) {
    $lastH = end($handoffHistory);
    if (!empty($lastH['received_at']) && !empty($lastH['to_dept'])) {
        $ownerDeptTab = (string)$lastH['to_dept'];
    }
    reset($handoffHistory);
}

$poNumTab = trim((string)($transaction['po_number'] ?? ''));
$txLabelTab = $poNumTab !== '' ? ('PO ' . $poNumTab) : ('Transaction #' . (int)$id);
$pageTitle = strtoupper($ownerDeptTab) . ' - ' . $txLabelTab . ' - STMS';

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
                <div id="basicInfoContainer" class="info-grid">
                    <div class="info-item info-item-full">
                        <div class="info-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="info-content">
                            <p class="info-label">Program Title</p>
                            <p class="info-value"><?php echo htmlspecialchars($transaction['program_title']); ?></p>
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
                        <div class="info-icon"><i class="fas fa-calendar"></i></div>
                        <div class="info-content">
                            <p class="info-label">Date Coverage</p>
                            <p class="info-value"><?php echo $coverageDisplay; ?></p>
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

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="info-content">
                            <p class="info-label">PO (Gross Amount)</p>
                            <p class="info-value">₱ <?php echo number_format($transaction['amount'], 2); ?></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-tags"></i></div>
                        <div class="info-content">
                            <p class="info-label">Transaction Type</p>
                            <p class="info-value">
                                <?php
                                $poType = trim((string)($transaction['po_type'] ?? ''));
                                echo $poType !== '' ? htmlspecialchars($poType) : '—';
                                ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($transaction['budget_dv_number'])): ?>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-receipt"></i></div>
                            <div class="info-content">
                                <p class="info-label">DV Number</p>
                                <p class="info-value"><?php echo htmlspecialchars($transaction['budget_dv_number']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($transaction['budget_dv_date'])): ?>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-calendar-check"></i></div>
                            <div class="info-content">
                                <p class="info-label">DV Date</p>
                                <p class="info-value"><?php echo htmlspecialchars($transaction['budget_dv_date']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($transaction['budget_demandability'])): ?>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-balance-scale"></i></div>
                            <div class="info-content">
                                <p class="info-label">Demandability</p>
                                <p class="info-value"><?php echo htmlspecialchars($transaction['budget_demandability']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($transaction['supply_delivery_receipt'])): ?>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-truck"></i></div>
                            <div class="info-content">
                                <p class="info-label">Delivery Receipt</p>
                                <p class="info-value"><?php echo htmlspecialchars($transaction['supply_delivery_receipt']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($transaction['supply_partial_delivery_date'])): ?>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-calendar-day"></i></div>
                            <div class="info-content">
                                <p class="info-label">Partial Delivery Date</p>
                                <p class="info-value"><?php echo htmlspecialchars($transaction['supply_partial_delivery_date']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($transaction['supply_delivery_date'])): ?>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-calendar-check"></i></div>
                            <div class="info-content">
                                <p class="info-label">Delivery Date</p>
                                <p class="info-value"><?php echo htmlspecialchars($transaction['supply_delivery_date']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($transaction['supply_sales_invoice'])): ?>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-file-invoice"></i></div>
                            <div class="info-content">
                                <p class="info-label">Sales Invoice</p>
                                <p class="info-value"><?php echo htmlspecialchars($transaction['supply_sales_invoice']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Update (<?php echo htmlspecialchars(strtoupper($role)); ?>)</h6>
                <?php if ($role === 'supplier'): ?>
                    <p class="text-muted mb-0">Suppliers can only view the status of their transactions.</p>
                <?php else: ?>
                    <?php
                    $budgetDoneForHandoff = !empty($transaction['budget_status']) || !empty($transaction['budget_dv_number']) || !empty($transaction['budget_dv_date']);
                    $deptForRoleUi = [
                        'procurement' => 'procurement',
                        'supply' => 'supply',
                        'accounting' => $budgetDoneForHandoff ? 'accounting_post' : 'accounting_pre',
                        'budget' => 'budget',
                        'cashier' => 'cashier',
                    ];
                    $roleDeptUi = $deptForRoleUi[$role] ?? '';

                    $currentDeptByHandoffUi = '';
                    if ($handoffOpen && !empty($handoffOpen['to_dept'])) {
                        $currentDeptByHandoffUi = (string)$handoffOpen['to_dept'];
                    } elseif (!empty($handoffHistory)) {
                        $lastH = end($handoffHistory);
                        if (!empty($lastH['received_at']) && !empty($lastH['to_dept'])) {
                            $currentDeptByHandoffUi = (string)$lastH['to_dept'];
                        }
                        reset($handoffHistory);
                    }

                    $nextDeptUi = '';
                    $fromDeptUi = '';
                    if ($currentDeptByHandoffUi !== '') {
                        $fromDeptUi = $currentDeptByHandoffUi;
                        $nextMapUi = [
                            'procurement' => 'supply',
                            'supply' => 'accounting_pre',
                            'accounting_pre' => 'budget',
                            'budget' => 'accounting_post',
                            'accounting_post' => 'cashier',
                            'cashier' => '',
                        ];
                        $nextDeptUi = $nextMapUi[$fromDeptUi] ?? '';
                    } elseif (!empty($transaction['cashier_status'])) {
                        $fromDeptUi = '';
                        $nextDeptUi = '';
                    } elseif (!empty($transaction['acct_post_status'])) {
                        $fromDeptUi = 'accounting_post';
                        $nextDeptUi = empty($transaction['cashier_status']) ? 'cashier' : '';
                    } elseif ($budgetDoneForHandoff) {
                        $fromDeptUi = 'budget';
                        $nextDeptUi = empty($transaction['acct_post_status']) ? 'accounting_post' : '';
                    } elseif (!empty($transaction['acct_pre_status'])) {
                        $fromDeptUi = 'accounting_pre';
                        $nextDeptUi = !$budgetDoneForHandoff ? 'budget' : '';
                    } elseif (!empty($transaction['supply_status']) || !empty($transaction['supply_date'])) {
                        $fromDeptUi = 'supply';
                        $nextDeptUi = empty($transaction['acct_pre_status']) ? 'accounting_pre' : '';
                    } elseif (!empty($transaction['proc_date']) || !empty($transaction['proc_status'])) {
                        $fromDeptUi = 'procurement';
                        $nextDeptUi = (empty($transaction['supply_status']) && empty($transaction['supply_date'])) ? 'supply' : '';
                    } else {
                        $fromDeptUi = 'procurement';
                        $nextDeptUi = 'supply';
                    }

                    $isFromDeptCompletedUi = false;
                    if ($fromDeptUi === 'procurement') {
                        $isFromDeptCompletedUi = strtoupper(trim((string)($transaction['proc_status'] ?? ''))) === 'COMPLETED';
                    } elseif ($fromDeptUi === 'supply') {
                        $isFromDeptCompletedUi = strtoupper(trim((string)($transaction['supply_status'] ?? ''))) === 'COMPLETED';
                    } elseif ($fromDeptUi === 'accounting_pre') {
                        $isFromDeptCompletedUi = strtoupper(trim((string)($transaction['acct_pre_status'] ?? ''))) === 'COMPLETED';
                    } elseif ($fromDeptUi === 'budget') {
                        $isFromDeptCompletedUi = strtoupper(trim((string)($transaction['budget_status'] ?? ''))) === 'COMPLETED';
                    } elseif ($fromDeptUi === 'accounting_post') {
                        $isFromDeptCompletedUi = strtoupper(trim((string)($transaction['acct_post_status'] ?? ''))) === 'COMPLETED';
                    } elseif ($fromDeptUi === 'cashier') {
                        $isFromDeptCompletedUi = strtoupper(trim((string)($transaction['cashier_status'] ?? ''))) === 'COMPLETED';
                    }

                    $canForward = ($roleDeptUi !== '' && $roleDeptUi === $fromDeptUi && $nextDeptUi !== '' && $isFromDeptCompletedUi);
                    $canReceive = false;
                    $handoffForwardedTs = null;
                    $handoffGraceEndsTs = null;
                    $handoffFromDept = '';
                    $handoffToDept = '';
                    $showHandoffBanner = false;
                    $handoffBannerText = '';
                    $handoffReceivedFlash = '';
                    if ($handoffOpen) {
                        $handoffFromDept = (string)($handoffOpen['from_dept'] ?? '');
                        $handoffToDept = (string)($handoffOpen['to_dept'] ?? '');
                        $fwdTs = (int)($handoffOpen['forwarded_ts'] ?? 0);
                        if ($fwdTs > 0) {
                            $handoffForwardedTs = $fwdTs;
                            $handoffGraceEndsTs = $fwdTs + $handoffGraceSeconds;
                        }
                    }

                    $handoffRemainingSeconds = '';
                    if ($handoffForwardedTs && $handoffGraceEndsTs) {
                        $serverNowForAttr = (int)($handoffOpen['server_now_ts'] ?? time());
                        $handoffRemainingSeconds = (string)max(0, (int)$handoffGraceEndsTs - $serverNowForAttr);
                    }

                    $handoffOverdueSeconds = '';
                    if ($handoffForwardedTs && $handoffGraceEndsTs) {
                        $serverNowForOverdueAttr = (int)($handoffOpen['server_now_ts'] ?? time());
                        $handoffOverdueSeconds = (string)max(0, $serverNowForOverdueAttr - (int)$handoffGraceEndsTs);
                    }

                    $receiveCountdown = '';
                    if ($handoffRemainingSeconds !== '') {
                        $remainingInt = (int)$handoffRemainingSeconds;
                        if ($remainingInt > 0) {
                            $hh = intdiv($remainingInt, 3600);
                            $mm = intdiv($remainingInt % 3600, 60);
                            $ss = $remainingInt % 60;
                            $receiveCountdown = 'Grace ends in ' . sprintf('%02d:%02d:%02d', $hh, $mm, $ss);
                        } else {
                            $overdueInt = (int)($handoffOverdueSeconds !== '' ? $handoffOverdueSeconds : '0');
                            $hh = intdiv($overdueInt, 3600);
                            $mm = intdiv($overdueInt % 3600, 60);
                            $ss = $overdueInt % 60;
                            $receiveCountdown = 'Grace period exceeded by ' . sprintf('%02d:%02d:%02d', $hh, $mm, $ss);
                        }
                    }

                    if ($roleDeptUi !== '' && $handoffOpen && !empty($handoffOpen['to_dept'])) {
                        $canReceive = ((string)$handoffOpen['to_dept'] === $roleDeptUi);
                    }

                    if ($roleDeptUi !== '' && $handoffOpen && $handoffForwardedTs && $handoffFromDept !== '' && $handoffToDept !== '') {
                        $showHandoffBanner = ($roleDeptUi === $handoffFromDept || $roleDeptUi === $handoffToDept);
                        if ($showHandoffBanner) {
                            if ($roleDeptUi === $handoffToDept) {
                                $handoffBannerText = 'Pending handoff from ' . strtoupper($handoffFromDept) . ' to ' . strtoupper($handoffToDept) . '.';
                            } else {
                                $handoffBannerText = 'Waiting for ' . strtoupper($handoffToDept) . ' to receive (forwarded by ' . strtoupper($handoffFromDept) . ')';
                            }
                        }
                    }

                    if (isset($_GET['handoff_received']) && (string)($_GET['handoff_received'] ?? '') === '1') {
                        $flashFrom = (string)($_GET['handoff_from'] ?? '');
                        $flashTo = (string)($_GET['handoff_to'] ?? '');
                        if ($roleDeptUi !== '' && $flashFrom !== '' && $flashTo !== '' && ($roleDeptUi === $flashFrom || $roleDeptUi === $flashTo)) {
                            $handoffReceivedFlash = 'Handoff received: ' . strtoupper($flashFrom) . ' → ' . strtoupper($flashTo) . '.';
                        }
                    }

                    $ownerDeptUi = 'procurement';
                    if ($handoffOpen && !empty($handoffFromDept)) {
                        $ownerDeptUi = $handoffFromDept;
                    } elseif (!empty($handoffHistory)) {
                        $lastH = end($handoffHistory);
                        if (!empty($lastH['received_at']) && !empty($lastH['to_dept'])) {
                            $ownerDeptUi = (string)$lastH['to_dept'];
                        }
                        reset($handoffHistory);
                    }

                    $workflowOrderUi = [
                        'procurement' => 0,
                        'supply' => 1,
                        'accounting_pre' => 2,
                        'budget' => 3,
                        'accounting_post' => 4,
                        'cashier' => 5,
                    ];
                    $roleRankUi = $workflowOrderUi[$roleDeptUi] ?? null;
                    $ownerRankUi = $workflowOrderUi[$ownerDeptUi] ?? null;
                    $canEditUpdatesUi = (
                        $role !== 'admin'
                        && $roleDeptUi !== ''
                        && $roleRankUi !== null
                        && $ownerRankUi !== null
                        && $roleRankUi <= $ownerRankUi
                    );
                    ?>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <div class="text-muted small">
                            Grace period: <strong><?php echo (int)$handoffGraceSeconds; ?></strong> seconds
                            (<?php echo (int)ceil(((int)$handoffGraceSeconds) / 60); ?> min)
                            <?php if (isset($_GET['grace_updated']) && (string)($_GET['grace_updated'] ?? '') === '1'): ?>
                                <span class="ms-2 text-success fw-semibold">Updated</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($role === 'admin'): ?>
                            <form method="post" class="m-0 d-flex align-items-center gap-2">
                                <input type="hidden" name="action" value="set_handoff_grace">
                                <input type="number" class="form-control form-control-sm" name="handoff_grace_seconds" min="0" step="1" value="<?php echo (int)$handoffGraceSeconds; ?>" style="width: 140px;" aria-label="Handoff grace seconds">
                                <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <div id="handoffStatusContainer" class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3" data-forwarded-ts="<?php echo $handoffForwardedTs ? (int)$handoffForwardedTs : ''; ?>" data-grace-ends-ts="<?php echo $handoffGraceEndsTs ? (int)$handoffGraceEndsTs : ''; ?>" data-server-now-ts="<?php echo !empty($handoffOpen['server_now_ts']) ? (int)$handoffOpen['server_now_ts'] : (int)time(); ?>" data-remaining-seconds="<?php echo htmlspecialchars($handoffRemainingSeconds); ?>" data-overdue-seconds="<?php echo htmlspecialchars($handoffOverdueSeconds); ?>" data-client-sync-ts="">
                        <div class="text-muted small">
                            <?php if ($showHandoffBanner && $handoffBannerText !== ''): ?>
                                <?php echo htmlspecialchars($handoffBannerText); ?>
                            <?php else: ?>
                                No pending handoff.
                            <?php endif; ?>
                            <?php if ($handoffReceivedFlash !== ''): ?>
                                <span class="ms-2 text-success fw-semibold"><?php echo htmlspecialchars($handoffReceivedFlash); ?></span>
                            <?php endif; ?>
                            <?php if ($handoffOpen && $handoffForwardedTs && $receiveCountdown !== ''): ?>
                                <span class="ms-2 text-danger" data-handoff-countdown><?php echo htmlspecialchars($receiveCountdown); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-2">
                            <?php if ($canForward && !$handoffOpen): ?>
                                <form method="post" class="m-0">
                                    <input type="hidden" name="handoff_action" value="forward">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">Forward</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($canReceive && $handoffOpen && !empty($handoffOpen['id'])): ?>
                                <form method="post" class="m-0">
                                    <input type="hidden" name="handoff_action" value="receive">
                                    <button type="submit" class="btn btn-success btn-sm">Receive</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!$canEditUpdatesUi): ?>
                        <div class="alert alert-secondary py-2 mb-2" style="opacity: 0.85;">
                            View only. You can update once this transaction is forwarded to your department and received.
                        </div>
                    <?php endif; ?>

                    <form method="post" novalidate>
                        <fieldset id="updateFieldset" <?php echo !$canEditUpdatesUi ? 'disabled' : ''; ?> style="<?php echo !$canEditUpdatesUi ? 'opacity:0.65;' : ''; ?>">
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
                                           inputmode="numeric" pattern="\d*" oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                                           value="<?php echo htmlspecialchars($transaction['supply_delivery_receipt'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sales Invoice</label>
                                    <input type="text" name="supply_sales_invoice" class="form-control"
                                           inputmode="numeric" pattern="\d*" oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                                           value="<?php echo htmlspecialchars($transaction['supply_sales_invoice'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Supply Status</label>
                                <select name="supply_status" id="supplyStatusSelect" class="form-control">
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

                            <div class="mb-3" id="partialDeliveryDateGroup" style="display: none;">
                                <label class="form-label">Partial Delivery Date</label>
                                <input type="date" name="supply_partial_delivery_date" class="form-control"
                                       value="<?php echo htmlspecialchars($transaction['supply_partial_delivery_date'] ?? ''); ?>">
                            </div>

                            <div class="mb-3" id="deliveryDateGroup" style="display: none;">
                                <label class="form-label">Delivery Date</label>
                                <input type="date" name="supply_delivery_date" class="form-control"
                                       value="<?php echo htmlspecialchars($transaction['supply_delivery_date'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Supply Remarks</label>
                                <textarea name="supply_remarks" class="form-control"
                                          rows="2"><?php echo htmlspecialchars($transaction['supply_remarks'] ?? ''); ?></textarea>
                            </div>

                            <script>
                                (function () {
                                    var select = document.getElementById('supplyStatusSelect');
                                    var partialGroup = document.getElementById('partialDeliveryDateGroup');
                                    var deliveryGroup = document.getElementById('deliveryDateGroup');
                                    if (!select || !partialGroup || !deliveryGroup) {
                                        return;
                                    }

                                    function updateVisibility() {
                                        var val = String(select.value || '').toUpperCase();
                                        partialGroup.style.display = (val === 'PARTIAL DELIVER') ? '' : 'none';
                                        deliveryGroup.style.display = (val === 'COMPLETED') ? '' : 'none';
                                    }

                                    select.addEventListener('change', updateVisibility);
                                    updateVisibility();
                                })();
                            </script>
                        <?php elseif ($role === 'accounting'): ?>
                            <div class="border rounded p-3 mb-2">
                                <h6 class="mb-2">Accounting</h6>
                                <div class="mb-2">
                                    <label class="form-label mb-1">Status</label>
                                    <?php
                                    $currentAcctStatus = ($transaction['acct_post_status'] ?: $transaction['acct_pre_status']) ?? '';
                                    ?>
                                    <select name="acct_status" class="form-control">
                                        <?php
                                        echo '<option value="">-- Select status --</option>';
                                        $acctOptions = ['FOR ORS', 'FOR VOUCHER', 'COMPLETED'];
                                        foreach ($acctOptions as $opt) {
                                            $selected = (strtoupper(trim($currentAcctStatus)) === strtoupper(trim($opt))) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($opt) . '" ' . $selected . '>' . htmlspecialchars($opt) . '</option>';
                                        }
                                        ?>
                                    </select>
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
                                           inputmode="numeric" pattern="\d*" oninput="this.value=this.value.replace(/[^0-9]/g,'');"
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
                                    $budgetStatusOptions = ['FOR PAYMENT', 'ACCOUNTS PAYABLE', 'COMPLETED'];
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
                                    $cashierStatusOptions = ['For ACIC', 'For OR Issuance', 'For SDS/ASDS Approval', 'COMPLETED'];
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
                                           inputmode="numeric" pattern="\d*" oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                                           value="<?php echo htmlspecialchars($transaction['cashier_or_number'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ACIC Date</label>
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
                                <button type="submit" class="btn btn-primary" id="saveUpdatesBtn">Save Updates</button>
                            </div>
                        <?php endif; ?>
                        </fieldset>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($role === 'cashier'): ?>
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="mb-2">Proceed to Landbank</h6>
                    <p class="small text-muted">
                        All requirements from Procurement, Supply, Accounting, and Budget are complete.
                    </p>
                    <a href="<?php echo htmlspecialchars(LANDBANK_URL); ?>" target="_blank"
                       class="btn btn-success w-100 mb-2">
                        Proceed to Landbank Site
                    </a>
                    <?php $notifyDefaultMsgUi = 'Your PO ' . ($transaction['po_number'] ?? '') . ' is now marked as COMPLETED. Please check the portal for details.'; ?>
                    <form method="post" class="mt-1">
                        <input type="hidden" name="notify_supplier" value="1">

                        <div class="mb-2">
                            <label class="form-label small mb-1">Message</label>
                            <textarea class="form-control" name="notify_message" id="notifyMessageTextarea" rows="3"><?php echo htmlspecialchars($notifyDefaultMsgUi); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-outline-primary w-100">
                            Notify Supplier
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="mb-3">
                    <i class="fas fa-stream me-2"></i>Flow Timeline
                </h6>
                <div class="timeline-scroll" style="max-height: 70vh; overflow-y: auto; padding-right: 8px;">
                <div class="timeline">
                    <!-- Procurement -->
                    <?php $procCompleted = (!empty($transaction['created_at']) || !empty($updatesByStage['procurement'])); ?>
                    <div class="timeline-item <?php echo $procCompleted ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo $procCompleted ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <?php
                            // Elapsed from transaction created_at to forwarded_at (handoff) or latest Procurement update
                            $elapsedProc = '';
                            if (!empty($updatesByStage['procurement'])) {
                                $createdTs = strtotime($transaction['created_at']);
                                $endTs = get_handoff_timestamp($handoffHistory, 'procurement', 'supply', 'forwarded_at');
                                if ($endTs === null) {
                                    $endTs = get_last_stage_timestamp($updatesByStage, 'procurement');
                                }
                                if ($createdTs !== false && $endTs !== null && $endTs >= $createdTs) {
                                    $elapsedProc = format_elapsed_time(($endTs - $createdTs) + (int)($handoffExtras['procurement'] ?? 0));
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
                                    <?php
                                    $procForwardTs = get_handoff_timestamp($handoffHistory, 'procurement', 'supply', 'forwarded_at');
                                    $procForwardPrinted = false;
                                    ?>
                                    <?php $countProc = count($updatesByStage['procurement']); ?>
                                    <?php foreach ($updatesByStage['procurement'] as $idx => $u): ?>
                                        <?php
                                        $isLatest = ($idx === $countProc - 1);
                                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');

                                        $showForwardHere = false;
                                        $updateTs = !empty($u['created_at']) ? strtotime((string)$u['created_at']) : false;
                                        $nextUpdateTs = false;
                                        if (($idx + 1) < $countProc) {
                                            $next = $updatesByStage['procurement'][$idx + 1] ?? null;
                                            if (!empty($next['created_at'])) {
                                                $nextUpdateTs = strtotime((string)$next['created_at']);
                                            }
                                        }
                                        if (!$procForwardPrinted && $procForwardTs !== null && $updateTs !== false) {
                                            $fwd = (int)$procForwardTs;
                                            if ($updateTs <= $fwd && ($nextUpdateTs === false || $nextUpdateTs > $fwd)) {
                                                $showForwardHere = true;
                                                $procForwardPrinted = true;
                                            } elseif ($idx === 0 && $updateTs > $fwd) {
                                                $showForwardHere = true;
                                                $procForwardPrinted = true;
                                            } elseif ($isLatest && !$procForwardPrinted) {
                                                $showForwardHere = true;
                                                $procForwardPrinted = true;
                                            }
                                        }
                                        ?>
                                        <div class="<?php echo $rowClass; ?>">
                                            <?php if ($isLatest): ?>
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
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
                                                    <div class="text-end text-muted">
                                                        <?php if ($showForwardHere && $procForwardTs !== null): ?>
                                                            <div>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$procForwardTs)); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
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
                                                    <div class="text-end text-muted">
                                                        <?php if ($showForwardHere && $procForwardTs !== null): ?>
                                                            <div>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$procForwardTs)); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                    $procSupplyForwardTs = get_handoff_timestamp($handoffHistory, 'procurement', 'supply', 'forwarded_at');
                    $procSupplyRecvTs = get_handoff_timestamp($handoffHistory, 'procurement', 'supply', 'received_at');
                    $procSupplyOverdue = 0;
                    if ($procSupplyForwardTs !== null) {
                        $endTs = $procSupplyRecvTs !== null ? $procSupplyRecvTs : time();
                        $delaySecs = max(0, (int)$endTs - (int)$procSupplyForwardTs);
                        $procSupplyOverdue = max(0, $delaySecs - (int)$handoffGraceSeconds);
                    }
                    ?>
                    <?php if ($procSupplyOverdue > 0): ?>
                        <div class="handoff-between-due">
                            <div class="small text-danger fw-semibold">
                                <?php echo htmlspecialchars(format_elapsed_time((int)$procSupplyOverdue)); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Supply Unit -->
                    <?php $supplyCompleted = (!empty($updatesByStage['supply']) || get_handoff_timestamp_first($handoffHistory, 'procurement', 'supply', 'received_at') !== null); ?>
                    <div class="timeline-item <?php echo $supplyCompleted ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo $supplyCompleted ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <?php
                            // Elapsed from received_at (handoff) to forwarded_at (handoff) or latest Supply update
                            $elapsedSupply = '';
                            $startTs = get_handoff_timestamp($handoffHistory, 'procurement', 'supply', 'received_at');
                            $endTs = get_handoff_timestamp($handoffHistory, 'supply', 'accounting_pre', 'forwarded_at');
                            if ($endTs === null) {
                                $endTs = get_last_stage_timestamp($updatesByStage, 'supply');
                            }
                            if ($startTs !== null && $endTs !== null && $endTs >= $startTs) {
                                $elapsedSupply = format_elapsed_time(($endTs - $startTs) + (int)($handoffExtras['supply'] ?? 0));
                            }
                            ?>
                            <h6 class="timeline-title d-flex justify-content-between align-items-center">
                                <span>Supply Unit</span>
                                <span class="small text-muted"><?php echo $elapsedSupply ? htmlspecialchars($elapsedSupply) : ''; ?></span>
                            </h6>

                            <?php if (!empty($updatesByStage['supply'])): ?>
                                <div class="timeline-history mt-2 p-2 border rounded bg-white">
                                    <div class="small text-muted mb-1">Update history</div>
                                    <?php
                                    $supplyReceivedTs = get_handoff_timestamp_first($handoffHistory, 'procurement', 'supply', 'received_at');
                                    $supplyReceivedPrinted = false;
                                    $supplyForwardTs = get_handoff_timestamp($handoffHistory, 'supply', 'accounting_pre', 'forwarded_at');
                                    $supplyForwardPrinted = false;
                                    ?>
                                    <?php $countSupply = count($updatesByStage['supply']); ?>
                                    <?php foreach ($updatesByStage['supply'] as $idx => $u): ?>
                                        <?php
                                        $isLatest = ($idx === $countSupply - 1);

                                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');

                                        $showReceivedHere = false;
                                        $showForwardHere = false;
                                        $updateTs = !empty($u['created_at']) ? strtotime((string)$u['created_at']) : false;
                                        $nextUpdateTs = false;
                                        if (($idx + 1) < $countSupply) {
                                            $next = $updatesByStage['supply'][$idx + 1] ?? null;
                                            if (!empty($next['created_at'])) {
                                                $nextUpdateTs = strtotime((string)$next['created_at']);
                                            }
                                        }
                                        if (!$supplyReceivedPrinted && $supplyReceivedTs !== null && $updateTs !== false && $updateTs >= (int)$supplyReceivedTs) {
                                            $showReceivedHere = true;
                                            $supplyReceivedPrinted = true;
                                        }

                                        if (!$supplyForwardPrinted && $supplyForwardTs !== null && $updateTs !== false) {
                                            $fwd = (int)$supplyForwardTs;
                                            if ($updateTs <= $fwd && ($nextUpdateTs === false || $nextUpdateTs > $fwd)) {
                                                $showForwardHere = true;
                                                $supplyForwardPrinted = true;
                                            } elseif ($idx === 0 && $updateTs > $fwd) {
                                                $showForwardHere = true;
                                                $supplyForwardPrinted = true;
                                            } elseif ($isLatest && !$supplyForwardPrinted) {
                                                $showForwardHere = true;
                                                $supplyForwardPrinted = true;
                                            }
                                        }

                                        ?>
                                        <div class="<?php echo $rowClass; ?>">
                                            <?php if ($isLatest): ?>
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
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
                                                    <div class="text-end text-muted">

                                                        <?php if ($showReceivedHere && $supplyReceivedTs !== null): ?>
                                                            <div>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$supplyReceivedTs)); ?></div>
                                                        <?php endif; ?>
                                                        <?php if ($showForwardHere && $supplyForwardTs !== null): ?>
                                                            <div>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$supplyForwardTs)); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
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
                                                    <div class="text-end text-muted">
                                                        <?php if ($showReceivedHere && $supplyReceivedTs !== null): ?>
                                                            <div>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$supplyReceivedTs)); ?></div>
                                                        <?php endif; ?>
                                                        <?php if ($showForwardHere && $supplyForwardTs !== null): ?>
                                                            <div>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$supplyForwardTs)); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                    $supplyAcctForwardTs = get_handoff_timestamp($handoffHistory, 'supply', 'accounting_pre', 'forwarded_at');
                    $supplyAcctRecvTs = get_handoff_timestamp($handoffHistory, 'supply', 'accounting_pre', 'received_at');
                    $supplyAcctOverdue = 0;
                    if ($supplyAcctForwardTs !== null) {
                        $endTs = $supplyAcctRecvTs !== null ? $supplyAcctRecvTs : time();
                        $delaySecs = max(0, (int)$endTs - (int)$supplyAcctForwardTs);
                        $supplyAcctOverdue = max(0, $delaySecs - (int)$handoffGraceSeconds);
                    }
                    ?>
                    <?php if ($supplyAcctOverdue > 0): ?>
                        <div class="handoff-between-due">
                            <div class="small text-danger fw-semibold">
                                <?php echo htmlspecialchars(format_elapsed_time((int)$supplyAcctOverdue)); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Accounting -->
                    <?php $acctPreCompleted = (!empty($updatesByStage['accounting_pre']) || get_handoff_timestamp_first($handoffHistory, 'supply', 'accounting_pre', 'received_at') !== null); ?>
                    <div class="timeline-item <?php echo $acctPreCompleted ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo $acctPreCompleted ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <?php
                            // Elapsed from received_at (handoff) to forwarded_at (handoff) or latest Accounting (pre-budget) update
                            $elapsedAcctPre = '';
                            $startTs = get_handoff_timestamp($handoffHistory, 'supply', 'accounting_pre', 'received_at');
                            $endTs = get_handoff_timestamp($handoffHistory, 'accounting_pre', 'budget', 'forwarded_at');
                            if ($endTs === null) {
                                $endTs = get_last_stage_timestamp($updatesByStage, 'accounting_pre');
                            }
                            if ($startTs !== null && $endTs !== null && $endTs >= $startTs) {
                                $elapsedAcctPre = format_elapsed_time(($endTs - $startTs) + (int)($handoffExtras['accounting_pre'] ?? 0));
                            }
                            ?>
                            <h6 class="timeline-title d-flex justify-content-between align-items-center">
                                <span>Accounting</span>
                                <span class="small text-muted"><?php echo $elapsedAcctPre ? htmlspecialchars($elapsedAcctPre) : ''; ?></span>
                            </h6>

                            <?php if (!empty($updatesByStage['accounting_pre'])): ?>
                                <div class="timeline-history mt-2 p-2 border rounded bg-white">
                                    <div class="small text-muted mb-1">Update history</div>
                                    <?php
                                    $acctPreReceivedTs = get_handoff_timestamp_first($handoffHistory, 'supply', 'accounting_pre', 'received_at');
                                    $acctPreReceivedPrinted = false;
                                    $acctPreForwardTs = get_handoff_timestamp($handoffHistory, 'accounting_pre', 'budget', 'forwarded_at');
                                    $acctPreForwardPrinted = false;
                                    ?>
                                    <?php $countAcctPre = count($updatesByStage['accounting_pre']); ?>
                                    <?php foreach ($updatesByStage['accounting_pre'] as $idx => $u): ?>
                                        <?php
                                        $isLatest = ($idx === $countAcctPre - 1);

                                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');

                                        $showReceivedHere = false;
                                        $showForwardHere = false;
                                        $updateTs = !empty($u['created_at']) ? strtotime((string)$u['created_at']) : false;
                                        $nextUpdateTs = false;
                                        if (($idx + 1) < $countAcctPre) {
                                            $next = $updatesByStage['accounting_pre'][$idx + 1] ?? null;
                                            if (!empty($next['created_at'])) {
                                                $nextUpdateTs = strtotime((string)$next['created_at']);
                                            }
                                        }
                                        if (!$acctPreReceivedPrinted && $acctPreReceivedTs !== null && $updateTs !== false && $updateTs >= (int)$acctPreReceivedTs) {
                                            $showReceivedHere = true;
                                            $acctPreReceivedPrinted = true;
                                        }

                                        if (!$acctPreForwardPrinted && $acctPreForwardTs !== null && $updateTs !== false) {
                                            $fwd = (int)$acctPreForwardTs;
                                            if ($updateTs <= $fwd && ($nextUpdateTs === false || $nextUpdateTs > $fwd)) {
                                                $showForwardHere = true;
                                                $acctPreForwardPrinted = true;
                                            } elseif ($idx === 0 && $updateTs > $fwd) {
                                                $showForwardHere = true;
                                                $acctPreForwardPrinted = true;
                                            } elseif ($isLatest && !$acctPreForwardPrinted) {
                                                $showForwardHere = true;
                                                $acctPreForwardPrinted = true;
                                            }
                                        }

                                        ?>
                                        <div class="<?php echo $rowClass; ?>">
                                            <?php if ($isLatest): ?>
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
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
                                                    <div class="text-end text-muted">

                                                        <?php if ($showReceivedHere && $acctPreReceivedTs !== null): ?>
                                                            <div>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$acctPreReceivedTs)); ?></div>
                                                        <?php endif; ?>
                                                        <?php if ($showForwardHere && $acctPreForwardTs !== null): ?>
                                                            <div>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$acctPreForwardTs)); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
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
                                                    <div class="text-end text-muted">
                                                        <?php if ($showReceivedHere && $acctPreReceivedTs !== null): ?>
                                                            <div>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$acctPreReceivedTs)); ?></div>
                                                        <?php endif; ?>
                                                        <?php if ($showForwardHere && $acctPreForwardTs !== null): ?>
                                                            <div>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$acctPreForwardTs)); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                    $acctPreBudgetForwardTs = get_handoff_timestamp($handoffHistory, 'accounting_pre', 'budget', 'forwarded_at');
                    $acctPreBudgetRecvTs = get_handoff_timestamp($handoffHistory, 'accounting_pre', 'budget', 'received_at');
                    $acctPreBudgetOverdue = 0;
                    if ($acctPreBudgetForwardTs !== null) {
                        $endTs = $acctPreBudgetRecvTs !== null ? $acctPreBudgetRecvTs : time();
                        $delaySecs = max(0, (int)$endTs - (int)$acctPreBudgetForwardTs);
                        $acctPreBudgetOverdue = max(0, $delaySecs - (int)$handoffGraceSeconds);
                    }
                    ?>
                    <?php if ($acctPreBudgetOverdue > 0): ?>
                        <div class="handoff-between-due">
                            <div class="small text-danger fw-semibold">
                                <?php echo htmlspecialchars(format_elapsed_time((int)$acctPreBudgetOverdue)); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Budget Unit -->
                    <?php $budgetCompleted = (!empty($updatesByStage['budget']) || get_handoff_timestamp_first($handoffHistory, 'accounting_pre', 'budget', 'received_at') !== null); ?>
                    <div class="timeline-item <?php echo $budgetCompleted ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo $budgetCompleted ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <?php
                            // Elapsed from received_at (handoff) to forwarded_at (handoff) or latest Budget update
                            $elapsedBudget = '';
                            $startTs = get_handoff_timestamp($handoffHistory, 'accounting_pre', 'budget', 'received_at');
                            $endTs = get_handoff_timestamp($handoffHistory, 'budget', 'accounting_post', 'forwarded_at');
                            if ($endTs === null) {
                                $endTs = get_last_stage_timestamp($updatesByStage, 'budget');
                            }
                            if ($startTs !== null && $endTs !== null && $endTs >= $startTs) {
                                $elapsedBudget = format_elapsed_time(($endTs - $startTs) + (int)($handoffExtras['budget'] ?? 0));
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
                                    <?php
                                    $budgetReceivedTs = get_handoff_timestamp_first($handoffHistory, 'accounting_pre', 'budget', 'received_at');
                                    $budgetReceivedPrinted = false;
                                    $budgetForwardTs = get_handoff_timestamp($handoffHistory, 'budget', 'accounting_post', 'forwarded_at');
                                    $budgetForwardPrinted = false;
                                    ?>
                                    <?php $countBudget = count($updatesByStage['budget']); ?>
                                    <?php foreach ($updatesByStage['budget'] as $idx => $u): ?>
                                        <?php
                                        $isLatest = ($idx === $countBudget - 1);

                                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');

                                        $showReceivedHere = false;
                                        $showForwardHere = false;
                                        $updateTs = !empty($u['created_at']) ? strtotime((string)$u['created_at']) : false;
                                        $nextUpdateTs = false;
                                        if (($idx + 1) < $countBudget) {
                                            $next = $updatesByStage['budget'][$idx + 1] ?? null;
                                            if (!empty($next['created_at'])) {
                                                $nextUpdateTs = strtotime((string)$next['created_at']);
                                            }
                                        }
                                        if (!$budgetReceivedPrinted && $budgetReceivedTs !== null && $updateTs !== false && $updateTs >= (int)$budgetReceivedTs) {
                                            $showReceivedHere = true;
                                            $budgetReceivedPrinted = true;
                                        }

                                        if (!$budgetForwardPrinted && $budgetForwardTs !== null && $updateTs !== false) {
                                            $fwd = (int)$budgetForwardTs;
                                            if ($updateTs <= $fwd && ($nextUpdateTs === false || $nextUpdateTs > $fwd)) {
                                                $showForwardHere = true;
                                                $budgetForwardPrinted = true;
                                            } elseif ($idx === 0 && $updateTs > $fwd) {
                                                $showForwardHere = true;
                                                $budgetForwardPrinted = true;
                                            } elseif ($isLatest && !$budgetForwardPrinted) {
                                                $showForwardHere = true;
                                                $budgetForwardPrinted = true;
                                            }
                                        }

                                        ?>
                                        <div class="<?php echo $rowClass; ?>">
                                            <?php if ($isLatest): ?>
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
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
                                                    <div class="text-end text-muted">

                                                        <?php if ($showReceivedHere && $budgetReceivedTs !== null): ?>
                                                            <div>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$budgetReceivedTs)); ?></div>
                                                        <?php endif; ?>
                                                        <?php if ($showForwardHere && $budgetForwardTs !== null): ?>
                                                            <div>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$budgetForwardTs)); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
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
                                                    <div class="text-end text-muted">
                                                        <?php if ($showReceivedHere && $budgetReceivedTs !== null): ?>
                                                            <div>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$budgetReceivedTs)); ?></div>
                                                        <?php endif; ?>
                                                        <?php if ($showForwardHere && $budgetForwardTs !== null): ?>
                                                            <div>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$budgetForwardTs)); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                    $budgetAcctPostForwardTs = get_handoff_timestamp($handoffHistory, 'budget', 'accounting_post', 'forwarded_at');
                    $budgetAcctPostRecvTs = get_handoff_timestamp($handoffHistory, 'budget', 'accounting_post', 'received_at');
                    $budgetAcctPostOverdue = 0;
                    if ($budgetAcctPostForwardTs !== null) {
                        $endTs = $budgetAcctPostRecvTs !== null ? $budgetAcctPostRecvTs : time();
                        $delaySecs = max(0, (int)$endTs - (int)$budgetAcctPostForwardTs);
                        $budgetAcctPostOverdue = max(0, $delaySecs - (int)$handoffGraceSeconds);
                    }
                    ?>
                    <?php if ($budgetAcctPostOverdue > 0): ?>
                        <div class="handoff-between-due">
                            <div class="small text-danger fw-semibold">
                                <?php echo htmlspecialchars(format_elapsed_time((int)$budgetAcctPostOverdue)); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Accounting -->
                    <?php $acctPostCompleted = (!empty($updatesByStage['accounting_post']) || get_handoff_timestamp_first($handoffHistory, 'budget', 'accounting_post', 'received_at') !== null); ?>
                    <div class="timeline-item <?php echo $acctPostCompleted ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo $acctPostCompleted ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <?php
                            // Elapsed from received_at (handoff) to forwarded_at (handoff) or latest Accounting (post-budget) update
                            $elapsedAcctPost = '';
                            $startTs = get_handoff_timestamp($handoffHistory, 'budget', 'accounting_post', 'received_at');
                            $endTs = get_handoff_timestamp($handoffHistory, 'accounting_post', 'cashier', 'forwarded_at');
                            if ($endTs === null) {
                                $endTs = get_last_stage_timestamp($updatesByStage, 'accounting_post');
                            }
                            if ($startTs !== null && $endTs !== null && $endTs >= $startTs) {
                                $elapsedAcctPost = format_elapsed_time(($endTs - $startTs) + (int)($handoffExtras['accounting_post'] ?? 0));
                            }
                            ?>
                            <h6 class="timeline-title d-flex justify-content-between align-items-center">
                                <span>Accounting</span>
                                <span class="small text-muted"><?php echo $elapsedAcctPost ? htmlspecialchars($elapsedAcctPost) : ''; ?></span>
                            </h6>

                            <?php if (!empty($updatesByStage['accounting_post'])): ?>
                                <div class="timeline-history mt-2 p-2 border rounded bg-white">
                                    <div class="small text-muted mb-1">Update history</div>
                                    <?php
                                    $acctPostReceivedTs = get_handoff_timestamp_first($handoffHistory, 'budget', 'accounting_post', 'received_at');
                                    $acctPostReceivedPrinted = false;
                                    $acctPostForwardTs = get_handoff_timestamp($handoffHistory, 'accounting_post', 'cashier', 'forwarded_at');
                                    $acctPostForwardPrinted = false;
                                    ?>
                                    <?php $countAcctPost = count($updatesByStage['accounting_post']); ?>
                                    <?php foreach ($updatesByStage['accounting_post'] as $idx => $u): ?>
                                        <?php
                                        $isLatest = ($idx === $countAcctPost - 1);

                                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');

                                        $showReceivedHere = false;
                                        $showForwardHere = false;
                                        $updateTs = !empty($u['created_at']) ? strtotime((string)$u['created_at']) : false;
                                        $nextUpdateTs = false;
                                        if (($idx + 1) < $countAcctPost) {
                                            $next = $updatesByStage['accounting_post'][$idx + 1] ?? null;
                                            if (!empty($next['created_at'])) {
                                                $nextUpdateTs = strtotime((string)$next['created_at']);
                                            }
                                        }
                                        if (!$acctPostReceivedPrinted && $acctPostReceivedTs !== null && $updateTs !== false && $updateTs >= (int)$acctPostReceivedTs) {
                                            $showReceivedHere = true;
                                            $acctPostReceivedPrinted = true;
                                        }

                                        if (!$acctPostForwardPrinted && $acctPostForwardTs !== null && $updateTs !== false) {
                                            $fwd = (int)$acctPostForwardTs;
                                            if ($updateTs <= $fwd && ($nextUpdateTs === false || $nextUpdateTs > $fwd)) {
                                                $showForwardHere = true;
                                                $acctPostForwardPrinted = true;
                                            } elseif ($idx === 0 && $updateTs > $fwd) {
                                                $showForwardHere = true;
                                                $acctPostForwardPrinted = true;
                                            } elseif ($isLatest && !$acctPostForwardPrinted) {
                                                $showForwardHere = true;
                                                $acctPostForwardPrinted = true;
                                            }
                                        }

                                        ?>
                                        <div class="<?php echo $rowClass; ?>">
                                            <?php if ($isLatest): ?>
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
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
                                                    <div class="text-end text-muted">

                                                        <?php if ($showReceivedHere && $acctPostReceivedTs !== null): ?>
                                                            <div>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$acctPostReceivedTs)); ?></div>
                                                        <?php endif; ?>
                                                        <?php if ($showForwardHere && $acctPostForwardTs !== null): ?>
                                                            <div>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$acctPostForwardTs)); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
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
                                                    <div class="text-end text-muted">
                                                        <?php if ($showReceivedHere && $acctPostReceivedTs !== null): ?>
                                                            <div>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$acctPostReceivedTs)); ?></div>
                                                        <?php endif; ?>
                                                        <?php if ($showForwardHere && $acctPostForwardTs !== null): ?>
                                                            <div>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$acctPostForwardTs)); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                    $acctPostCashierForwardTs = get_handoff_timestamp($handoffHistory, 'accounting_post', 'cashier', 'forwarded_at');
                    $acctPostCashierRecvTs = get_handoff_timestamp($handoffHistory, 'accounting_post', 'cashier', 'received_at');
                    $acctPostCashierOverdue = 0;
                    if ($acctPostCashierForwardTs !== null) {
                        $endTs = $acctPostCashierRecvTs !== null ? $acctPostCashierRecvTs : time();
                        $delaySecs = max(0, (int)$endTs - (int)$acctPostCashierForwardTs);
                        $acctPostCashierOverdue = max(0, $delaySecs - (int)$handoffGraceSeconds);
                    }
                    ?>
                    <?php if ($acctPostCashierOverdue > 0): ?>
                        <div class="handoff-between-due">
                            <div class="small text-danger fw-semibold">
                                <?php echo htmlspecialchars(format_elapsed_time((int)$acctPostCashierOverdue)); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Cashier -->
                    <?php $cashierCompleted = (!empty($updatesByStage['cashier']) || get_handoff_timestamp_first($handoffHistory, 'accounting_post', 'cashier', 'received_at') !== null); ?>
                    <div class="timeline-item <?php echo $cashierCompleted ? 'completed' : 'pending'; ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-<?php echo $cashierCompleted ? 'check-circle' : 'circle'; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <?php
                            // Elapsed from received_at (handoff) to latest Cashier update
                            $elapsedCashier = '';
                            $startTs = get_handoff_timestamp($handoffHistory, 'accounting_post', 'cashier', 'received_at');
                            $endTs = get_last_stage_timestamp($updatesByStage, 'cashier');
                            if ($startTs !== null && $endTs !== null && $endTs >= $startTs) {
                                $elapsedCashier = format_elapsed_time(($endTs - $startTs) + (int)($handoffExtras['cashier'] ?? 0));
                            }
                            ?>
                            <h6 class="timeline-title d-flex justify-content-between align-items-center">
                                <span>Cashier</span>
                                <span class="small text-muted"><?php echo $elapsedCashier ? htmlspecialchars($elapsedCashier) : ''; ?></span>
                            </h6>
                            <?php if (!empty($updatesByStage['cashier'])): ?>
                                <div class="timeline-history mt-2 p-2 border rounded bg-white">
                                    <div class="small text-muted mb-1">Update history</div>
                                    <?php
                                    $cashierReceivedTs = get_handoff_timestamp_first($handoffHistory, 'accounting_post', 'cashier', 'received_at');
                                    $cashierReceivedPrinted = false;
                                    ?>
                                    <?php $countCashier = count($updatesByStage['cashier']); ?>
                                    <?php foreach ($updatesByStage['cashier'] as $idx => $u): ?>
                                        <?php
                                        $isLatest = ($idx === $countCashier - 1);

                                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');

                                        $showReceivedHere = false;
                                        $updateTs = !empty($u['created_at']) ? strtotime((string)$u['created_at']) : false;
                                        if (!$cashierReceivedPrinted && $cashierReceivedTs !== null && $updateTs !== false && $updateTs >= (int)$cashierReceivedTs) {
                                            $showReceivedHere = true;
                                            $cashierReceivedPrinted = true;
                                        }

                                        ?>
                                        <div class="<?php echo $rowClass; ?>">
                                            <?php if ($isLatest): ?>
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
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
                                                    <div class="text-end text-muted">

                                                        <?php if ($showReceivedHere && $cashierReceivedTs !== null): ?>
                                                            <div>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$cashierReceivedTs)); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
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
                                                    <div class="text-end text-muted">
                                                        <?php if ($showReceivedHere && $cashierReceivedTs !== null): ?>
                                                            <div>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$cashierReceivedTs)); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
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
</div>

<style>
@keyframes handoffBlinkBorder {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.0); }
    50% { box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.55); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.0); }
}

.handoff-overdue {
    border: 1px solid #dc3545 !important;
    border-radius: .375rem;
    padding: .375rem .5rem;
    animation: handoffBlinkBorder 1s infinite;
}

.timeline-item.handoff-between .timeline-marker {
    background: #e7f1ff;
    border-color: #0d6efd;
}

.timeline-item.handoff-between .timeline-content {
    background: #f8f9fa;
    border-left: 4px solid #0d6efd;
    border-radius: .5rem;
    padding: .5rem .75rem;
}

.timeline-item.handoff-between .timeline-title {
    font-size: .9rem;
    margin-bottom: .25rem;
}

.timeline-item.handoff-between .timeline-history {
    background: #ffffff;
    border-color: #cfe2ff !important;
}

.timeline .timeline-item {
    padding-top: .75rem;
    padding-bottom: .75rem;
}

.timeline .timeline-item:first-child {
    padding-top: 0;
}

.timeline-item.handoff-between .timeline-title {
    font-size: .9rem;
    margin-bottom: .25rem;
}

.timeline-item.handoff-between .timeline-history {
    background: #ffffff;
    border-color: #cfe2ff !important;
}

.handoff-between-due {
    display: flex;
    justify-content: center;
    width: 100%;
    margin: .25rem 0;
}

@media (max-width: 768px) {
    .timeline .timeline-item {
        padding-top: .6rem;
        padding-bottom: .6rem;
    }
}
</style>

<script>
// Periodically refresh only the Flow Timeline without reloading the whole page
document.addEventListener('DOMContentLoaded', function () {
    const refreshIntervalMs = window.POLL_INTERVALS.TRANSACTION_TIMELINE;

    function refreshTimeline() {
        if (window.SMART_POLLING_ENABLED && (document.visibilityState !== 'visible' || !document.hasFocus())) {
            return;
        }
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

    function refreshBasicInfo() {
        if (window.SMART_POLLING_ENABLED && (document.visibilityState !== 'visible' || !document.hasFocus())) {
            return;
        }
        const current = document.getElementById('basicInfoContainer');
        if (!current) return;

        fetch(window.location.href, { cache: 'no-store' })
            .then(function (response) { return response.text(); })
            .then(function (html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const next = doc.getElementById('basicInfoContainer');
                if (next) {
                    current.innerHTML = next.innerHTML;
                }
            })
            .catch(function () {
            });
    }

    setInterval(refreshTimeline, refreshIntervalMs);

    function refreshHandoffStatus() {
        if (window.SMART_POLLING_ENABLED && (document.visibilityState !== 'visible' || !document.hasFocus())) {
            return;
        }
        const current = document.getElementById('handoffStatusContainer');
        if (!current) return;

        fetch(window.location.href, { cache: 'no-store' })
            .then(function (response) { return response.text(); })
            .then(function (html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const next = doc.getElementById('handoffStatusContainer');
                if (next) {
                    current.innerHTML = next.innerHTML;
                    current.setAttribute('data-forwarded-ts', next.getAttribute('data-forwarded-ts') || '');
                    current.setAttribute('data-grace-ends-ts', next.getAttribute('data-grace-ends-ts') || '');
                    current.setAttribute('data-server-now-ts', next.getAttribute('data-server-now-ts') || '');
                    current.setAttribute('data-remaining-seconds', next.getAttribute('data-remaining-seconds') || '');
                    current.setAttribute('data-overdue-seconds', next.getAttribute('data-overdue-seconds') || '');
                    current.setAttribute('data-client-sync-ts', String(Math.floor(Date.now() / 1000)));
                    tickHandoffCountdown();
                }
            })
            .catch(function () {
            });
    }

    function tickHandoffCountdown() {
        const container = document.getElementById('handoffStatusContainer');
        if (!container) return;
        const el = container.querySelector('[data-handoff-countdown]');
        if (!el) {
            container.classList.remove('handoff-overdue');
            return;
        }
        const baseRemaining = parseInt(container.getAttribute('data-remaining-seconds') || '', 10);
        const baseOverdue = parseInt(container.getAttribute('data-overdue-seconds') || '0', 10);
        const clientSync = parseInt(container.getAttribute('data-client-sync-ts') || '', 10);
        const nowClient = Math.floor(Date.now() / 1000);

        if (isNaN(baseRemaining) || baseRemaining < 0) {
            container.classList.remove('handoff-overdue');
            return;
        }

        const elapsed = clientSync ? Math.max(0, nowClient - clientSync) : 0;
        const remainingRaw = baseRemaining - elapsed;
        const remaining = Math.max(0, remainingRaw);

        if (remainingRaw > 0) {
            const hh = String(Math.floor(remaining / 3600)).padStart(2, '0');
            const mm = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            const ss = String(remaining % 60).padStart(2, '0');
            el.textContent = 'Grace ends in ' + hh + ':' + mm + ':' + ss;
            container.classList.remove('handoff-overdue');
        } else {
            const overdueRaw = (isNaN(baseOverdue) ? 0 : Math.max(0, baseOverdue)) + elapsed;
            const hh = String(Math.floor(overdueRaw / 3600)).padStart(2, '0');
            const mm = String(Math.floor((overdueRaw % 3600) / 60)).padStart(2, '0');
            const ss = String(overdueRaw % 60).padStart(2, '0');
            el.textContent = 'Grace period exceeded by ' + hh + ':' + mm + ':' + ss;
            container.classList.add('handoff-overdue');
        }
    }

    var initialHandoffContainer = document.getElementById('handoffStatusContainer');
    if (initialHandoffContainer) {
        initialHandoffContainer.setAttribute('data-client-sync-ts', String(Math.floor(Date.now() / 1000)));
    }

    refreshHandoffStatus();
    tickHandoffCountdown();

    refreshBasicInfo();

    setInterval(refreshHandoffStatus, refreshIntervalMs);
    setInterval(tickHandoffCountdown, 1000);
    setInterval(refreshBasicInfo, refreshIntervalMs);

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
