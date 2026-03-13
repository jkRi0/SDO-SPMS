<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';

require_login();

$db   = get_db();
$role = $_SESSION['role'] ?? '';
$supplierId = $_SESSION['supplier_id'] ?? null;

$where  = [];
$params = [];

if ($role === 'supplier' && $supplierId) {
    $where[]  = 't.supplier_id = ?';
    $params[] = $supplierId;
}

// Same visibility rules as dashboard table
if ($role === 'supply') {
    $where[]  = 'proc_date IS NOT NULL';
}
if ($role === 'accounting') {
    $where[]  = 'supply_status IS NOT NULL';
}
if ($role === 'budget') {
    $where[]  = 'acct_pre_status IS NOT NULL';
}
if ($role === 'cashier') {
    $where[]  = 'acct_post_status IS NOT NULL';
}

$sql = 'SELECT t.*, s.name AS supplier_name
        FROM transactions t
        JOIN suppliers s ON t.supplier_id = s.id';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY t.created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

if (!$transactions) {
    echo '<tr>';
    echo '<td class="text-center text-muted">No transactions found.</td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '</tr>';
    exit;
}

foreach ($transactions as $t) {
    $status = 'NEW';
    $statusDept = '';
    $nextDept = '';
    $globalStage = 'active';

    $has = function ($v): bool {
        return trim((string)($v ?? '')) !== '';
    };

    if ($has($t['cashier_status'])) {
        $status = $t['cashier_status'];
        $statusDept = 'Cashier';
        $nextDept = '';
    } elseif ($has($t['acct_post_status'])) {
        $status = $t['acct_post_status'];
        $statusDept = 'Accounting';
        $nextDept = 'cashier';
    } elseif ($has($t['budget_status'])) {
        $status = $t['budget_status'];
        $statusDept = 'Budget';
        $nextDept = 'accounting';
    } elseif ($has($t['acct_pre_status'])) {
        $status = $t['acct_pre_status'];
        $statusDept = 'Accounting';
        $nextDept = 'budget';
    } elseif ($has($t['supply_status'])) {
        $status = $t['supply_status'];
        $statusDept = 'Supply';
        $nextDept = 'accounting';
    } elseif ($has($t['proc_status'])) {
        $status = $t['proc_status'];
        $statusDept = 'Procurement';
        $nextDept = 'supply';
    } elseif (!empty($t['proc_date'])) {
        $nextDept = 'supply';
    } else {
        $nextDept = 'procurement';
    }

    $statusLabel = $statusDept ? ($statusDept . ' - ' . $status) : $status;

    if ($has($t['cashier_status'])) {
        $globalStage = 'approved';
    } elseif ($has($t['supply_status']) || $has($t['acct_pre_status']) || $has($t['budget_status']) || $has($t['acct_post_status'])) {
        $globalStage = 'pending';
    } else {
        $globalStage = 'active';
    }

    $stageLabel = '';
    $stageClass = '';
    if ($role === 'procurement') {
        if ($has($t['cashier_status'])) {
            $stageLabel = 'Approved';
            $stageClass = 'bg-success';
        } elseif ($has($t['supply_status']) || $has($t['acct_pre_status']) || $has($t['budget_status']) || $has($t['acct_post_status'])) {
            $stageLabel = 'Pending';
            $stageClass = 'bg-warning text-dark';
        } elseif ($has($t['proc_status']) && !$has($t['supply_status'])) {
            $stageLabel = 'Active';
            $stageClass = 'bg-primary';
        }
    } elseif ($role === 'supply') {
        if ($has($t['cashier_status'])) {
            $stageLabel = 'Approved';
            $stageClass = 'bg-success';
        } elseif ($has($t['supply_status']) && !$has($t['cashier_status'])) {
            $stageLabel = 'Pending';
            $stageClass = 'bg-warning text-dark';
        } elseif ($has($t['proc_date']) && !$has($t['supply_status'])) {
            $stageLabel = 'Active';
            $stageClass = 'bg-primary';
        }
    } elseif ($role === 'accounting') {
        if ($has($t['cashier_status'])) {
            $stageLabel = 'Approved';
            $stageClass = 'bg-success';
        } elseif (($has($t['acct_pre_status']) && !$has($t['budget_status'])) || ($has($t['acct_post_status']) && !$has($t['cashier_status']))) {
            $stageLabel = 'Pending';
            $stageClass = 'bg-warning text-dark';
        } elseif (($has($t['supply_status']) && !$has($t['acct_pre_status'])) || ($has($t['budget_status']) && !$has($t['acct_post_status']))) {
            $stageLabel = 'Active';
            $stageClass = 'bg-primary';
        }
    } elseif ($role === 'budget') {
        if ($has($t['cashier_status'])) {
            $stageLabel = 'Approved';
            $stageClass = 'bg-success';
        } elseif ($has($t['budget_status']) && !$has($t['cashier_status'])) {
            $stageLabel = 'Pending';
            $stageClass = 'bg-warning text-dark';
        } elseif ($has($t['acct_pre_status']) && !$has($t['budget_status'])) {
            $stageLabel = 'Active';
            $stageClass = 'bg-primary';
        }
    } elseif ($role === 'cashier') {
        $cashierStatusUpper2 = strtoupper(trim((string)($t['cashier_status'] ?? '')));
        if ($cashierStatusUpper2 === 'COMPLETED') {
            $stageLabel = 'Approved';
            $stageClass = 'bg-success';
        } elseif ($has($t['cashier_status'])) {
            $stageLabel = 'Pending';
            $stageClass = 'bg-warning text-dark';
        } elseif ($has($t['acct_post_status']) && !$has($t['cashier_status'])) {
            $stageLabel = 'Active';
            $stageClass = 'bg-primary';
        }
    } elseif ($role === 'supplier') {
        $cashierStatusUpper2 = strtoupper(trim((string)($t['cashier_status'] ?? '')));
        if ($cashierStatusUpper2 === 'COMPLETED') {
            $stageLabel = 'Approved';
            $stageClass = 'bg-success';
        } elseif ($has($t['cashier_status'])) {
            $stageLabel = 'Pending';
            $stageClass = 'bg-warning text-dark';
        }
    }

    $statusUpper = strtoupper(trim($status));
    $statusClass = 'badge-info';
    if (strpos($statusUpper, 'PAID') !== false || strpos($statusUpper, 'APPROVE') !== false || strpos($statusUpper, 'COMPLETED') !== false) {
        $statusClass = 'badge-success';
    } elseif (strpos($statusUpper, 'PEND') !== false || strpos($statusUpper, 'WAIT') !== false) {
        $statusClass = 'badge-warning';
    } elseif (strpos($statusUpper, 'REJECT') !== false || strpos($statusUpper, 'CANCEL') !== false || strpos($statusUpper, 'DENIED') !== false) {
        $statusClass = 'badge-danger';
    }

    $filterStage = $stageLabel !== '' ? strtolower($stageLabel) : $globalStage;

    echo '<tr data-next-dept="' . htmlspecialchars($nextDept) . '" data-status-dept="' . htmlspecialchars(strtolower($statusDept)) . '" data-stage="' . htmlspecialchars($filterStage) . '">';
    echo '<td>' . htmlspecialchars($t['po_number']) . '</td>';
    echo '<td>' . htmlspecialchars($t['supplier_name']) . '</td>';
    echo '<td>' . htmlspecialchars($t['program_title']) . '</td>';
    echo '<td>₱ ' . number_format($t['amount'], 2) . '</td>';
    echo '<td>';
    echo '<div class="d-flex justify-content-between align-items-center gap-2">';
    echo '<span class="badge ' . htmlspecialchars($statusClass) . '">' . htmlspecialchars($statusLabel) . '</span>';
    if ($stageLabel !== '') {
        echo '<span class="badge ' . htmlspecialchars($stageClass) . '">' . htmlspecialchars($stageLabel) . '</span>';
    }
    echo '</div>';
    echo '</td>';
    echo '<td>' . htmlspecialchars($t['created_at']) . '</td>';
    echo '<td class="text-end">';
    echo '<a class="btn btn-outline-primary btn-sm" aria-label="View or update transaction" title="View / Update" href="transaction_view.php?id=' . (int)$t['id'] . '"><i class="fas fa-eye"></i></a>';
    if ($role === 'procurement') {
        echo ' <button type="button" class="btn btn-outline-danger btn-sm ms-1 btn-delete-tx" title="Delete transaction" aria-label="Delete transaction" data-tx-id="' . (int)$t['id'] . '"><i class="fas fa-trash"></i></button>';
    }
    echo '</td>';
    echo '</tr>';
}
