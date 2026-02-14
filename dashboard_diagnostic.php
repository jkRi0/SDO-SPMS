<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

echo "<h2>Dashboard Diagnostic</h2>";
echo "<hr>";

// Check session
echo "<h3>Session Status:</h3>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    echo "<p>✓ User is logged in (ID: " . $_SESSION['user_id'] . ")</p>";
    echo "<p>✓ Username: " . $_SESSION['username'] . "</p>";
    echo "<p>✓ Role: " . $_SESSION['role'] . "</p>";
} else {
    echo "<p>✗ User is NOT logged in</p>";
    echo "<p><a href='login.php'>Go to Login</a></p>";
    exit;
}

// Check database connection
echo "<h3>Database Connection:</h3>";
try {
    $db = get_db();
    echo "<p>✓ Database connected successfully</p>";
    
    // Check transactions table
    $count = $db->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
    echo "<p>✓ Transactions table exists (Records: $count)</p>";
    
    // Check stats
    $activePOs = (int)$db->query('SELECT COUNT(*) AS c FROM transactions WHERE proc_status IS NOT NULL AND supply_status IS NULL')->fetch()['c'];
    $pendingReview = (int)$db->query('SELECT COUNT(*) AS c FROM transactions WHERE (supply_status IS NOT NULL OR acct_pre_status IS NOT NULL OR budget_status IS NOT NULL OR acct_post_status IS NOT NULL) AND cashier_status IS NULL')->fetch()['c'];
    $approved = (int)$db->query('SELECT COUNT(*) AS c FROM transactions WHERE cashier_status IS NOT NULL')->fetch()['c'];
    
    echo "<p>✓ Dashboard Stats Calculated:</p>";
    echo "<ul>";
    echo "<li>Active POs: $activePOs</li>";
    echo "<li>Pending Review: $pendingReview</li>";
    echo "<li>Approved: $approved</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p>✗ Database Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>Back to Dashboard</a></p>";
?>
