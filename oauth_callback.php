<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Handle Google OAuth 2.0 callback for suppliers

if (!isset($_GET['code'])) {
    header('Location: login.php');
    exit;
}

// Optional: state check to mitigate CSRF
if (!empty($_SESSION['oauth2_state'])) {
    $sessionState = $_SESSION['oauth2_state'];
    unset($_SESSION['oauth2_state']);
    if (!isset($_GET['state']) || $_GET['state'] !== $sessionState) {
        header('Location: login.php?oauth_error=state');
        exit;
    }
}

$code = $_GET['code'];

// Exchange code for access token via HTTP POST
$tokenEndpoint = 'https://oauth2.googleapis.com/token';
$postData = http_build_query([
    'code' => $code,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code',
]);

$opts = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $postData,
        'timeout' => 15,
    ],
];

$context  = stream_context_create($opts);
$response = @file_get_contents($tokenEndpoint, false, $context);

if ($response === false) {
    header('Location: login.php?oauth_error=token');
    exit;
}

$tokenData = json_decode($response, true);
if (!is_array($tokenData) || empty($tokenData['access_token'])) {
    header('Location: login.php?oauth_error=token');
    exit;
}

$accessToken = $tokenData['access_token'];

// Fetch user info from Google
$userInfoEndpoint = 'https://www.googleapis.com/oauth2/v2/userinfo';
$optsUser = [
    'http' => [
        'method' => 'GET',
        'header' => "Authorization: Bearer {$accessToken}\r\n",
        'timeout' => 15,
    ],
];
$contextUser = stream_context_create($optsUser);
$userJson    = @file_get_contents($userInfoEndpoint, false, $contextUser);

if ($userJson === false) {
    header('Location: login.php?oauth_error=userinfo');
    exit;
}

$userData = json_decode($userJson, true);
$email = isset($userData['email']) ? strtolower(trim($userData['email'])) : '';
$googleName = isset($userData['name']) ? trim($userData['name']) : '';

if ($email === '') {
    header('Location: login.php?oauth_error=noemail');
    exit;
}

// Auto-provision supplier + user if needed, or reuse existing ones
$db = get_db();

try {
    $db->beginTransaction();

    // 1) Find or create supplier by email (assumes suppliers.email exists)
    $stmt = $db->prepare('SELECT id, name FROM suppliers WHERE LOWER(email) = ? LIMIT 1');
    $stmt->execute([$email]);
    $supplier = $stmt->fetch();

    if (!$supplier) {
        // Create new supplier record
        $supplierName = $googleName !== '' ? $googleName : $email;
        $insertSup = $db->prepare('INSERT INTO suppliers (name, email) VALUES (?, ?)');
        $insertSup->execute([$supplierName, $email]);
        $supplierId = (int)$db->lastInsertId();
    } else {
        $supplierId = (int)$supplier['id'];
    }

    // 2) Find or create user account linked to this supplier with supplier role
    // Get role_id for supplier
    $roleStmt = $db->prepare('SELECT id FROM roles WHERE name = ? LIMIT 1');
    $roleStmt->execute(['supplier']);
    $role = $roleStmt->fetch();
    if (!$role) {
        // No supplier role configured
        $db->rollBack();
        header('Location: login.php?oauth_error=norole');
        exit;
    }
    $supplierRoleId = (int)$role['id'];

    // Try to find an existing user for this supplier_id with supplier role
    $userStmt = $db->prepare('SELECT u.*, r.name AS role_name
                              FROM users u
                              JOIN roles r ON u.role_id = r.id
                              WHERE u.supplier_id = ? AND u.role_id = ?
                              LIMIT 1');
    $userStmt->execute([$supplierId, $supplierRoleId]);
    $user = $userStmt->fetch();

    if (!$user) {
        // Auto-create a user tied to this supplier
        $baseUsername = strstr($email, '@', true) ?: $email;
        $username = $baseUsername;

        // Ensure username is unique
        $checkStmt = $db->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $suffix = 1;
        while (true) {
            $checkStmt->execute([$username]);
            if (!$checkStmt->fetch()) {
                break;
            }
            $username = $baseUsername . $suffix;
            $suffix++;
        }

        // Generate a random password (not used, but required for schema)
        $randomPassword = bin2hex(random_bytes(16));
        $passwordHash = password_hash($randomPassword, PASSWORD_DEFAULT);

        $insertUser = $db->prepare('INSERT INTO users (username, password_hash, role_id, supplier_id) VALUES (?, ?, ?, ?)');
        $insertUser->execute([$username, $passwordHash, $supplierRoleId, $supplierId]);

        $userId = (int)$db->lastInsertId();

        // Load the created user with role_name
        $userStmt = $db->prepare('SELECT u.*, r.name AS role_name
                                  FROM users u
                                  JOIN roles r ON u.role_id = r.id
                                  WHERE u.id = ?');
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch();
    }

    $db->commit();
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    header('Location: login.php?oauth_error=server');
    exit;
}

if (!$user) {
    header('Location: login.php?oauth_error=unknown_user');
    exit;
}

// Successful OAuth login: establish the same session as normal login
$_SESSION['user_id']      = $user['id'];
$_SESSION['username']     = $user['username'];
$_SESSION['role']         = $user['role_name'];
$_SESSION['supplier_id']  = $user['supplier_id'];

header('Location: dashboard.php');
exit;
