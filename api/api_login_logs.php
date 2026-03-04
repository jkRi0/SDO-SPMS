<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';

header('Content-Type: application/json');

require_login();

$role = $_SESSION['role'] ?? '';
if ($role !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

$db = get_db();

try {
    $limit = 8;
    if (isset($_GET['limit'])) {
        $limit = (int)$_GET['limit'];
    }
    if ($limit <= 0) {
        $limit = 8;
    }
    if ($limit > 50) {
        $limit = 50;
    }

    $stmt = $db->prepare('SELECT al.created_at, al.action, al.details, u.username
                          FROM activity_logs al
                          LEFT JOIN users u ON al.user_id = u.id
                          WHERE al.action IN (?,?,?)
                          ORDER BY al.created_at DESC
                          LIMIT ' . $limit);
    $stmt->execute(['login', 'logout', 'login_failed']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $items = [];
    foreach ($rows as $row) {
        $usernameOut = $row['username'] ?? 'System';
        if (($row['action'] ?? '') === 'login_failed' && empty($row['username']) && !empty($row['details'])) {
            $decoded = json_decode($row['details'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded['attempted_username'])) {
                $usernameOut = (string)$decoded['attempted_username'];
            }
        }
        $items[] = [
            'created_at' => $row['created_at'] ?? null,
            'time' => !empty($row['created_at']) ? date('m/d/Y H:i', strtotime($row['created_at'])) : '',
            'username' => $usernameOut,
            'event' => $row['action'] ?? '',
        ];
    }

    echo json_encode([
        'success' => true,
        'count' => count($items),
        'logs' => $items,
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error loading login logs']);
}
