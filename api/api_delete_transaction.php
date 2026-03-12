<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../audit.php';

header('Content-Type: application/json');

require_login();
if (($_SESSION['role'] ?? '') !== 'procurement') {
    http_response_code(403);
}

$db = get_db();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid transaction ID.',
    ]);
    exit;
}

try {
    $db->beginTransaction();

    $stmtTx = $db->prepare('SELECT po_number, supplier_id FROM transactions WHERE id = ? LIMIT 1');
    $stmtTx->execute([$id]);
    $txRow = $stmtTx->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare('DELETE FROM transaction_updates WHERE transaction_id = ?');
    $stmt->execute([$id]);

    $stmt = $db->prepare('DELETE FROM transactions WHERE id = ?');
    $stmt->execute([$id]);

    $db->commit();

    try {
        create_log($db, $_SESSION['user_id'] ?? null, 'transaction_delete', 'transaction', (int)$id, json_encode([
            'transaction_id' => (int)$id,
            'po_number' => (string)($txRow['po_number'] ?? ''),
            'supplier_id' => (int)($txRow['supplier_id'] ?? 0),
        ]));
    } catch (Exception $e) {
    }

    echo json_encode([
        'success' => true,
        'message' => 'Transaction deleted successfully.',
    ]);
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete transaction.',
    ]);
}
