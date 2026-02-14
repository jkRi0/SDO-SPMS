<?php
require_once __DIR__ . '/db.php';

/**
 * Redirect to login page if user is not authenticated.
 */
function require_login()
{
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Redirect if the user does not have one of the allowed roles.
 *
 * @param array $allowed_roles
 */
function require_role(array $allowed_roles)
{
    require_login();
    if (empty($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles, true)) {
        http_response_code(403);
        echo 'Access denied.';
        exit;
    }
}

/**
 * Returns the current logged in user record.
 */
function current_user()
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    static $cachedUser = null;
    if ($cachedUser !== null) {
        return $cachedUser;
    }

    $db = get_db();
    $stmt = $db->prepare('SELECT u.*, r.name AS role_name 
                          FROM users u 
                          LEFT JOIN roles r ON u.role_id = r.id 
                          WHERE u.id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $cachedUser = $stmt->fetch();

    return $cachedUser;
}

