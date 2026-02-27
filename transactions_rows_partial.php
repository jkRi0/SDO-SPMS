<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

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

    if (!empty($t['cashier_status'])) {
        $status = $t['cashier_status'];
        $statusDept = 'Cashier';
    } elseif (!empty($t['acct_post_status'])) {
        $status = $t['acct_post_status'];
        $statusDept = 'Accounting';
    } elseif (!empty($t['budget_status'])) {
        $status = $t['budget_status'];
        $statusDept = 'Budget';
    } elseif (!empty($t['acct_pre_status'])) {
        $status = $t['acct_pre_status'];
        $statusDept = 'Accounting';
    } elseif (!empty($t['supply_status'])) {
        $status = $t['supply_status'];
        $statusDept = 'Supply';
    } elseif (!empty($t['proc_status'])) {
        $status = $t['proc_status'];
        $statusDept = 'Procurement';
    }

    $statusLabel = $statusDept ? ($statusDept . ' - ' . $status) : $status;

    $statusUpper = strtoupper(trim($status));
    $statusClass = 'badge-info';
    if (strpos($statusUpper, 'PAID') !== false || strpos($statusUpper, 'APPROVE') !== false || strpos($statusUpper, 'COMPLETED') !== false) {
        $statusClass = 'badge-success';
    } elseif (strpos($statusUpper, 'PEND') !== false || strpos($statusUpper, 'WAIT') !== false) {
        $statusClass = 'badge-warning';
    } elseif (strpos($statusUpper, 'REJECT') !== false || strpos($statusUpper, 'CANCEL') !== false || strpos($statusUpper, 'DENIED') !== false) {
        $statusClass = 'badge-danger';
    }

    echo '<tr>';
    echo '<td>' . htmlspecialchars($t['po_number']) . '</td>';
    echo '<td>' . htmlspecialchars($t['supplier_name']) . '</td>';
    echo '<td>' . htmlspecialchars($t['program_title']) . '</td>';
    echo '<td>₱ ' . number_format($t['amount'], 2) . '</td>';
    echo '<td><span class="badge ' . htmlspecialchars($statusClass) . '">' . htmlspecialchars($statusLabel) . '</span></td>';
    echo '<td>' . htmlspecialchars($t['created_at']) . '</td>';
    echo '<td class="text-end">';
    echo '<a class="btn btn-outline-primary btn-sm" aria-label="View or update transaction" title="View / Update" href="transaction_view.php?id=' . (int)$t['id'] . '"><i class="fas fa-eye"></i></a>';
    if ($role === 'procurement') {
        echo ' <button type="button" class="btn btn-outline-danger btn-sm ms-1 btn-delete-tx" title="Delete transaction" aria-label="Delete transaction" data-tx-id="' . (int)$t['id'] . '"><i class="fas fa-trash"></i></button>';
    }
    echo '</td>';
    echo '</tr>';
}
