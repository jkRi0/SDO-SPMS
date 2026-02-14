<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

header('Content-Type: application/json');

require_login();

try {
    $db = get_db();
    $stmt = $db->query('SELECT id, name FROM suppliers ORDER BY name ASC');
    $suppliers = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'suppliers' => $suppliers
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error loading suppliers'
    ]);
}
?>
