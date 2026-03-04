<?php
if (PHP_SAPI !== 'cli') {
    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($remoteAddr, ['127.0.0.1', '::1'], true)) {
        http_response_code(403);
        exit('Forbidden');
    }
}

require_once __DIR__ . '/../db.php';

$db = get_db();

try {
    $stmt = $db->query("DESCRIBE transactions");
    $columns = $stmt->fetchAll();
    
    echo "<pre>";
    echo "Transaction Table Columns:\n";
    echo "===========================\n";
    foreach ($columns as $col) {
        echo $col['Field'] . " - " . $col['Type'] . " (" . $col['Null'] . ")\n";
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
