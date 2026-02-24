<?php
// Shared transaction table for different dashboards
// Expects $db and $_SESSION context

$role = $_SESSION['role'] ?? '';
$supplierId = $_SESSION['supplier_id'] ?? null;

$where = [];
$params = [];

if ($role === 'supplier' && $supplierId) {
    $where[] = 't.supplier_id = ?';
    $params[] = $supplierId;
}

// Example: show only items relevant to each unit (can be refined later)
if ($role === 'supply') {
    // Supply sees items once procurement has set a status
    $where[] = 'proc_status IS NOT NULL';
}
if ($role === 'accounting') {
    // Accounting should only see items after Supply has reviewed them
    $where[] = 'supply_status IS NOT NULL';
}
if ($role === 'budget') {
    $where[] = 'acct_pre_status IS NOT NULL';
}
if ($role === 'cashier') {
    $where[] = 'acct_post_status IS NOT NULL';
}

$sql = 'SELECT 
            t.*, 
            s.name AS supplier_name,
            GREATEST(
                t.created_at,
                IFNULL(t.proc_date, "0000-00-00"),
                IFNULL(t.supply_date, "0000-00-00"),
                IFNULL(t.acct_pre_date, "0000-00-00"),
                IFNULL(t.budget_dv_date, "0000-00-00"),
                IFNULL(t.acct_post_date, "0000-00-00"),
                IFNULL(t.cashier_payment_date, "0000-00-00")
            ) AS last_activity
        FROM transactions t
        JOIN suppliers s ON t.supplier_id = s.id';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY last_activity DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll();
?>

<div class="card table-wrapper">
    <div class="card-body table-responsive">
        <table id="transactionsTable" class="table table-sm table-hover table-striped align-middle datatable table-compact">
            <thead class="table-light">
            <tr>
                <th>PO #</th>
                <th>Supplier</th>
                <th>Program Title</th>
                <th>Amount</th>
                <th>Current Status</th>
                <th>Created</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$transactions): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">No transactions found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($transactions as $t): ?>
                    <?php
                    // Derive a simple current status text from the flow
                    $status = $t['cashier_status']
                        ?: $t['acct_post_status']
                        ?: $t['budget_status']
                        ?: $t['acct_pre_status']
                        ?: $t['supply_status']
                        ?: $t['proc_status']
                        ?: 'NEW';
                    // Map status to badge styles
                    $statusUpper = strtoupper(trim($status));
                    $statusClass = 'badge-info';
                    if (strpos($statusUpper, 'PAID') !== false || strpos($statusUpper, 'APPROVE') !== false || strpos($statusUpper, 'COMPLETED') !== false) {
                        $statusClass = 'badge-success';
                    } elseif (strpos($statusUpper, 'PEND') !== false || strpos($statusUpper, 'WAIT') !== false) {
                        $statusClass = 'badge-warning';
                    } elseif (strpos($statusUpper, 'REJECT') !== false || strpos($statusUpper, 'CANCEL') !== false || strpos($statusUpper, 'DENIED') !== false) {
                        $statusClass = 'badge-danger';
                    }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($t['po_number']); ?></td>
                        <td><?php echo htmlspecialchars($t['supplier_name']); ?></td>
                        <td><?php echo htmlspecialchars($t['program_title']); ?></td>
                        <td>₱ <?php echo number_format($t['amount'], 2); ?></td>
                        <td><span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                        <td><?php echo htmlspecialchars($t['created_at']); ?></td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary btn-sm" aria-label="View or update transaction" title="View / Update"
                               href="transaction_view.php?id=<?php echo (int)$t['id']; ?>">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ($role === 'procurement'): ?>
                                <button type="button"
                                        class="btn btn-outline-danger btn-sm ms-1 btn-delete-tx"
                                        title="Delete transaction"
                                        aria-label="Delete transaction"
                                        data-tx-id="<?php echo (int)$t['id']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
</div>

<?php if ($role === 'procurement'): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete-tx').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-tx-id');
            if (!id) return;

            if (!confirm('Delete this transaction? This action cannot be undone.')) {
                return;
            }

            fetch('api_delete_transaction.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ id: id })
            })
                .then(resp => resp.json())
                .then(data => {
                    if (data.success) {
                        // Simple reload to refresh table
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to delete transaction.');
                    }
                })
                .catch(() => {
                    alert('Failed to delete transaction.');
                });
        });
    });
});
</script>
<?php endif; ?>

