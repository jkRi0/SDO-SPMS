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
    // Supply sees items only once Procurement has actually saved an update
    // (proc_date is set when Procurement submits its form)
    $where[] = 'proc_date IS NOT NULL';
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
?>

<div class="card table-wrapper">
    <div class="card-body">
        <div id="transactionsToolbar" class="d-flex justify-content-between align-items-center gap-2 mb-2" style="flex-wrap: nowrap; overflow-x: auto; white-space: nowrap;">
            <div class="d-flex align-items-center gap-2" style="flex-wrap: nowrap;">
                <div id="transactionsDeptFilterWrap" class="d-none">
                    <label class="mb-0 small text-muted" for="transactionsDeptFilter">Department</label>
                    <select id="transactionsDeptFilter" class="form-select form-select-sm ms-2" style="width: 180px; display: inline-block;">
                        <option value="">All</option>
                        <option value="procurement">Procurement</option>
                        <option value="supply">Supply</option>
                        <option value="accounting">Accounting</option>
                        <option value="budget">Budget</option>
                        <option value="cashier">Cashier</option>
                    </select>
                </div>

                <?php if ($role !== 'admin'): ?>
                    <div id="transactionsStageFilterWrap" class="d-none">
                        <label class="mb-0 small text-muted" for="transactionsStageFilter">Stage</label>
                        <select id="transactionsStageFilter" class="form-select form-select-sm ms-2" style="width: 140px; display: inline-block;">
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                        </select>
                    </div>
                <?php endif; ?>
            </div>

            <div id="transactionsSearchSlot" class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-danger btn-sm" id="btnTransactionsPdf">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button type="button" class="btn btn-outline-success btn-sm" id="btnTransactionsExcel">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
            </div>
        </div>
        <div class="table-responsive">
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
                <tbody id="transactionsBody">
            <?php if (!$transactions): ?>
                <tr>
                    <td class="text-center text-muted">No transactions found.</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            <?php else: ?>
                <?php foreach ($transactions as $t): ?>
                    <?php
                    // Derive a simple current status text from the flow and which department owns it
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
                        $cashierStatusUpper = strtoupper(trim((string)($t['cashier_status'] ?? '')));
                        if ($cashierStatusUpper === 'COMPLETED') {
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
                        $cashierStatusUpper = strtoupper(trim((string)($t['cashier_status'] ?? '')));
                        if ($cashierStatusUpper === 'COMPLETED') {
                            $stageLabel = 'Approved';
                            $stageClass = 'bg-success';
                        } elseif ($has($t['cashier_status'])) {
                            $stageLabel = 'Pending';
                            $stageClass = 'bg-warning text-dark';
                        }
                    }

                    // Map status to badge styles based on the status text
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
                    ?>
                    <tr data-next-dept="<?php echo htmlspecialchars($nextDept); ?>" data-status-dept="<?php echo htmlspecialchars(strtolower($statusDept)); ?>" data-stage="<?php echo htmlspecialchars($filterStage); ?>">
                        <td><?php echo htmlspecialchars($t['po_number']); ?></td>
                        <td><?php echo htmlspecialchars($t['supplier_name']); ?></td>
                        <td><?php echo htmlspecialchars($t['program_title']); ?></td>
                        <td>₱ <?php echo number_format($t['amount'], 2); ?></td>
                        <td>
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($statusLabel); ?></span>
                                <?php if ($stageLabel !== ''): ?>
                                    <span class="badge <?php echo htmlspecialchars($stageClass); ?>"><?php echo htmlspecialchars($stageLabel); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
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
</div>

<?php if ($role === 'procurement'): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete-tx').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-tx-id');
            if (!id) return;

            const row = this.closest('tr');

            if (!confirm('Delete this transaction? This cannot be undone.')) {
                return;
            }

            fetch('api/api_delete_transaction.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ id: id })
            })
                .then(resp => resp.json())
                .then(data => {
                    if (data.success) {
                        if (row) {
                            row.remove();
                        }

                        const tbody = document.getElementById('transactionsBody');
                        if (tbody && tbody.querySelectorAll('tr').length === 0) {
                            const tr = document.createElement('tr');
                            tr.innerHTML = '<td class="text-center text-muted">No transactions found.</td><td></td><td></td><td></td><td></td><td></td><td></td>';
                            tbody.appendChild(tr);
                        }
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

