<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

header('Content-Type: application/json');

require_login();

$role = $_SESSION['role'] ?? '';
if ($role !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

$db = get_db();

try {
    $stmt = $db->query('SELECT f.id, f.type, f.message, f.created_at, f.role AS from_role, f.is_read, u.username
                        FROM feedback f
                        LEFT JOIN users u ON f.user_id = u.id
                        ORDER BY f.created_at DESC
                        LIMIT 10');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $items = [];
    $unreadCount = 0;
    foreach ($rows as $row) {
        $preview = trim($row['message']);
        if (mb_strlen($preview) > 80) {
            $preview = mb_substr($preview, 0, 77) . '...';
        }

        if (empty($row['is_read'])) {
            $unreadCount++;
        }

        $items[] = [
            'id'         => (int)$row['id'],
            'type'       => $row['type'],
            'message'    => $preview,
            'created_at' => date('m/d/Y H:i', strtotime($row['created_at'])),
            'from_role'  => $row['from_role'],
            'is_read'    => (int)$row['is_read'],
            'username'   => $row['username'] ?? 'N/A',
        ];
    }

    echo json_encode([
        'success'  => true,
        'feedback' => $items,
        'count'    => count($items),
        'unread'   => $unreadCount,
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error loading feedback']);
}
