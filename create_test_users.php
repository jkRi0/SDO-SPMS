<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

echo "<h2>Create Test Users for Each Role</h2>";
echo "<hr>";

try {
    $db = get_db();
    
    // Test users for each role
    $testUsers = [
        ['username' => 'procurement@deped.gov', 'role' => 'procurement', 'password' => 'password123'],
        ['username' => 'supply@deped.gov', 'role' => 'supply', 'password' => 'password123'],
        ['username' => 'accounting@deped.gov', 'role' => 'accounting', 'password' => 'password123'],
        ['username' => 'budget@deped.gov', 'role' => 'budget', 'password' => 'password123'],
        ['username' => 'cashier@deped.gov', 'role' => 'cashier', 'password' => 'password123'],
    ];
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Username</th><th>Role</th><th>Password</th><th>Status</th></tr>";
    
    foreach ($testUsers as $user) {
        // Check if user exists
        $stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$user['username']]);
        if ($stmt->fetch()) {
            echo "<tr>";
            echo "<td>" . $user['username'] . "</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "<td>" . $user['password'] . "</td>";
            echo "<td style='color: orange;'>✓ Already exists</td>";
            echo "</tr>";
            continue;
        }
        
        // Get role ID
        $roleStmt = $db->prepare('SELECT id FROM roles WHERE name = ?');
        $roleStmt->execute([$user['role']]);
        $roleData = $roleStmt->fetch();
        
        if (!$roleData) {
            echo "<tr>";
            echo "<td>" . $user['username'] . "</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "<td>" . $user['password'] . "</td>";
            echo "<td style='color: red;'>✗ Role not found</td>";
            echo "</tr>";
            continue;
        }
        
        // Create user
        $stmt = $db->prepare('INSERT INTO users (username, password_hash, role_id) VALUES (?, ?, ?)');
        $stmt->execute([
            $user['username'],
            password_hash($user['password'], PASSWORD_DEFAULT),
            $roleData['id']
        ]);
        
        echo "<tr>";
        echo "<td>" . $user['username'] . "</td>";
        echo "<td>" . $user['role'] . "</td>";
        echo "<td>" . $user['password'] . "</td>";
        echo "<td style='color: green;'>✓ Created</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<hr>";
    echo "<h3>Test Users Created Successfully!</h3>";
    echo "<p>You can now log in with any of these accounts:</p>";
    echo "<ul>";
    foreach ($testUsers as $user) {
        echo "<li><strong>" . $user['username'] . "</strong> - Password: <strong>" . $user['password'] . "</strong></li>";
    }
    echo "</ul>";
    
    echo "<hr>";
    echo "<p><a href='login.php' style='padding: 10px 20px; background: #3b7ca8; color: white; text-decoration: none; border-radius: 6px;'>Go to Login</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
?>
