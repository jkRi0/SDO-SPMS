<?php
require_once __DIR__ . '/db.php';

echo "<h2>Database Verification</h2>";
echo "<hr>";

try {
    $db = get_db();
    
    // Check tables
    $tables = ['roles', 'suppliers', 'users', 'transactions', 'activity_logs'];
    echo "<h3>Database Tables:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        try {
            $stmt = $db->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "<li>✓ <strong>$table</strong> (Records: $count)</li>";
        } catch (Exception $e) {
            echo "<li>✗ <strong>$table</strong> (Missing)</li>";
        }
    }
    echo "</ul>";
    
    // Check transactions columns
    echo "<h3>Transactions Table Columns:</h3>";
    $stmt = $db->query("DESCRIBE transactions");
    $columns = $stmt->fetchAll();
    echo "<ul>";
    foreach ($columns as $col) {
        echo "<li>" . $col['Field'] . " (" . $col['Type'] . ")</li>";
    }
    echo "</ul>";
    
    // Check admin user
    echo "<h3>Default Admin User:</h3>";
    $stmt = $db->query("SELECT u.username, r.name as role FROM users u JOIN roles r ON u.role_id = r.id LIMIT 1");
    $user = $stmt->fetch();
    if ($user) {
        echo "<p>✓ Username: <strong>" . $user['username'] . "</strong> | Role: <strong>" . $user['role'] . "</strong></p>";
        echo "<p>✓ Default password: <strong>admin123</strong></p>";
    }
    
    echo "<hr>";
    echo "<p><a href='login.php' style='padding: 10px 20px; background: #3b7ca8; color: white; text-decoration: none; border-radius: 6px;'>Go to Login</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
?>
