<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';

header('Content-Type: application/json');

require_role(['procurement']);

$db = get_db();

try {
    $supplier_name = trim($_POST['supplier_name'] ?? '');
    $program_title = trim($_POST['program_title'] ?? '');
    $proponent = trim($_POST['proponent'] ?? '');
    $coverage_start = trim($_POST['coverage_start'] ?? '');
    $coverage_end = trim($_POST['coverage_end'] ?? '');
    $expected_date = trim($_POST['expected_date'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $po_number = trim($_POST['po_number'] ?? '');
    $po_type = trim($_POST['po_type'] ?? '');
    $po_type_other = trim($_POST['po_type_other'] ?? '');

    // Validation
    if (empty($supplier_name) || empty($program_title) || empty($proponent)
        || empty($amount) || empty($po_number)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill in all required fields'
        ]);
        exit;
    }

    // Validate PO number: digits and - _ /
    if (!preg_match('/^[0-9\/_-]+$/', $po_number)) {
        echo json_encode([
            'success' => false,
            'message' => 'PO number may contain numbers and the symbols - _ / only'
        ]);
        exit;
    }

    $final_po_type = $po_type_other !== '' ? $po_type_other : $po_type;
    if ($final_po_type === '') {
        echo json_encode([
            'success' => false,
            'message' => 'Please select a type or enter another type'
        ]);
        exit;
    }

    // Validate PO type
    $allowedTypes = ['Transpo/venue', 'Supplies', 'Meals', 'Services'];
    if ($po_type_other === '') {
        if (!in_array($final_po_type, $allowedTypes, true)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid PO type selected'
            ]);
            exit;
        }
    }

    // Validate coverage date range (optional)
    if ($coverage_start !== '' && $coverage_end !== '' && $coverage_start > $coverage_end) {
        echo json_encode([
            'success' => false,
            'message' => 'Coverage start date cannot be after end date'
        ]);
        exit;
    }

    // Validate amount is numeric
    if (!is_numeric($amount) || $amount <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid amount entered'
        ]);
        exit;
    }

    $db->beginTransaction();

    if ($expected_date === '') {
        $expected_date = null;
    }

    if ($coverage_start === '') {
        $coverage_start = null;
    }
    if ($coverage_end === '') {
        $coverage_end = null;
    }

    // Check if supplier exists or create new one
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
        (supplier_id, po_number, program_title, po_type, proponent, coverage_start, coverage_end, expected_date, amount, proc_status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
    
    $stmt->execute([
        $supplier_id,
        $po_number,
        $program_title,
        $final_po_type,
        $proponent,
        $coverage_start,
        $coverage_end,
        $expected_date,
        $amount,
        null
    ]);

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Purchase Order created successfully. PO Number: ' . $po_number,
        'po_number' => $po_number
    ]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error creating purchase order: ' . $e->getMessage()
    ]);
}
?>
