<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';

require_login();

header('Content-Type: application/json');

try {
    $db = get_db();
    $role = $_SESSION['role'] ?? '';

    $active = 0;
    $pending = 0;
    $approved = 0;

    $nz = function (string $col): string {
        // non-empty string check (NULL or '' both treated as empty)
        return "(NULLIF(TRIM($col), '') IS NOT NULL)";
    };

    if ($role === 'procurement') {
        $stmt = $db->query(
            'SELECT '
            . 'SUM(CASE WHEN ' . $nz('cashier_status') . ' THEN 1 ELSE 0 END) AS approved, '
            . 'SUM(CASE WHEN NOT ' . $nz('cashier_status') . ' AND (' . $nz('supply_status') . ' OR ' . $nz('acct_pre_status') . ' OR ' . $nz('budget_status') . ' OR ' . $nz('acct_post_status') . ') THEN 1 ELSE 0 END) AS pending, '
            . 'SUM(CASE WHEN NOT ' . $nz('cashier_status') . ' AND ' . $nz('proc_status') . ' AND NOT ' . $nz('supply_status') . ' THEN 1 ELSE 0 END) AS active '
            . 'FROM transactions'
        );
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        $active = (int)($row['active'] ?? 0);
        $pending = (int)($row['pending'] ?? 0);
        $approved = (int)($row['approved'] ?? 0);
    } elseif ($role === 'supply') {
        $stmt = $db->query(
            'SELECT '
            . 'SUM(CASE WHEN ' . $nz('cashier_status') . ' THEN 1 ELSE 0 END) AS approved, '
            . 'SUM(CASE WHEN NOT ' . $nz('cashier_status') . ' AND ' . $nz('supply_status') . ' THEN 1 ELSE 0 END) AS pending, '
            . 'SUM(CASE WHEN NOT ' . $nz('cashier_status') . ' AND proc_date IS NOT NULL AND NOT ' . $nz('supply_status') . ' THEN 1 ELSE 0 END) AS active '
            . 'FROM transactions '
            . 'WHERE proc_date IS NOT NULL'
        );
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        $active = (int)($row['active'] ?? 0);
        $pending = (int)($row['pending'] ?? 0);
        $approved = (int)($row['approved'] ?? 0);
    } elseif ($role === 'accounting') {
        $stmt = $db->query(
            'SELECT '
            . 'SUM(CASE WHEN ' . $nz('cashier_status') . ' THEN 1 ELSE 0 END) AS approved, '
            . 'SUM(CASE WHEN NOT ' . $nz('cashier_status') . ' AND ((' . $nz('acct_pre_status') . ' AND NOT ' . $nz('budget_status') . ') OR (' . $nz('acct_post_status') . ' AND NOT ' . $nz('cashier_status') . ')) THEN 1 ELSE 0 END) AS pending, '
            . 'SUM(CASE WHEN NOT ' . $nz('cashier_status') . ' AND ((' . $nz('supply_status') . ' AND NOT ' . $nz('acct_pre_status') . ') OR (' . $nz('budget_status') . ' AND NOT ' . $nz('acct_post_status') . ')) THEN 1 ELSE 0 END) AS active '
            . 'FROM transactions '
            . 'WHERE ' . $nz('supply_status')
        );
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        $active = (int)($row['active'] ?? 0);
        $pending = (int)($row['pending'] ?? 0);
        $approved = (int)($row['approved'] ?? 0);
    } elseif ($role === 'budget') {
        $stmt = $db->query(
            'SELECT '
            . 'SUM(CASE WHEN ' . $nz('cashier_status') . ' THEN 1 ELSE 0 END) AS approved, '
            . 'SUM(CASE WHEN NOT ' . $nz('cashier_status') . ' AND ' . $nz('budget_status') . ' THEN 1 ELSE 0 END) AS pending, '
            . 'SUM(CASE WHEN NOT ' . $nz('cashier_status') . ' AND ' . $nz('acct_pre_status') . ' AND NOT ' . $nz('budget_status') . ' THEN 1 ELSE 0 END) AS active '
            . 'FROM transactions '
            . 'WHERE ' . $nz('acct_pre_status')
        );
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        $active = (int)($row['active'] ?? 0);
        $pending = (int)($row['pending'] ?? 0);
        $approved = (int)($row['approved'] ?? 0);
    } elseif ($role === 'cashier') {
        $stmt = $db->query(
            'SELECT '
            . "SUM(CASE WHEN UPPER(TRIM(cashier_status)) = 'COMPLETED' THEN 1 ELSE 0 END) AS approved, "
            . "SUM(CASE WHEN " . $nz('cashier_status') . " AND UPPER(TRIM(cashier_status)) <> 'COMPLETED' THEN 1 ELSE 0 END) AS pending, "
            . 'SUM(CASE WHEN NOT ' . $nz('cashier_status') . ' AND ' . $nz('acct_post_status') . ' THEN 1 ELSE 0 END) AS active '
            . 'FROM transactions '
            . 'WHERE ' . $nz('acct_post_status')
        );
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        $active = (int)($row['active'] ?? 0);
        $pending = (int)($row['pending'] ?? 0);
        $approved = (int)($row['approved'] ?? 0);
    }

    echo json_encode([
        'success' => true,
        'active' => $active,
        'pending' => $pending,
        'approved' => $approved,
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load dashboard stats.',
    ]);
}
