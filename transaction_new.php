<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_role(['procurement']);

$db = get_db();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = trim($_POST['supplier_name'] ?? '');
    $program_title = trim($_POST['program_title'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $proc_status = trim($_POST['proc_status'] ?? '');
    $proc_remarks = trim($_POST['proc_remarks'] ?? '');

    if ($supplier_name === '' || $program_title === '' || $amount === '') {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $db->beginTransaction();

            // Auto-generate PO number: PO-YYYY-MM-DD-XXXX
            $date = date('Y-m-d');
            $randomNum = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $po_number = 'PO-' . $date . '-' . $randomNum;

            // Find or create supplier
            $stmt = $db->prepare('SELECT id FROM suppliers WHERE name = ?');
            $stmt->execute([$supplier_name]);
            $supplier = $stmt->fetch();

            if ($supplier) {
                $supplier_id = $supplier['id'];
            } else {
                $stmt = $db->prepare('INSERT INTO suppliers (name) VALUES (?)');
                $stmt->execute([$supplier_name]);
                $supplier_id = $db->lastInsertId();
            }

            // Insert transaction
            $stmt = $db->prepare('INSERT INTO transactions 
                (supplier_id, po_number, program_title, amount, proc_status, proc_remarks, proc_date)
                VALUES (?,?,?,?,?,?,CURDATE())');
            $stmt->execute([
                $supplier_id,
                $po_number,
                $program_title,
                $amount,
                $proc_status ?: 'FOR SUPPLY REVIEW',
                $proc_remarks
            ]);

            $db->commit();
            $success = 'Transaction created successfully. PO Number: ' . $po_number;
        } catch (Exception $e) {
            $db->rollBack();
            $error = 'Error creating transaction.';
        }
    }
}

include __DIR__ . '/header.php';
?>

<div class="page-header mb-4">
    <h2 class="page-title">New Transaction (Procurement)</h2>
    <p class="page-subtitle">Fill in the details below to create a new purchase order</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="table-wrapper">
            <div style="padding: 2rem;">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                <form method="post" novalidate>
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-building form-icon"></i>Supplier Name</label>
                        <input type="text" name="supplier_name" class="form-control" required
                               placeholder="Enter supplier name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-project-diagram form-icon"></i>Program Title</label>
                        <input type="text" name="program_title" class="form-control" required
                               placeholder="Enter program title">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-dollar-sign form-icon"></i>Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required
                                   placeholder="Enter amount">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-list form-icon"></i>Procurement Status</label>
                            <input type="text" name="proc_status" class="form-control"
                                   placeholder="e.g., FOR SUPPLY REVIEW">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-comment form-icon"></i>Remarks</label>
                        <input type="text" name="proc_remarks" class="form-control"
                               placeholder='e.g., "NO SIGNATURE", "CHECKING"'>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>

