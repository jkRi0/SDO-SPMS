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
    $ttl = defined('SESSION_TTL_SECONDS') ? (int)SESSION_TTL_SECONDS : 600;

    $stmt = $db->prepare('SELECT u.id, u.username, r.name AS role_name, MAX(us.last_seen) AS last_seen
                          FROM user_sessions us
                          JOIN users u ON us.user_id = u.id
                          LEFT JOIN roles r ON u.role_id = r.id
                          WHERE us.revoked_at IS NULL
                            AND us.last_seen IS NOT NULL
                            AND us.last_seen >= (NOW() - INTERVAL ? SECOND)
                          GROUP BY u.id, u.username, r.name
                          ORDER BY last_seen DESC
                          LIMIT 20');
    $stmt->execute([$ttl]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $items = [];
    foreach ($rows as $row) {
        $items[] = [
            'id' => (int)$row['id'],
            'username' => $row['username'] ?? '',
            'role_name' => $row['role_name'] ?? '',
            'last_seen' => !empty($row['last_seen']) ? date('m/d/Y H:i', strtotime($row['last_seen'])) : '',
        ];
    }

    echo json_encode([
        'success' => true,
        'count' => count($items),
        'users' => $items,
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error loading online users']);
}
