<?php
// Simple audit helper
function create_log($db, $user_id, $action, $target_type = null, $target_id = null, $details = null) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    try {
        $stmt = $db->prepare('INSERT INTO activity_logs (user_id, action, target_type, target_id, details, ip) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$user_id, $action, $target_type, $target_id, $details, $ip]);
    } catch (Exception $e) {
        try {
            $stmt2 = $db->prepare('INSERT INTO activity_logs (user_id, action, target_type, target_id, details) VALUES (?, ?, ?, ?, ?)');
            $stmt2->execute([$user_id, $action, $target_type, $target_id, $details]);
        } catch (Exception $e2) {
            // If logging fails (e.g., table missing), do not block main flow
            error_log('create_log failed: ' . $e->getMessage());
        }
    }
}

function format_log_details($action, $details) {
    if ($details === null) {
        return '';
    }

    $detailsStr = is_string($details) ? trim($details) : '';
    if ($detailsStr === '') {
        return '';
    }

    $decoded = json_decode($detailsStr, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
        return $detailsStr;
    }

    if ($action === 'update_account') {
        $oldU = $decoded['old_username'] ?? '';
        $newU = $decoded['new_username'] ?? '';
        $pwChanged = !empty($decoded['password_changed']);

        $parts = [];
        if ($oldU !== '' || $newU !== '') {
            $parts[] = 'Username: ' . ($oldU !== '' ? $oldU : '-') . ' -> ' . ($newU !== '' ? $newU : '-');
        }
        $parts[] = 'Password changed: ' . ($pwChanged ? 'Yes' : 'No');
        return implode(' | ', $parts);
    }

    if ($action === 'update_user' || $action === 'create_user') {
        $parts = [];
        if (isset($decoded['username'])) {
            $parts[] = 'Username: ' . $decoded['username'];
        }
        if (isset($decoded['role_id'])) {
            $parts[] = 'Role ID: ' . $decoded['role_id'];
        }
        if (array_key_exists('supplier_id', $decoded)) {
            $parts[] = 'Supplier ID: ' . ($decoded['supplier_id'] === null ? '' : $decoded['supplier_id']);
        }
        if (!empty($parts)) {
            return implode(' | ', $parts);
        }
    }

    $parts = [];
    foreach ($decoded as $k => $v) {
        if (is_array($v)) {
            $v = json_encode($v);
        } elseif (is_bool($v)) {
            $v = $v ? 'true' : 'false';
        } elseif ($v === null) {
            $v = '';
        }
        $parts[] = $k . ': ' . $v;
    }
    return implode(' | ', $parts);
}

function fetch_logs($db, $filters = []) {
    $sql = 'SELECT al.*, u.username, r.name AS role_name
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE 1=1';
    $params = [];

    if (!empty($filters['exclude_actions']) && is_array($filters['exclude_actions'])) {
        $exclude = array_values(array_filter($filters['exclude_actions'], function ($a) {
            return $a !== null && $a !== '';
        }));
        if (!empty($exclude)) {
            $placeholders = implode(',', array_fill(0, count($exclude), '?'));
            $sql .= ' AND al.action NOT IN (' . $placeholders . ')';
            foreach ($exclude as $a) {
                $params[] = $a;
            }
        }
    }
    if (!empty($filters['actions']) && is_array($filters['actions'])) {
        $actions = array_values(array_filter($filters['actions'], function ($a) {
            return $a !== null && $a !== '';
        }));
        if (!empty($actions)) {
            $placeholders = implode(',', array_fill(0, count($actions), '?'));
            $sql .= ' AND al.action IN (' . $placeholders . ')';
            foreach ($actions as $a) {
                $params[] = $a;
            }
        }
    }
    if (!empty($filters['action'])) {
        $sql .= ' AND al.action = ?';
        $params[] = $filters['action'];
    }
    if (!empty($filters['role'])) {
        $sql .= ' AND r.name = ?';
        $params[] = $filters['role'];
    }
    if (!empty($filters['user_id'])) {
        $sql .= ' AND al.user_id = ?';
        $params[] = (int)$filters['user_id'];
    }
    if (!empty($filters['date'])) {
        $sql .= ' AND DATE(al.created_at) = ?';
        $params[] = $filters['date'];
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
