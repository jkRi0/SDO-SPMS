<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

header('Content-Type: application/json');

require_role(['procurement']);

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

    $stmt = $db->prepare('DELETE FROM transaction_updates WHERE transaction_id = ?');
    $stmt->execute([$id]);

    $stmt = $db->prepare('DELETE FROM transactions WHERE id = ?');
    $stmt->execute([$id]);

    $db->commit();

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
