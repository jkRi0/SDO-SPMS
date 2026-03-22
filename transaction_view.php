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

$settings = [];
try {
    $db->exec('CREATE TABLE IF NOT EXISTS app_settings (
        setting_key VARCHAR(128) NOT NULL,
        setting_value TEXT NOT NULL,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (setting_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');

    // Check for email column in users table
    $stmtColUser = $db->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmtColUser->execute(['users', 'email']);
    $hasEmail = (int)$stmtColUser->fetchColumn() > 0;
    if (!$hasEmail) {
        $db->exec('ALTER TABLE users ADD COLUMN email VARCHAR(255) NULL AFTER username');
    }

    $stmtAll = $db->query('SELECT setting_key, setting_value FROM app_settings');
    while ($row = $stmtAll->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
}

$handoffGraceSeconds = 60;
if (isset($settings['handoff_grace_seconds'])) {
    $handoffGraceSeconds = max(0, (int)$settings['handoff_grace_seconds']);
}

$lastNotifyTitle = $settings['last_notify_title'] ?? 'Payment status update';
$lastNotifyMessage = $settings['last_notify_message'] ?? '';

// Retrieve current user's email for CC pre-fill
$currentUserEmail = '';
try {
    $stmtUser = $db->prepare('SELECT email FROM users WHERE id = ? LIMIT 1');
    $stmtUser->execute([$_SESSION['user_id']]);
    $currentUserEmail = $stmtUser->fetchColumn() ?: '';
} catch (Exception $e) {
}

$updatesByStage = [
    'procurement' => [],
    'supply' => [],
    'accounting' => [],
    'budget' => [],
    'cashier' => [],
];

$handoffOpen = null;
$handoffHistory = [];
$handoffExtras = [
    'procurement' => 0,
    'supply' => 0,
    'accounting' => 0,
    'budget' => 0,
    'cashier' => 0,
];

try {
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

$deptForRoleEdit = [
    'procurement' => 'procurement',
    'supply' => 'supply',
    'accounting' => 'accounting',
    'budget' => 'budget',
    'cashier' => 'cashier',
];
$roleDeptEdit = $deptForRoleEdit[$role] ?? '';

$ownerDeptEdit = 'procurement';
if (!empty($handoffOpen) && !empty($handoffOpen['to_dept'])) {
    $ownerDeptEdit = ''; 
} elseif (!empty($handoffHistory)) {
    $lastH = end($handoffHistory);
    if (!empty($lastH['received_at']) && !empty($lastH['to_dept'])) {
        $ownerDeptEdit = (string)$lastH['to_dept'];
    }
    reset($handoffHistory);
}

$canEditUpdatesUi = (
    $role !== 'supplier'
    && $role !== 'admin'
    && $roleDeptEdit !== ''
    && $ownerDeptEdit !== ''
    && $roleDeptEdit === $ownerDeptEdit
);

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
    $transaction['acct_date'] ?? null,
    $transaction['budget_ors_date'] ?? null,
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

if (!function_exists('is_dept_forwardable')) {
    function is_dept_forwardable(array $transaction, string $dept): bool
    {
        $forwardableFieldsByDept = [
            'procurement' => ['proc_status', 'proc_remarks'],
            'supply' => ['supply_status', 'supply_remarks'],
            'accounting' => ['acct_status', 'acct_remarks'],
            'budget' => ['budget_status', 'budget_remarks'],
            'cashier' => ['cashier_status', 'cashier_remarks'],
        ];

        $fields = $forwardableFieldsByDept[$dept] ?? [];
        foreach ($fields as $f) {
            $val = trim((string)($transaction[$f] ?? ''));
            if ($val !== '') {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('dept_ui_label')) {
    function dept_ui_label(string $dept): string
    {
        $d = strtolower(trim($dept));
        if ($d === 'accounting') {
            return 'ACCOUNTING';
        }
        return strtoupper($dept);
    }
}

if (!function_exists('render_handoff_history')) {
    function render_handoff_history(array $handoffHistory, int $handoffGraceSeconds): void
    {
        if (empty($handoffHistory)) {
            return;
        }
        ?>
        <div class="timeline-item completed handoff-between">
            <div class="timeline-marker">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="timeline-content">
                <h6 class="timeline-title d-flex justify-content-between align-items-center">
                    <span>Handoff History</span>
                    <span class="small text-muted"></span>
                </h6>
                <div class="timeline-history mt-1 p-2 border rounded bg-white">
                    <?php foreach ($handoffHistory as $idx => $h): ?>
                        <?php
                        $fromDept = (string)($h['from_dept'] ?? '');
                        $toDept = (string)($h['to_dept'] ?? '');
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
                        $rowClass = 'timeline-history-item py-1 px-2 small ' . ($idx === (count($handoffHistory) - 1) ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');
                        ?>
                        <div class="<?php echo $rowClass; ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">
                                    <span class="text-primary"><?php echo strtoupper(htmlspecialchars(dept_ui_label($fromDept))); ?></span>
                                    <i class="fas fa-arrow-right mx-1 text-muted small"></i>
                                    <span class="text-success"><?php echo strtoupper(htmlspecialchars(dept_ui_label($toDept))); ?></span>
                                </span>
                                <?php if ($receivedAt !== ''): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle small">Completed</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle small">In Transit</span>
                                <?php endif; ?>
                            </div>
                            <div class="row mt-1 g-0 small text-muted">
                                <div class="col-6">Forwarded: <?php echo htmlspecialchars($forwardedAt); ?></div>
                                <div class="col-6 text-end">
                                    <?php if ($receivedAt !== ''): ?>
                                        Received: <?php echo htmlspecialchars($receivedAt); ?>
                                    <?php else: ?>
                                        <span class="text-danger fw-bold">PENDING</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($overdueSecs > 0): ?>
                                <div class="text-danger fw-semibold mt-1 small">
                                    <i class="fas fa-clock me-1"></i>Overdue: <?php echo htmlspecialchars(format_elapsed_time($overdueSecs)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
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
        $fromLabel = dept_ui_label($fromDept);
        $toLabel = dept_ui_label($toDept);
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
    if ($role === 'cashier' && isset($_POST['notify_supplier']) && $_POST['notify_supplier'] === '1' && !isset($_POST['save_updates'])) {
        try {
            $notifyStmt = $db->prepare('INSERT INTO notifications (supplier_id, transaction_id, title, message, link, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $defaultTitle = 'Payment status update';
            $title = trim((string)($_POST['notify_title'] ?? ''));
            if ($title === '') {
                $title = $defaultTitle;
            }
            $defaultMessage = 'Your PO ' . ($transaction['po_number'] ?? '') . ' is now marked as COMPLETED. Please check the portal for details.';
            $message = trim((string)($_POST['notify_message'] ?? ''));
            $ccRaw = trim((string)($_POST['notify_cc'] ?? ''));
            $ccEmails = [];
            if ($ccRaw !== '') {
                $parts = preg_split('/[\s,;]+/', $ccRaw, -1, PREG_SPLIT_NO_EMPTY);
                if (is_array($parts)) {
                    foreach ($parts as $p) {
                        $p = strtolower(trim((string)$p));
                        if ($p === '') {
                            continue;
                        }
                        if (!filter_var($p, FILTER_VALIDATE_EMAIL)) {
                            $error = 'Invalid CC email address: ' . $p;
                            break;
                        }
                        $ccEmails[] = $p;
                    }
                }
            }
            if ($message === '') {
                $message = $defaultMessage;
            }
            if ($error !== '') {
                throw new Exception('notify_cc_invalid');
            }
            $link = 'transaction_view.php?id=' . $transaction['id'];
            $notifyStmt->execute([
                $transaction['supplier_id'],
                $transaction['id'],
                $title,
                $message,
                $link,
            ]);

            // Persist the last used title and message in app_settings
            $stmtSetTitle = $db->prepare('INSERT INTO app_settings (setting_key, setting_value) VALUES (?, ?) 
                                         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
            $stmtSetTitle->execute(['last_notify_title', $title]);
            
            $stmtSetMsg = $db->prepare('INSERT INTO app_settings (setting_key, setting_value) VALUES (?, ?) 
                                        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
            $stmtSetMsg->execute(['last_notify_message', $message]);

            // Also email supplier if an email address is available (e.g. Google OAuth suppliers)
            $emailStmt = $db->prepare('SELECT email FROM suppliers WHERE id = ? LIMIT 1');
            $emailStmt->execute([$transaction['supplier_id']]);
            $supplierRow = $emailStmt->fetch();

            if ($supplierRow && !empty($supplierRow['email'])) {
                $toEmail = strtolower(trim($supplierRow['email']));
                $emailSubject = $title;
                $emailBody = '<p>' . htmlspecialchars($message) . '</p>' .
                    '<p><a href="' . htmlspecialchars(BASE_URL . $link) . '">View details in the STMS portal</a></p>';
                
                $ccParam = !empty($ccEmails) ? $ccEmails : null;
                
                send_supplier_email($toEmail, $emailSubject, $emailBody, $ccParam);
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

    if (isset($_POST['handoff_action']) && $role !== 'supplier' && $role !== 'admin') {
        $action = (string)($_POST['handoff_action'] ?? '');

        $allowedDepts = ['procurement', 'supply', 'accounting', 'budget', 'cashier'];
        $fromDept = 'procurement';
        if (!empty($handoffHistory)) {
            $lastH = end($handoffHistory);
            if (!empty($lastH['received_at']) && !empty($lastH['to_dept'])) {
                $fromDept = (string)$lastH['to_dept'];
            }
            reset($handoffHistory);
        }
        $fromDept = strtolower(trim((string)$fromDept));
        if (!in_array($fromDept, $allowedDepts, true)) {
            $fromDept = 'procurement';
        }

        $selectedToDept = strtolower(trim((string)($_POST['handoff_to_dept'] ?? '')));
        if (!in_array($selectedToDept, $allowedDepts, true)) {
            $selectedToDept = '';
        }

        if ($action === 'receive') {
            $roleDept = '';
            if ($role === 'procurement') $roleDept = 'procurement';
            elseif ($role === 'supply') $roleDept = 'supply';
            elseif ($role === 'accounting') $roleDept = 'accounting';
            elseif ($role === 'budget') $roleDept = 'budget';
            elseif ($role === 'cashier') $roleDept = 'cashier';

            if ($roleDept !== '' && $handoffOpen && $handoffOpen['to_dept'] === $roleDept) {
                $stmtRecv = $db->prepare('UPDATE transaction_handoffs SET received_at = NOW() WHERE id = ?');
                $stmtRecv->execute([$handoffOpen['id']]);

                try {
                    $poNum = $transaction['po_number'] ?? '';
                    $link = 'transaction_view.php?id=' . (int)$id;
                    $recvTitle = 'Handoff Received';
                    $recvMsg = dept_ui_label($roleDept) . ' has received PO ' . $poNum . ' from ' . dept_ui_label($handoffOpen['from_dept']) . '.';
                    create_dept_notification($db, $handoffOpen['from_dept'], $id, $recvTitle, $recvMsg, $link, $roleDept);
                } catch (Exception $e) {
                }

                try {
                    create_log($db, $_SESSION['user_id'] ?? null, 'transaction_handoff_receive', 'transaction', (int)$id, json_encode([
                        'transaction_id' => (int)$id,
                        'po_number' => (string)($transaction['po_number'] ?? ''),
                        'from_dept' => (string)$handoffOpen['from_dept'],
                        'to_dept' => (string)$handoffOpen['to_dept'],
                    ]));
                } catch (Exception $e) {
                }

                header('Location: transaction_view.php?id=' . $id . '&handoff_received=1');
                exit;
            }
        }

        if ($action === 'forward') {
            $roleDept = '';
            if ($role === 'procurement') $roleDept = 'procurement';
            elseif ($role === 'supply') $roleDept = 'supply';
            elseif ($role === 'accounting') $roleDept = 'accounting';
            elseif ($role === 'budget') $roleDept = 'budget';
            elseif ($role === 'cashier') $roleDept = 'cashier';

            if (
                $roleDept !== ''
                && $fromDept !== ''
                && $selectedToDept !== ''
                && $selectedToDept !== $fromDept
                && $roleDept === $fromDept
                && !$handoffOpen
                && is_dept_forwardable($transaction, $fromDept)
            ) {
                $stmtOpen = $db->prepare('SELECT id FROM transaction_handoffs WHERE transaction_id = ? AND received_at IS NULL LIMIT 1');
                $stmtOpen->execute([$id]);
                $open = $stmtOpen->fetch(PDO::FETCH_ASSOC);

                if (!$open) {
                    $stmtIns = $db->prepare('INSERT INTO transaction_handoffs (transaction_id, from_dept, to_dept, forwarded_at, created_by_user_id)
                                             VALUES (?, ?, ?, NOW(), ?)');
                    $stmtIns->execute([$id, $fromDept, $selectedToDept, (int)($_SESSION['user_id'] ?? 0)]);

                    try {
                        create_log($db, $_SESSION['user_id'] ?? null, 'transaction_handoff_forward', 'transaction', (int)$id, json_encode([
                            'transaction_id' => (int)$id,
                            'po_number' => (string)($transaction['po_number'] ?? ''),
                            'from_dept' => (string)$fromDept,
                            'to_dept' => (string)$selectedToDept,
                        ]));
                    } catch (Exception $e) {
                    }

                    try {
                        $poNum = $transaction['po_number'] ?? '';
                        $link = 'transaction_view.php?id=' . (int)$id;
                        $pendingTitle = 'Handoff Forwarded';
                        $pendingMsg = dept_ui_label($fromDept) . ' forwarded PO ' . $poNum . ' to ' . dept_ui_label($selectedToDept) . '. Please receive it.';
                        create_dept_notification_once($db, $selectedToDept, $id, $pendingTitle, $pendingMsg, $link, 120, $fromDept);
                    } catch (Exception $e) {
                    }
                }
            }
        }

        if ($action === 'forward' || $action === 'receive') {
            header('Location: transaction_view.php?id=' . $id);
            exit;
        }
    }

    if (isset($_POST['save_updates'])) {
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
                    if ($normalizedSupplyStatus === 'PARTIAL DELIVERY') {
                        $supplyStatus = 'PARTIAL DELIVERY';
                    }
                    if ($normalizedSupplyStatus === 'PARTIAL DELIVER') {
                        $supplyStatus = 'PARTIAL DELIVERY';
                        $normalizedSupplyStatus = 'PARTIAL DELIVERY';
                    }

                    if ($normalizedSupplyStatus === 'PARTIAL DELIVERY') {
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

                        if ($normalizedSupplyStatus === 'PARTIAL DELIVERY') {
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
                $acctStatus = trim((string)($_POST['acct_status'] ?? ''));
                $acctRemarksBase = trim($_POST['acct_remarks'] ?? '');
                $acctDvAmount    = trim($_POST['acct_dv_amount'] ?? '');
                $acctDvNumber    = trim($_POST['acct_dv_number'] ?? '');
                $acctDvDate      = trim($_POST['acct_dv_date'] ?? '');

                $acctStatusNorm = strtoupper(trim((string)$acctStatus));
                $isMeaningfulAcctUpdate = (trim((string)$acctStatus) !== '' || trim((string)$acctRemarksBase) !== '');
                if ($acctStatusNorm === 'FOR VOUCHER' && $isMeaningfulAcctUpdate) {
                    if ($acctDvAmount === '') {
                        $error = 'DV Amount is required.';
                    } elseif (!is_numeric($acctDvAmount)) {
                        $error = 'DV Amount must be a valid number.';
                    } elseif ($acctDvNumber === '') {
                        $error = 'DV Number is required.';
                    } elseif (!preg_match('/^\d+$/', $acctDvNumber)) {
                        $error = 'DV Number must be numbers only.';
                    } elseif ($acctDvDate === '') {
                        $error = 'DV Date is required.';
                    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $acctDvDate)) {
                        $error = 'DV Date is invalid.';
                    }
                }

                if ($error === '') {
                    $fields[] = 'acct_status = ?';
                    $fields[] = 'acct_remarks = ?';
                    $fields[] = 'acct_dv_number = ?';
                    $fields[] = 'acct_dv_date = ?';
                    $fields[] = 'acct_dv_amount = ?';
                    $fields[] = 'acct_date = CURDATE()';

                    $params[] = $acctStatus;
                    $params[] = $acctRemarksBase;
                    $params[] = $acctDvNumber !== '' ? $acctDvNumber : null;
                    $params[] = $acctDvDate !== '' ? $acctDvDate : null;
                    $params[] = $acctDvAmount !== '' ? $acctDvAmount : null;

                    $logStage = 'accounting';
                    $logStatus = $acctStatus;
                    $logRemarks = $acctRemarksBase;
                }
            } elseif ($role === 'budget') {
                $budgetOrsNumber = trim($_POST['budget_ors_number'] ?? '');
                $budgetOrsDate = trim($_POST['budget_ors_date'] ?? '');
                $budgetStatus = trim($_POST['budget_status'] ?? '');
                $budgetDemandability = trim($_POST['budget_demandability'] ?? '');
                $budgetRemarks = trim($_POST['budget_remarks'] ?? '');

                if ($budgetOrsNumber !== '' && !preg_match('/^\d+$/', $budgetOrsNumber)) {
                    $error = 'ORS Number must be numbers only.';
                } else {
                    $fields[] = 'budget_ors_number = ?';
                    $fields[] = 'budget_ors_date = ?';
                    $fields[] = 'budget_status = ?';
                    $fields[] = 'budget_demandability = ?';
                    $fields[] = 'budget_remarks = ?';
                    $params[] = $budgetOrsNumber;
                    $params[] = $budgetOrsDate;
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
                if (!$canEditUpdatesUi) {
                    $error = 'You cannot edit this transaction. Current owner: ' . ($ownerDeptEdit ? dept_ui_label($ownerDeptEdit) : 'None');
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
                            $details['ors_number'] = (string)($budgetOrsNumber ?? '');
                            $details['ors_date'] = (string)($budgetOrsDate ?? '');
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
                        'accounting' => ['procurement', 'supply'],
                        'budget' => ['procurement', 'supply', 'accounting'],
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
                    } elseif ($logStage === 'accounting') {
                        $old = trim((string)($transaction['acct_status'] ?? ''));
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
                    }

                    // Completed should notify the next department in the workflow.
                    if ($statusUpper === 'COMPLETED') {
                        $nextRoleByStage = [
                            'procurement' => 'supply',
                            'supply' => 'accounting',
                            'accounting' => 'budget',
                            'budget' => 'accounting',
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
            $error = 'Error updating transaction: ' . $e->getMessage();
            try {
                error_log('Transaction update error (id=' . (int)$id . ', role=' . (string)($role ?? '') . '): ' . $e->getMessage());
            } catch (Exception $ignored) {
            }
        }
    } // End of save_updates block
} // End of POST method check

    // Determine if cashier can proceed to Landbank
    // Temporarily allow cashier to always proceed (no status/DV verification)
    $canProceedLandbank = ($role === 'cashier');

    $poNumTab = trim((string)($transaction['po_number'] ?? ''));
    $txLabelTab = $poNumTab !== '' ? ('PO ' . $poNumTab) : ('Transaction #' . (int)$id);
    $viewerDeptTab = strtoupper(trim((string)$role));
    if ($viewerDeptTab === '') {
        $viewerDeptTab = 'STMS';
    }
    $pageTitle = $viewerDeptTab . ' - ' . $txLabelTab . ' - STMS';
$poNumTab = trim((string)($transaction['po_number'] ?? ''));
$txLabelTab = $poNumTab !== '' ? ('PO ' . $poNumTab) : ('Transaction #' . (int)$id);
$viewerDeptTab = strtoupper(trim((string)$role));
if ($viewerDeptTab === '') {
    $viewerDeptTab = 'STMS';
}
$pageTitle = $viewerDeptTab . ' - ' . $txLabelTab . ' - STMS';

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
        <div class="alert alert-danger py-2 alert-dismissible fade show auto-dismiss" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success py-2 alert-dismissible fade show auto-dismiss" role="alert">
            <?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

<div class="d-flex flex-wrap gap-3" style="">
    <div class="card mb-3" style="flex: 1 1 560px; min-width: 560px;">
        <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
            <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
            <div id="basicInfoContainer">
                <?php
                $programTitle = trim((string)($transaction['program_title'] ?? ''));
                $expectedText = trim((string)($transaction['expected_date'] ?? ''));
                $poType = trim((string)($transaction['po_type'] ?? ''));

                $hasCoverage = (!empty($transaction['coverage_start']) || !empty($transaction['coverage_end']));

                $hasSupplyGroup = (
                    !empty($transaction['supply_delivery_receipt'])
                    || !empty($transaction['supply_sales_invoice'])
                    || !empty($transaction['supply_partial_delivery_date'])
                    || !empty($transaction['supply_delivery_date'])
                );

                $hasDvGroup = (
                    !empty($transaction['acct_dv_amount'])
                    || !empty($transaction['acct_dv_number'])
                    || !empty($transaction['acct_dv_date'])
                );

                $hasOrsGroup = (
                    !empty($transaction['budget_ors_number'])
                    || !empty($transaction['budget_ors_date'])
                    || !empty($transaction['budget_demandability'])
                );

                $hasCashierGroup = (
                    !empty($transaction['cashier_or_number'])
                    || !empty($transaction['cashier_or_date'])
                    || !empty($transaction['cashier_landbank_ref'])
                    || !empty($transaction['cashier_payment_date'])
                );
                ?>

                <?php if ($programTitle !== ''): ?>
                    <div class="border rounded p-3 mb-2 bg-white">
                        <div><strong>Program Title:</strong> <?php echo htmlspecialchars($programTitle); ?></div>
                    </div>
                <?php endif; ?>

                <div class="border rounded p-3 mb-2 bg-white">
                    <div><strong>Date Created:</strong> <?php echo date('m/d/Y H:i:s', strtotime($transaction['created_at'])); ?></div>
                    <?php if ($hasCoverage): ?>
                        <div class="mt-1"><strong>Date Coverage:</strong> <?php echo htmlspecialchars($coverageDisplay); ?></div>
                    <?php endif; ?>
                    <?php if ($expectedText !== ''): ?>
                        <div class="mt-1"><strong>Expected Date:</strong> <?php echo htmlspecialchars($expectedText); ?></div>
                    <?php endif; ?>
                    <div class="mt-1"><strong>PO (Gross Amount):</strong> ₱ <?php echo number_format((float)$transaction['amount'], 2); ?></div>
                    <?php if ($poType !== ''): ?>
                        <div class="mt-1"><strong>Transaction Type:</strong> <?php echo htmlspecialchars($poType); ?></div>
                    <?php endif; ?>
                </div>

                <?php if ($hasSupplyGroup): ?>
                    <div class="border rounded p-3 mb-2 bg-white">
                        <?php if (!empty($transaction['supply_delivery_receipt'])): ?>
                            <div><strong>Delivery Receipt:</strong> <?php echo htmlspecialchars($transaction['supply_delivery_receipt']); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($transaction['supply_sales_invoice'])): ?>
                            <div class="mt-1"><strong>Sales Invoice:</strong> <?php echo htmlspecialchars($transaction['supply_sales_invoice']); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($transaction['supply_partial_delivery_date'])): ?>
                            <div class="mt-1"><strong>Partial Delivery Date:</strong> <?php echo htmlspecialchars($transaction['supply_partial_delivery_date']); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($transaction['supply_delivery_date'])): ?>
                            <div class="mt-1"><strong>Delivery Date:</strong> <?php echo htmlspecialchars($transaction['supply_delivery_date']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($hasDvGroup): ?>
                    <div class="border rounded p-3 mb-2 bg-white">
                        <?php if (!empty($transaction['acct_dv_amount'])): ?>
                            <div><strong>DV Amount:</strong> ₱ <?php echo number_format((float)$transaction['acct_dv_amount'], 2); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($transaction['acct_dv_number'])): ?>
                            <div class="mt-1"><strong>DV Number:</strong> <?php echo htmlspecialchars($transaction['acct_dv_number']); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($transaction['acct_dv_date'])): ?>
                            <div class="mt-1"><strong>DV Date:</strong> <?php echo htmlspecialchars($transaction['acct_dv_date']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($hasOrsGroup): ?>
                    <div class="border rounded p-3 mb-2 bg-white">
                        <?php if (!empty($transaction['budget_ors_number'])): ?>
                            <div><strong>ORS Number:</strong> <?php echo htmlspecialchars($transaction['budget_ors_number']); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($transaction['budget_ors_date'])): ?>
                            <div class="mt-1"><strong>ORS Date:</strong> <?php echo htmlspecialchars($transaction['budget_ors_date']); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($transaction['budget_demandability'])): ?>
                            <div class="mt-1"><strong>Demandability:</strong> <?php echo htmlspecialchars($transaction['budget_demandability']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($hasCashierGroup): ?>
                    <div class="border rounded p-3 mb-0 bg-white">
                        <?php if (!empty($transaction['cashier_or_number'])): ?>
                            <div><strong>ACIC Number:</strong> <?php echo htmlspecialchars($transaction['cashier_or_number']); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($transaction['cashier_or_date'])): ?>
                            <div class="mt-1"><strong>ACIC Date:</strong> <?php echo htmlspecialchars($transaction['cashier_or_date']); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($transaction['cashier_landbank_ref'])): ?>
                            <div class="mt-1"><strong>Amount:</strong> ₱ <?php
                                $cashierAmountDisplay = trim((string)$transaction['cashier_landbank_ref']);
                                $cashierAmountClean = str_replace(',', '', $cashierAmountDisplay);
                                if ($cashierAmountClean !== '' && is_numeric($cashierAmountClean)) {
                                    echo htmlspecialchars(number_format((float)$cashierAmountClean, 2));
                                } else {
                                    echo htmlspecialchars($cashierAmountDisplay);
                                }
                            ?></div>
                        <?php endif; ?>
                        <?php if (!empty($transaction['cashier_payment_date'])): ?>
                            <div class="mt-1"><strong>Payment Date:</strong> <?php echo htmlspecialchars($transaction['cashier_payment_date']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <hr class="my-4">
            <h6 class="mb-3">Update (<?php echo htmlspecialchars(strtoupper($role)); ?>)</h6>
            <?php if ($role === 'supplier'): ?>
                <p class="text-muted mb-0">Suppliers can only view the status of their transactions.</p>
            <?php else: ?>
                <?php
                $deptForRoleUi = [
                    'procurement' => 'procurement',
                    'supply' => 'supply',
                    'accounting' => 'accounting',
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

                $fromDeptUi = 'procurement';
                if (!empty($handoffHistory)) {
                    $lastH = end($handoffHistory);
                    if (!empty($lastH['received_at']) && !empty($lastH['to_dept'])) {
                        $fromDeptUi = (string)$lastH['to_dept'];
                    }
                    reset($handoffHistory);
                }

                $isFromDeptForwardableUi = ($fromDeptUi !== '' && is_dept_forwardable($transaction, $fromDeptUi));

                $canForward = ($roleDeptUi !== '' && $roleDeptUi === $fromDeptUi && $isFromDeptForwardableUi);
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
                        $handoffFromLabel = dept_ui_label($handoffFromDept);
                        $handoffToLabel = dept_ui_label($handoffToDept);
                        if ($roleDeptUi === $handoffToDept) {
                            $handoffBannerText = 'Pending handoff from ' . $handoffFromLabel . ' to ' . $handoffToLabel . '.';
                        } else {
                            $handoffBannerText = 'Waiting for ' . $handoffToLabel . ' to receive (forwarded by ' . $handoffFromLabel . ')';
                        }
                    }
                }

                if (isset($_GET['handoff_received']) && (string)($_GET['handoff_received'] ?? '') === '1') {
                    $flashFrom = (string)($_GET['handoff_from'] ?? '');
                    $flashTo = (string)($_GET['handoff_to'] ?? '');
                    if ($roleDeptUi !== '' && $flashFrom !== '' && $flashTo !== '' && ($roleDeptUi === $flashFrom || $roleDeptUi === $flashTo)) {
                        $handoffReceivedFlash = 'Handoff received: ' . dept_ui_label($flashFrom) . ' → ' . dept_ui_label($flashTo) . '.';
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
                    'accounting' => 2,
                    'budget' => 3,
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

                <?php if ($role === 'admin'): ?>
                    <div class="d-flex justify-content-end align-items-center flex-wrap gap-2 mb-2">
                        <form method="post" class="m-0 d-flex align-items-center gap-2">
                            <input type="hidden" name="action" value="set_handoff_grace">
                            <span class="text-muted small">Grace Period for forwarding and receiving. (seconds)</span>
                            <input type="number" class="form-control form-control-sm" name="handoff_grace_seconds" min="0" step="1" value="<?php echo (int)$handoffGraceSeconds; ?>" style="width: 140px;" aria-label="Handoff grace seconds">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                        </form>
                    </div>
                <?php endif; ?>

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
                        <?php if ($roleDeptUi !== '' && ($roleDeptUi === $handoffFromDept || $roleDeptUi === $handoffToDept) && $handoffOpen && $handoffForwardedTs && $receiveCountdown !== ''): ?>
                            <span class="ms-2 text-danger" data-handoff-countdown><?php echo htmlspecialchars($receiveCountdown); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <?php if ($canForward && !$handoffOpen): ?>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="forwardDropdownBtn">
                                    Forward
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="forwardDropdownBtn">
                                    <li class="dropdown-header small text-muted">Forward PO to:</li>
                                    <?php
                                    $allDepts = ['procurement', 'supply', 'accounting', 'budget', 'cashier'];
                                    foreach ($allDepts as $d) {
                                        if ($d === $fromDeptUi) {
                                            continue;
                                        }
                                        ?>
                                        <li>
                                            <form method="post" class="m-0" id="forwardForm_<?php echo htmlspecialchars($d); ?>">
                                                <input type="hidden" name="handoff_action" value="forward">
                                                <input type="hidden" name="handoff_to_dept" value="<?php echo htmlspecialchars($d); ?>">
                                                <button type="submit" class="dropdown-item py-1" onclick="this.disabled=true; this.form.submit();">
                                                    <i class="fas fa-arrow-right me-2 small text-primary"></i>
                                                    <?php echo strtoupper(htmlspecialchars($d)); ?>
                                                </button>
                                            </form>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
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
                    <?php if ($role === 'procurement' && $roleDeptEdit === $ownerDeptEdit): ?>
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
                    <?php elseif ($role === 'supply' && $roleDeptEdit === $ownerDeptEdit): ?>
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
                            <select name="supply_status" class="form-control" id="supplyStatusSelect">
                                <?php
                                $currentSupplyStatus = $transaction['supply_status'] ?? '';
                                $normalizedCurrentSupplyStatus = strtoupper(trim((string)$currentSupplyStatus));
                                // Empty placeholder
                                echo '<option value="">-- Select status --</option>';
                                $supplyOptions = ['PARTIAL DELIVERY', 'COMPLETED'];
                                foreach ($supplyOptions as $opt) {
                                    $selected = '';
                                    if ($opt === 'PARTIAL DELIVERY') {
                                        $selected = ($normalizedCurrentSupplyStatus === 'PARTIAL DELIVER' || $normalizedCurrentSupplyStatus === 'PARTIAL DELIVERY') ? 'selected' : '';
                                    } else {
                                        $selected = ($normalizedCurrentSupplyStatus === strtoupper($opt)) ? 'selected' : '';
                                    }
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
                                    partialGroup.style.display = (val === 'PARTIAL DELIVER' || val === 'PARTIAL DELIVERY') ? '' : 'none';
                                    deliveryGroup.style.display = (val === 'COMPLETED') ? '' : 'none';
                                }

                                select.addEventListener('change', updateVisibility);
                                updateVisibility();
                            })();
                        </script>
                    <?php elseif ($role === 'accounting' && $roleDeptEdit === $ownerDeptEdit): ?>
                        <div class="border rounded p-3 mb-2">
                            <h6 class="mb-2">Accounting</h6>
                            <div class="mb-2">
                                <label class="form-label mb-1">Status</label>
                                <?php $currentAcctStatus = (string)($transaction['acct_status'] ?? ''); ?>
                                <select name="acct_status" class="form-control">
                                    <?php
                                    echo '<option value="">-- Select status --</option>';
                                    $acctOptions = ['FOR ORS', 'INCOMPLETE', 'FOR VOUCHER'];
                                    foreach ($acctOptions as $opt) {
                                        $selected = ($currentAcctStatus === $opt) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($opt) . '" ' . $selected . '>' . htmlspecialchars($opt) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label mb-1">Remarks</label>
                                <textarea name="acct_remarks" class="form-control" rows="2" placeholder="Enter accounting remarks"><?php echo htmlspecialchars($transaction['acct_remarks'] ?? ''); ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label mb-1">DV Number</label>
                                    <input type="text" name="acct_dv_number" class="form-control"
                                            inputmode="numeric" pattern="\d*" oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                                            value="<?php echo htmlspecialchars($transaction['acct_dv_number'] ?? ''); ?>"
                                            placeholder="Enter DV number">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label mb-1">DV Date</label>
                                    <input type="date" name="acct_dv_date" class="form-control" value="<?php echo htmlspecialchars($transaction['acct_dv_date'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label mb-1">DV Amount</label>
                                <input type="number" step="0.01" min="0" name="acct_dv_amount" class="form-control"
                                        value="<?php echo htmlspecialchars($transaction['acct_dv_amount'] ?? ''); ?>"
                                        placeholder="Enter DV amount (net)">
                            </div>
                        </div>
                    <?php elseif ($role === 'budget' && $roleDeptEdit === $ownerDeptEdit): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ORS Number</label>
                                <input type="text" name="budget_ors_number" class="form-control"
                                        inputmode="numeric" pattern="\d*" oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                                        value="<?php echo htmlspecialchars($transaction['budget_ors_number'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ORS Date</label>
                                <input type="date" name="budget_ors_date" class="form-control"
                                        value="<?php echo htmlspecialchars($transaction['budget_ors_date'] ?? ''); ?>">
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
                    <?php elseif ($role === 'cashier' && $roleDeptEdit === $ownerDeptEdit): ?>
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
                                    value="<?php
                                    $cashierAmountUi = trim((string)($transaction['cashier_landbank_ref'] ?? ''));
                                    if ($cashierAmountUi === '') {
                                        $cashierAmountUi = trim((string)($transaction['acct_dv_amount'] ?? ''));
                                        if ($cashierAmountUi === '') {
                                            $cashierAmountUi = trim((string)($transaction['amount'] ?? ''));
                                        }
                                    }
                                    echo htmlspecialchars($cashierAmountUi);
                                    ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Date</label>
                                <input type="date" name="cashier_payment_date" class="form-control"
                                        value="<?php echo htmlspecialchars($transaction['cashier_payment_date'] ?? ''); ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($role !== 'supplier' && $roleDeptEdit === $ownerDeptEdit): ?>
                        <div class="d-grid">
                            <button type="submit" name="save_updates" value="1" class="btn btn-primary" id="saveUpdatesBtn">Save Updates</button>
                        </div>
                    <?php endif; ?>
                    </fieldset>
                </form>

                <?php if ($role === 'cashier'): ?>
                    <hr class="my-4">
                    <h6 class="mb-2">Proceed to Landbank</h6>
                    <p class="small text-muted">
                        All requirements from Procurement, Supply, Accounting, and Budget are complete.
                    </p>
                    <a href="<?php echo htmlspecialchars(LANDBANK_URL); ?>" target="_blank"
                        class="btn btn-success w-100 mb-2">
                        Proceed to Landbank Site
                    </a>
                    <?php 
                    $notifyDefaultMsgUi = $lastNotifyMessage;
                    if (empty($notifyDefaultMsgUi)) {
                        $notifyDefaultMsgUi = 'Your PO ' . ($transaction['po_number'] ?? '') . ' is now marked as COMPLETED. Please check the portal for details.';
                    }
                    ?>
                    
                    <br><br>
                    <form method="post" class="mt-1">
                        <input type="hidden" name="notify_supplier" value="1">

                        <div class="mb-2">
                            <label class="form-label small mb-1">Title</label>
                            <input type="text" class="form-control" name="notify_title" value="<?php echo htmlspecialchars($lastNotifyTitle); ?>">
                        </div>

                        <div class="mb-2">
                            <label class="form-label small mb-1">Message</label>
                            <textarea class="form-control" name="notify_message" id="notifyMessageTextarea" rows="3"><?php echo htmlspecialchars($notifyDefaultMsgUi); ?></textarea>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small mb-1">CC</label>
                            <input type="text" class="form-control" name="notify_cc" value="<?php echo htmlspecialchars($currentUserEmail); ?>" placeholder="example1@email.com, example2@email.com">
                        </div>

                        <button type="submit" class="btn btn-outline-primary w-100">
                            Notify Supplier
                        </button>
                    </form>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3" style="flex: 1 1 420px; min-width: 420px;">
        <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
            <h6 class="mb-3">
                <i class="fas fa-stream me-2"></i>Flow Timeline
            </h6>
            <div class="timeline-scroll" style="padding-right: 8px;">
            <div class="timeline">
                <?php if (!empty($handoffHistory)): ?>
                    <div class="card border-primary mb-3 bg-light bg-opacity-10">
                        <div class="card-header bg-primary text-white py-1 px-2 small fw-bold">
                            <i class="fas fa-exchange-alt me-1"></i> Recent Handoffs
                        </div>
                        <ul class="list-group list-group-flush small">
                            <?php foreach (array_reverse($handoffHistory) as $h): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2">
                                    <div>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars(strtoupper($h['from_dept'])); ?></span>
                                        <i class="fas fa-long-arrow-alt-right mx-1 text-primary"></i>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars(strtoupper($h['to_dept'])); ?></span>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            <?php if ($h['forwarded_at']): ?>
                                                Fwd: <?php echo date('m/d H:i', strtotime($h['forwarded_at'])); ?>
                                            <?php endif; ?>
                                            <?php if ($h['received_at']): ?>
                                                <br>Rec: <?php echo date('m/d H:i', strtotime($h['received_at'])); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php
                // Handover delay display between stages (now based on next meaningful received handoff)
                $handoffGraceSeconds = (int)$handoffGraceSeconds;
                ?>

                <!-- Procurement -->
                <?php $procCompleted = (!empty($transaction['created_at']) || !empty($updatesByStage['procurement'])); ?>
                <div class="timeline-item <?php echo $procCompleted ? 'completed' : 'pending'; ?>">
                    <div class="timeline-marker">
                        <i class="fas fa-<?php echo $procCompleted ? 'check-circle' : 'circle'; ?>"></i>
                    </div>
                    <div class="timeline-content">
                        <?php
                        // Elapsed from transaction created_at to first forward from procurement
                        $elapsedProc = '';
                        if (!empty($updatesByStage['procurement'])) {
                            $createdTs = strtotime($transaction['created_at']);
                            $endTs = null;
                            foreach ($handoffHistory as $h) {
                                if ($h['from_dept'] === 'procurement' && !empty($h['forwarded_at'])) {
                                    $endTs = strtotime($h['forwarded_at']);
                                    break;
                                }
                            }
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
                                $procForwardTs = null;
                                foreach ($handoffHistory as $h) {
                                    if ($h['from_dept'] === 'procurement' && !empty($h['forwarded_at'])) {
                                        $procForwardTs = strtotime($h['forwarded_at']);
                                        break;
                                    }
                                }
                                $procForwardPrinted = false;
                                ?>
                                <?php
                                $procReceivedTs = null; // No "received" for initial procurement stage usually, but could be from a backward handoff
                                foreach ($handoffHistory as $h) {
                                    if ($h['to_dept'] === 'procurement' && !empty($h['received_at'])) {
                                        $procReceivedTs = strtotime($h['received_at']);
                                        break;
                                    }
                                }
                                $procReceivedPrinted = false;
                                $procForwardTs = null;
                                foreach ($handoffHistory as $h) {
                                    if ($h['from_dept'] === 'procurement' && !empty($h['forwarded_at'])) {
                                        $procForwardTs = strtotime($h['forwarded_at']);
                                        break;
                                    }
                                }
                                $procForwardPrinted = false;
                                ?>
                                <?php $countProc = count($updatesByStage['procurement']); ?>
                                <?php foreach ($updatesByStage['procurement'] as $idx => $u): ?>
                                    <?php
                                    $isLatest = ($idx === $countProc - 1);
                                    $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');

                                    $showReceivedHere = false;
                                    $showForwardHere = false;
                                    $updateTs = !empty($u['created_at']) ? strtotime((string)$u['created_at']) : false;
                                    $nextUpdateTs = false;
                                    if (($idx + 1) < $countProc) {
                                        $next = $updatesByStage['procurement'][$idx + 1] ?? null;
                                        if (!empty($next['created_at'])) {
                                            $nextUpdateTs = strtotime((string)$next['created_at']);
                                        }
                                    }
                                    
                                    if (!$procReceivedPrinted && $procReceivedTs !== null && $updateTs !== false && $updateTs >= (int)$procReceivedTs) {
                                        $showReceivedHere = true;
                                        $procReceivedPrinted = true;
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
                                                <?php if ($showReceivedHere && $procReceivedTs !== null): ?>
                                                    <div><i class="fas fa-sign-in-alt me-1"></i>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$procReceivedTs)); ?></div>
                                                <?php endif; ?>
                                                <?php if ($showForwardHere && $procForwardTs !== null): ?>
                                                    <div><i class="fas fa-sign-out-alt me-1"></i>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$procForwardTs)); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
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
                        $endTs = null;
                        // For flexible forwarding, we look for any handoff FROM supply
                        foreach ($handoffHistory as $h) {
                            if ($h['from_dept'] === 'supply' && !empty($h['forwarded_at'])) {
                                $endTs = strtotime($h['forwarded_at']);
                                break;
                            }
                        }
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
                                $supplyForwardTs = null;
                                foreach ($handoffHistory as $h) {
                                    if ($h['from_dept'] === 'supply' && !empty($h['forwarded_at'])) {
                                        $supplyForwardTs = strtotime($h['forwarded_at']);
                                        break;
                                    }
                                }
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
                                                    <div><i class="fas fa-sign-in-alt me-1"></i>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$supplyReceivedTs)); ?></div>
                                                <?php endif; ?>
                                                <?php if ($showForwardHere && $supplyForwardTs !== null): ?>
                                                    <div><i class="fas fa-sign-out-alt me-1"></i>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$supplyForwardTs)); ?></div>
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
                $supplyAcctForwardTs = get_handoff_timestamp($handoffHistory, 'supply', 'accounting', 'forwarded_at');
                $supplyAcctRecvTs = get_handoff_timestamp($handoffHistory, 'supply', 'accounting', 'received_at');
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
                <?php 
                $hasAcctRecv = false;
                foreach ($handoffHistory as $h) {
                    if ($h['to_dept'] === 'accounting' && !empty($h['received_at'])) {
                        $hasAcctRecv = true;
                        break;
                    }
                }
                $acctCompleted = (!empty($updatesByStage['accounting']) || $hasAcctRecv); 
                ?>
                <div class="timeline-item <?php echo $acctCompleted ? 'completed' : 'pending'; ?>">
                    <div class="timeline-marker">
                        <i class="fas fa-<?php echo $acctCompleted ? 'check-circle' : 'circle'; ?>"></i>
                    </div>
                    <div class="timeline-content">
                        <?php
                        // Elapsed from first received_at (handoff) to last forwarded_at (handoff) or latest Accounting update
                        $elapsedAcct = '';
                        $startTs = null;
                        foreach ($handoffHistory as $h) {
                            if ($h['to_dept'] === 'accounting' && !empty($h['received_at'])) {
                                $startTs = strtotime($h['received_at']);
                                break;
                            }
                        }
                        $endTs = null;
                        // Use the very last handoff from accounting as the end point
                        foreach (array_reverse($handoffHistory) as $h) {
                            if ($h['from_dept'] === 'accounting' && !empty($h['forwarded_at'])) {
                                $endTs = strtotime($h['forwarded_at']);
                                break;
                            }
                        }
                        if ($startTs !== null && $endTs !== null && $endTs >= $startTs) {
                            $elapsedAcct = format_elapsed_time(($endTs - $startTs) + (int)($handoffExtras['accounting'] ?? 0));
                        }
                        ?>
                        <h6 class="timeline-title d-flex justify-content-between align-items-center">
                            <span>Accounting</span>
                            <span class="small text-muted"><?php echo $elapsedAcct ? htmlspecialchars($elapsedAcct) : ''; ?></span>
                        </h6>

                        <?php if (!empty($updatesByStage['accounting'])): ?>
                            <div class="timeline-history mt-2 p-2 border rounded bg-white">
                                <div class="small text-muted mb-1">Update history</div>
                                <?php
                                $acctReceivedTs = null;
                                foreach ($handoffHistory as $h) {
                                    if ($h['to_dept'] === 'accounting' && !empty($h['received_at'])) {
                                        $acctReceivedTs = strtotime($h['received_at']);
                                        break;
                                    }
                                }
                                $acctReceivedPrinted = false;
                                $acctForwardTs = null;
                                foreach (array_reverse($handoffHistory) as $h) {
                                    if ($h['from_dept'] === 'accounting' && !empty($h['forwarded_at'])) {
                                        $acctForwardTs = strtotime($h['forwarded_at']);
                                        break;
                                    }
                                }
                                $acctForwardPrinted = false;
                                ?>
                                <?php $countAcct = count($updatesByStage['accounting']); ?>
                                <?php foreach ($updatesByStage['accounting'] as $idx => $u): ?>
                                    <?php
                                    $isLatest = ($idx === $countAcct - 1);
                                    $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');
                                    $showReceivedHere = false;
                                    $showForwardHere = false;
                                    $updateTs = !empty($u['created_at']) ? strtotime((string)$u['created_at']) : false;
                                    $nextUpdateTs = false;
                                    if (($idx + 1) < $countAcct) {
                                        $next = $updatesByStage['accounting'][$idx + 1] ?? null;
                                        if (!empty($next['created_at'])) {
                                            $nextUpdateTs = strtotime((string)$next['created_at']);
                                        }
                                    }
                                    if (!$acctReceivedPrinted && $acctReceivedTs !== null && $updateTs !== false && $updateTs >= (int)$acctReceivedTs) {
                                        $showReceivedHere = true;
                                        $acctReceivedPrinted = true;
                                    }
                                    if (!$acctForwardPrinted && $acctForwardTs !== null && $updateTs !== false) {
                                        $fwd = (int)$acctForwardTs;
                                        if ($updateTs <= $fwd && ($nextUpdateTs === false || $nextUpdateTs > $fwd)) {
                                            $showForwardHere = true;
                                            $acctForwardPrinted = true;
                                        } elseif ($idx === 0 && $updateTs > $fwd) {
                                            $showForwardHere = true;
                                            $acctForwardPrinted = true;
                                        } elseif ($isLatest && !$acctForwardPrinted) {
                                            $showForwardHere = true;
                                            $acctForwardPrinted = true;
                                        }
                                    }
                                    ?>
                                    <div class="<?php echo $rowClass; ?>">
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
                                                <?php if ($showReceivedHere && $acctReceivedTs !== null): ?>
                                                    <div>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$acctReceivedTs)); ?></div>
                                                <?php endif; ?>
                                                <?php if ($showForwardHere && $acctForwardTs !== null): ?>
                                                    <div>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$acctForwardTs)); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php
                $acctBudgetForwardTs = get_handoff_timestamp($handoffHistory, 'accounting', 'budget', 'forwarded_at');
                $acctBudgetRecvTs = get_handoff_timestamp($handoffHistory, 'accounting', 'budget', 'received_at');
                $acctBudgetOverdue = 0;
                if ($acctBudgetForwardTs !== null) {
                    $endTs = $acctBudgetRecvTs !== null ? $acctBudgetRecvTs : time();
                    $delaySecs = max(0, (int)$endTs - (int)$acctBudgetForwardTs);
                    $acctBudgetOverdue = max(0, $delaySecs - (int)$handoffGraceSeconds);
                }
                ?>
                <?php if ($acctBudgetOverdue > 0): ?>
                    <div class="handoff-between-due">
                        <div class="small text-danger fw-semibold">
                            <?php echo htmlspecialchars(format_elapsed_time((int)$acctBudgetOverdue)); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Budget Unit -->
                <?php 
                $hasBudgetRecv = false;
                foreach ($handoffHistory as $h) {
                    if ($h['to_dept'] === 'budget' && !empty($h['received_at'])) {
                        $hasBudgetRecv = true;
                        break;
                    }
                }
                $budgetCompleted = (!empty($updatesByStage['budget']) || $hasBudgetRecv); 
                ?>
                <div class="timeline-item <?php echo $budgetCompleted ? 'completed' : 'pending'; ?>">
                    <div class="timeline-marker">
                        <i class="fas fa-<?php echo $budgetCompleted ? 'check-circle' : 'circle'; ?>"></i>
                    </div>
                    <div class="timeline-content">
                        <?php
                        // Elapsed from received_at (handoff) to forwarded_at (handoff) or latest Budget update
                        $elapsedBudget = '';
                        $startTs = null;
                        foreach ($handoffHistory as $h) {
                            if ($h['to_dept'] === 'budget' && !empty($h['received_at'])) {
                                $startTs = strtotime($h['received_at']);
                                break;
                            }
                        }
                        $endTs = null;
                        foreach ($handoffHistory as $h) {
                            if ($h['from_dept'] === 'budget' && !empty($h['forwarded_at'])) {
                                $endTs = strtotime($h['forwarded_at']);
                                break;
                            }
                        }
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

                        <?php if (!empty($updatesByStage['budget'])): ?>
                            <div class="timeline-history mt-2 p-2 border rounded bg-white">
                                <div class="small text-muted mb-1">Update history</div>
                                <?php
                                $budgetReceivedTs = null;
                                foreach ($handoffHistory as $h) {
                                    if ($h['to_dept'] === 'budget' && !empty($h['received_at'])) {
                                        $budgetReceivedTs = strtotime($h['received_at']);
                                        break;
                                    }
                                }
                                $budgetReceivedPrinted = false;
                                $budgetForwardTs = null;
                                foreach ($handoffHistory as $h) {
                                    if ($h['from_dept'] === 'budget' && !empty($h['forwarded_at'])) {
                                        $budgetForwardTs = strtotime($h['forwarded_at']);
                                        break;
                                    }
                                }
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
                                                    <div><i class="fas fa-sign-in-alt me-1"></i>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$budgetReceivedTs)); ?></div>
                                                <?php endif; ?>
                                                <?php if ($showForwardHere && $budgetForwardTs !== null): ?>
                                                    <div><i class="fas fa-sign-out-alt me-1"></i>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$budgetForwardTs)); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php
                $budgetAcctForwardTs = get_handoff_timestamp($handoffHistory, 'budget', 'accounting', 'forwarded_at');
                $budgetAcctRecvTs = get_handoff_timestamp($handoffHistory, 'budget', 'accounting', 'received_at');
                $budgetAcctOverdue = 0;
                if ($budgetAcctForwardTs !== null) {
                    $endTs = $budgetAcctRecvTs !== null ? $budgetAcctRecvTs : time();
                    $delaySecs = max(0, (int)$endTs - (int)$budgetAcctForwardTs);
                    $budgetAcctOverdue = max(0, $delaySecs - (int)$handoffGraceSeconds);
                }
                ?>
                <?php if ($budgetAcctOverdue > 0): ?>
                    <div class="handoff-between-due">
                        <div class="small text-danger fw-semibold">
                            <?php echo htmlspecialchars(format_elapsed_time((int)$budgetAcctOverdue)); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                $acctCashierForwardTs = get_handoff_timestamp($handoffHistory, 'accounting', 'cashier', 'forwarded_at');
                $acctCashierRecvTs = get_handoff_timestamp($handoffHistory, 'accounting', 'cashier', 'received_at');
                $acctCashierOverdue = 0;
                if ($acctCashierForwardTs !== null) {
                    $endTs = $acctCashierRecvTs !== null ? $acctCashierRecvTs : time();
                    $delaySecs = max(0, (int)$endTs - (int)$acctCashierForwardTs);
                    $acctCashierOverdue = max(0, $delaySecs - (int)$handoffGraceSeconds);
                }
                ?>
                <?php if ($acctCashierOverdue > 0): ?>
                    <div class="handoff-between-due">
                        <div class="small text-danger fw-semibold">
                            <?php echo htmlspecialchars(format_elapsed_time((int)$acctCashierOverdue)); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Cashier Unit -->
                <?php 
                $hasCashierRecv = false;
                foreach ($handoffHistory as $h) {
                    if ($h['to_dept'] === 'cashier' && !empty($h['received_at'])) {
                        $hasCashierRecv = true;
                        break;
                    }
                }
                $cashierCompleted = (!empty($updatesByStage['cashier']) || $hasCashierRecv); 
                ?>
                <div class="timeline-item <?php echo $cashierCompleted ? 'completed' : 'pending'; ?>">
                    <div class="timeline-marker">
                        <i class="fas fa-<?php echo $cashierCompleted ? 'check-circle' : 'circle'; ?>"></i>
                    </div>
                    <div class="timeline-content">
                        <?php
                        // Elapsed from received_at (handoff) to latest Cashier update
                        $elapsedCashier = '';
                        $startTs = null;
                        foreach ($handoffHistory as $h) {
                            if ($h['to_dept'] === 'cashier' && !empty($h['received_at'])) {
                                $startTs = strtotime($h['received_at']);
                                break;
                            }
                        }
                        $endTs = get_last_stage_timestamp($updatesByStage, 'cashier');
                        if ($startTs !== null && $endTs !== null && $endTs >= $startTs) {
                            $elapsedCashier = format_elapsed_time(($endTs - $startTs) + (int)($handoffExtras['cashier'] ?? 0));
                        }
                        ?>
                        <h6 class="timeline-title d-flex justify-content-between align-items-center">
                            <span>Cashier Unit</span>
                            <span class="small text-muted"><?php echo $elapsedCashier ? htmlspecialchars($elapsedCashier) : ''; ?></span>
                        </h6>

                        <?php if (!empty($updatesByStage['cashier'])): ?>
                            <div class="timeline-history mt-2 p-2 border rounded bg-white">
                                <div class="small text-muted mb-1">Update history</div>
                                <?php
                                $cashierReceivedTs = null;
                                foreach ($handoffHistory as $h) {
                                    if ($h['to_dept'] === 'cashier' && !empty($h['received_at'])) {
                                        $cashierReceivedTs = strtotime($h['received_at']);
                                        break;
                                    }
                                }
                                $cashierReceivedPrinted = false;
                                ?>
                                <?php
                                $cashierReceivedTs = null;
                                foreach ($handoffHistory as $h) {
                                    if ($h['to_dept'] === 'cashier' && !empty($h['received_at'])) {
                                        $cashierReceivedTs = strtotime($h['received_at']);
                                        break;
                                    }
                                }
                                $cashierReceivedPrinted = false;
                                $cashierForwardTs = null;
                                foreach ($handoffHistory as $h) {
                                    if ($h['from_dept'] === 'cashier' && !empty($h['forwarded_at'])) {
                                        $cashierForwardTs = strtotime($h['forwarded_at']);
                                        break;
                                    }
                                }
                                $cashierForwardPrinted = false;
                                ?>
                                <?php $countCashier = count($updatesByStage['cashier']); ?>
                                <?php foreach ($updatesByStage['cashier'] as $idx => $u): ?>
                                    <?php
                                    $isLatest = ($idx === $countCashier - 1);
                                    $rowClass = 'timeline-history-item py-1 px-2 small ' . ($isLatest ? 'border border-primary bg-primary bg-opacity-10 rounded' : 'border-top');
                                    $showReceivedHere = false;
                                    $showForwardHere = false;
                                    $updateTs = !empty($u['created_at']) ? strtotime((string)$u['created_at']) : false;
                                    $nextUpdateTs = false;
                                    if (($idx + 1) < $countCashier) {
                                        $next = $updatesByStage['cashier'][$idx + 1] ?? null;
                                        if (!empty($next['created_at'])) {
                                            $nextUpdateTs = strtotime((string)$next['created_at']);
                                        }
                                    }
                                    if (!$cashierReceivedPrinted && $cashierReceivedTs !== null && $updateTs !== false && $updateTs >= (int)$cashierReceivedTs) {
                                        $showReceivedHere = true;
                                        $cashierReceivedPrinted = true;
                                    }
                                    if (!$cashierForwardPrinted && $cashierForwardTs !== null && $updateTs !== false) {
                                        $fwd = (int)$cashierForwardTs;
                                        if ($updateTs <= $fwd && ($nextUpdateTs === false || $nextUpdateTs > $fwd)) {
                                            $showForwardHere = true;
                                            $cashierForwardPrinted = true;
                                        } elseif ($idx === 0 && $updateTs > $fwd) {
                                            $showForwardHere = true;
                                            $cashierForwardPrinted = true;
                                        } elseif ($isLatest && !$cashierForwardPrinted) {
                                            $showForwardHere = true;
                                            $cashierForwardPrinted = true;
                                        }
                                    }
                                    ?>
                                    <div class="<?php echo $rowClass; ?>">
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
                                                    <div><i class="fas fa-sign-in-alt me-1"></i>Received: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$cashierReceivedTs)); ?></div>
                                                <?php endif; ?>
                                                <?php if ($showForwardHere && $cashierForwardTs !== null): ?>
                                                    <div><i class="fas fa-sign-out-alt me-1"></i>Forwarded: <?php echo htmlspecialchars(date('m/d/Y H:i:s', (int)$cashierForwardTs)); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
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

        // Prevent refresh if forward dropdown is open
        const forwardDropdown = document.getElementById('forwardDropdownBtn');
        if (forwardDropdown && forwardDropdown.classList.contains('show')) {
            return;
        }

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

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert.auto-dismiss').forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

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
