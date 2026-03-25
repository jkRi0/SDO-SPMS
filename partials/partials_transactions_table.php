<?php
// Shared transaction table for different dashboards
// Expects $db and $_SESSION context

$role = $_SESSION['role'] ?? '';
$supplierId = $_SESSION['supplier_id'] ?? null;
$proponentId = $_SESSION['proponent_id'] ?? null;
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
            <?php include __DIR__ . '/transactions_rows_renderer.php'; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>

<?php if ($role === 'procurement'): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.__STMS_TX_DELETE_BOUND) {
        return;
    }
    window.__STMS_TX_DELETE_BOUND = true;

    document.addEventListener('click', function (e) {
        var btn = e.target && e.target.closest ? e.target.closest('.btn-delete-tx') : null;
        if (!btn) {
            return;
        }

        const id = btn.getAttribute('data-tx-id');
        if (!id) return;

        const row = btn.closest('tr');

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
</script>
<?php endif; ?>

