<?php

$role = $_SESSION['role'] ?? '';
$supplierId = $_SESSION['supplier_id'] ?? null;

$where = [];
$params = [];

if ($role === 'supplier' && $supplierId) {
    $where[] = 't.supplier_id = ?';
    $params[] = $supplierId;
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
} catch (Exception $e) {
}

if (in_array($role, ['supply', 'accounting', 'budget', 'cashier'], true)) {
    $where[] = 'EXISTS (
        SELECT 1
        FROM transaction_handoffs h
        WHERE h.transaction_id = t.id AND h.to_dept = ?
        LIMIT 1
    )';
    $params[] = $role;
}

$sql = 'SELECT t.*, s.name AS supplier_name
        , COALESCE(lh.to_dept, "procurement") AS current_dept
        FROM transactions t
        JOIN suppliers s ON t.supplier_id = s.id
        LEFT JOIN (
            SELECT h1.transaction_id, h1.to_dept
            FROM transaction_handoffs h1
            JOIN (
                SELECT transaction_id, MAX(forwarded_at) AS max_forwarded_at
                FROM transaction_handoffs
                GROUP BY transaction_id
            ) hm ON hm.transaction_id = h1.transaction_id AND hm.max_forwarded_at = h1.forwarded_at
        ) lh ON lh.transaction_id = t.id';
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
    return;
}

$has = function ($v): bool {
    return trim((string)($v ?? '')) !== '';
};

foreach ($transactions as $t) {
    // Find first non-empty department status
    $status = 'NEW';
    $statusDept = '';
    
    $deptOrder = ['cashier', 'accounting', 'budget', 'supply', 'procurement'];
    $statusFields = [
        'cashier' => $t['cashier_status'],
        'accounting' => $t['acct_status'], 
        'budget' => $t['budget_status'],
        'supply' => $t['supply_status'],
        'procurement' => $t['proc_status']
    ];
    
    foreach ($deptOrder as $dept) {
        if ($has($statusFields[$dept])) {
            $status = $statusFields[$dept];
            $statusDept = ucfirst($dept);
            break;
        }
    }
    
    // Status badge styling
    $statusUpper = strtoupper(trim((string)$status));
    $statusClass = 'badge-info';

    $currentDept = strtolower(trim((string)($t['current_dept'] ?? 'procurement')));

    // Green: completed by cashier (strict)
    $cashierUpper = strtoupper(trim((string)($t['cashier_status'] ?? '')));
    if ($cashierUpper === 'COMPLETED') {
        $statusClass = 'badge-success';
    }
    // Yellow: transaction currently holds on viewer's department
    elseif ($role !== '' && $currentDept === strtolower($role)) {
        $statusClass = 'badge-warning';
    }
    // Blue: viewer is involved but transaction is currently on another dept

    $holderStatus = $statusFields[$currentDept] ?? null;
    if (!$has($holderStatus)) {
        $statusLabel = ucwords($currentDept);
    } else {
        $statusLabel = $statusDept ? ($statusDept . ' - ' . $status) : $status;
    }

    echo '<tr data-status-dept="' . htmlspecialchars(strtolower($statusDept)) . '">';
    echo '<td>' . htmlspecialchars($t['po_number']) . '</td>';
    echo '<td>' . htmlspecialchars($t['supplier_name']) . '</td>';
    echo '<td>' . htmlspecialchars($t['program_title']) . '</td>';
    echo '<td>₱ ' . number_format((float)$t['amount'], 2) . '</td>';
    echo '<td>';
    echo '<span class="badge ' . htmlspecialchars($statusClass) . '">' . htmlspecialchars($statusLabel) . '</span>';
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
