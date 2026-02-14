<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

echo "<h2>Adding Test Users to Database</h2>";
echo "<hr>";

try {
    $db = get_db();
    
    // First, check what roles exist
    $roles = $db->query('SELECT id, name FROM roles ORDER BY id')->fetchAll();
    echo "<h3>Available Roles:</h3>";
    echo "<ul>";
    foreach ($roles as $role) {
        echo "<li>ID: " . $role['id'] . " - " . $role['name'] . "</li>";
    }
    echo "</ul>";
    
    echo "<hr>";
    echo "<h3>Creating Test Users:</h3>";
    
    // Test users for each role
    $testUsers = [
        ['username' => 'procurement@deped.gov', 'role' => 'procurement', 'password' => 'password123'],
        ['username' => 'supply@deped.gov', 'role' => 'supply', 'password' => 'password123'],
        ['username' => 'accounting@deped.gov', 'role' => 'accounting', 'password' => 'password123'],
        ['username' => 'budget@deped.gov', 'role' => 'budget', 'password' => 'password123'],
        ['username' => 'cashier@deped.gov', 'role' => 'cashier', 'password' => 'password123'],
    ];
    
    $created = 0;
    $skipped = 0;
    
    foreach ($testUsers as $user) {
        // Check if user exists
        $checkStmt = $db->prepare('SELECT id FROM users WHERE username = ?');
        $checkStmt->execute([$user['username']]);
        $existing = $checkStmt->fetch();
        
        if ($existing) {
            echo "<p style='color: orange;'>⊘ <strong>" . $user['username'] . "</strong> already exists</p>";
            $skipped++;
            continue;
        }
        
        // Get role ID
        $roleStmt = $db->prepare('SELECT id FROM roles WHERE name = ?');
        $roleStmt->execute([$user['role']]);
        $roleData = $roleStmt->fetch();
        
        if (!$roleData) {
            echo "<p style='color: red;'>✗ <strong>" . $user['username'] . "</strong> - Role '{$user['role']}' not found</p>";
            continue;
        }
        
        // Create user
        try {
            $insertStmt = $db->prepare('INSERT INTO users (username, password_hash, role_id) VALUES (?, ?, ?)');
            $insertStmt->execute([
                $user['username'],
                password_hash($user['password'], PASSWORD_DEFAULT),
                $roleData['id']
            ]);
            echo "<p style='color: green;'>✓ <strong>" . $user['username'] . "</strong> created (Role: {$user['role']}, Password: {$user['password']})</p>";
            $created++;
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Error creating {$user['username']}: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<p><strong>Summary:</strong> $created created, $skipped skipped</p>";
    
    // Show all users now
    echo "<hr>";
    echo "<h3>All Users in Database:</h3>";
    $allUsers = $db->query('SELECT u.username, r.name as role FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id')->fetchAll();
    echo "<ul>";
    foreach ($allUsers as $user) {
        echo "<li><strong>" . $user['username'] . "</strong> - Role: " . $user['role'] . "</li>";
    }
    echo "</ul>";
    
    echo "<hr>";
    echo "<p><a href='login.php' style='padding: 10px 20px; background: #3b7ca8; color: white; text-decoration: none; border-radius: 6px;'>Go to Login</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Fatal Error:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
