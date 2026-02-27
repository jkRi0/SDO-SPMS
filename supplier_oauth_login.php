<?php
require_once __DIR__ . '/config.php';

// Simple plain-PHP Google OAuth 2.0 start for suppliers

// Build the Google authorization URL manually
$clientId     = GOOGLE_CLIENT_ID;
$redirectUri  = urlencode(GOOGLE_REDIRECT_URI);
$scope        = urlencode('openid email profile');
$state        = bin2hex(random_bytes(16));

// Store state in session to verify later
$_SESSION['oauth2_state'] = $state;

$authUrl = 'https://accounts.google.com/o/oauth2/v2/auth'
    . '?response_type=code'
    . '&client_id=' . urlencode($clientId)
    . '&redirect_uri=' . $redirectUri
    . '&scope=' . $scope
    . '&state=' . urlencode($state)
    . '&access_type=online'
    . '&include_granted_scopes=true';

header('Location: ' . $authUrl);
exit;
