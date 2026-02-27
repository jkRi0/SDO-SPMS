<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'spms');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3306');

define('BASE_URL', '');

define('LANDBANK_URL', 'https://www.lbpemds.com/');

// Google OAuth 2.0 settings for supplier login (fill these with your own values)
define('GOOGLE_CLIENT_ID', '');
define('GOOGLE_CLIENT_SECRET', '');
// Example for local XAMPP: 'http://localhost/samples/SDO-SPMS/oauth_callback.php'
define('GOOGLE_REDIRECT_URI', 'http://localhost/samples/SDO-SPMS/oauth_callback.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

