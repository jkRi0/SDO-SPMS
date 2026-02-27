<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();
if (($_SESSION['role'] ?? '') !== 'cashier') {
    http_response_code(403);
    echo 'Access denied.';
    exit;
}

$db = get_db();

// Get a transaction for checking
$stmt = $db->query('SELECT * FROM transactions LIMIT 1');
$transaction = $stmt->fetch();

if (!$transaction) {
    echo "<p>No transactions found. Please create a transaction first.</p>";
    exit;
}

echo "<h2>Landbank Button Requirements Checker</h2>";
echo "<hr>";
echo "<h3>Transaction ID: " . $transaction['id'] . "</h3>";

// Check 1: Procurement Status
echo "<h4>1. Procurement Status</h4>";
if (empty($transaction['proc_status'])) {
    echo "<p style='color: red;'>❌ NOT SET</p>";
} elseif (in_array($transaction['proc_status'], ['PENDING', 'FOR CORRECTION'])) {
    echo "<p style='color: orange;'>⊘ " . $transaction['proc_status'] . " (must be completed)</p>";
} else {
    echo "<p style='color: green;'>✓ " . $transaction['proc_status'] . "</p>";
}

// Check 2: Supply Status
echo "<h4>2. Supply Status</h4>";
if (empty($transaction['supply_status'])) {
    echo "<p style='color: red;'>❌ NOT SET</p>";
} elseif (in_array($transaction['supply_status'], ['PENDING', 'FOR CORRECTION'])) {
    echo "<p style='color: orange;'>⊘ " . $transaction['supply_status'] . " (must be completed)</p>";
} else {
    echo "<p style='color: green;'>✓ " . $transaction['supply_status'] . "</p>";
}

// Check 3: Initial Accounting Status
echo "<h4>3. Accounting Status (initial review)</h4>";
if (empty($transaction['acct_pre_status'])) {
    echo "<p style='color: red;'>❌ NOT SET</p>";
} elseif (in_array($transaction['acct_pre_status'], ['PENDING', 'FOR CORRECTION'])) {
    echo "<p style='color: orange;'>⊘ " . $transaction['acct_pre_status'] . " (must be completed)</p>";
} else {
    echo "<p style='color: green;'>✓ " . $transaction['acct_pre_status'] . "</p>";
}

// Check 4: Budget Status & DV Details
echo "<h4>4. Budget Status</h4>";
if (empty($transaction['budget_status'])) {
    echo "<p style='color: red;'>❌ NOT SET</p>";
} elseif (in_array($transaction['budget_status'], ['PENDING', 'FOR CORRECTION'])) {
    echo "<p style='color: orange;'>⊘ " . $transaction['budget_status'] . " (must be completed)</p>";
} else {
    echo "<p style='color: green;'>✓ " . $transaction['budget_status'] . "</p>";
}

echo "<h4>4a. Budget DV Number</h4>";
if (empty($transaction['budget_dv_number'])) {
    echo "<p style='color: red;'>❌ NOT SET</p>";
} else {
    echo "<p style='color: green;'>✓ " . $transaction['budget_dv_number'] . "</p>";
}

echo "<h4>4b. Budget DV Date</h4>";
if (empty($transaction['budget_dv_date'])) {
    echo "<p style='color: red;'>❌ NOT SET</p>";
} else {
    echo "<p style='color: green;'>✓ " . $transaction['budget_dv_date'] . "</p>";
}

// Check 5: Final Accounting Status
echo "<h4>5. Accounting Status (final)</h4>";
if (empty($transaction['acct_post_status'])) {
    echo "<p style='color: red;'>❌ NOT SET</p>";
} elseif (in_array($transaction['acct_post_status'], ['PENDING', 'FOR CORRECTION'])) {
    echo "<p style='color: orange;'>⊘ " . $transaction['acct_post_status'] . " (must be completed)</p>";
} else {
    echo "<p style='color: green;'>✓ " . $transaction['acct_post_status'] . "</p>";
}

echo "<hr>";
echo "<h3>Summary</h3>";

// Overall check
$canProceed = 
    !empty($transaction['proc_status']) && !in_array($transaction['proc_status'], ['FOR CORRECTION', 'PENDING'], true) &&
    !empty($transaction['supply_status']) && !in_array($transaction['supply_status'], ['FOR CORRECTION', 'PENDING'], true) &&
    !empty($transaction['acct_pre_status']) && !in_array($transaction['acct_pre_status'], ['FOR CORRECTION', 'PENDING'], true) &&
    !empty($transaction['budget_status']) && !in_array($transaction['budget_status'], ['FOR CORRECTION', 'PENDING'], true) &&
    !empty($transaction['budget_dv_number']) && !empty($transaction['budget_dv_date']) &&
    !empty($transaction['acct_post_status']) && !in_array($transaction['acct_post_status'], ['FOR CORRECTION', 'PENDING'], true);

if ($canProceed) {
    echo "<p style='color: green; font-size: 18px;'><strong>✓ READY TO PROCEED TO LANDBANK!</strong></p>";
} else {
    echo "<p style='color: red; font-size: 18px;'><strong>❌ NOT READY - Complete the missing items above</strong></p>";
}

echo "<hr>";
echo "<p><a href='transaction_view.php?id=" . $transaction['id'] . "'>Back to Transaction</a></p>";
?>
