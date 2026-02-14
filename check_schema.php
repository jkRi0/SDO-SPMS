<?php
require_once __DIR__ . '/db.php';

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
