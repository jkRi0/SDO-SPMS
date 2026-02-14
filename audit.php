<?php
// Simple audit helper
function create_log($db, $user_id, $action, $target_type = null, $target_id = null, $details = null) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    try {
        $stmt = $db->prepare('INSERT INTO activity_logs (user_id, action, target_type, target_id, details, ip) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$user_id, $action, $target_type, $target_id, $details, $ip]);
    } catch (Exception $e) {
        // If logging fails (e.g., table missing), do not block main flow
        error_log('create_log failed: ' . $e->getMessage());
    }
}

function fetch_logs($db, $filters = []) {
    $sql = 'SELECT al.*, u.username FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id WHERE 1=1';
    $params = [];

    if (!empty($filters['action'])) {
        $sql .= ' AND al.action = ?';
        $params[] = $filters['action'];
    }
    if (!empty($filters['user_id'])) {
        $sql .= ' AND al.user_id = ?';
        $params[] = (int)$filters['user_id'];
    }
    if (!empty($filters['from'])) {
        $sql .= ' AND al.created_at >= ?';
        $params[] = $filters['from'];
    }
    if (!empty($filters['to'])) {
        $sql .= ' AND al.created_at <= ?';
        $params[] = $filters['to'];
    }

    $sql .= ' ORDER BY al.created_at DESC LIMIT 1000';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
