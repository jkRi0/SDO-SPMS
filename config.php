<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'sdo-ftms');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3306');



define('LANDBANK_URL', 'https://www.lbpemds.com/');



//DEVELOPMENT
define('BASE_URL', 'http://localhost/samples/SDO-STMS/');

// some development urls located at:
// - https://console.cloud.google.com/ (for OAuth 2.0)
// - https://myaccount.google.com/ (for SMTP)


//PRODUCTION
// define('BASE_URL', '');
// OAuth 2.0 and SMTP settings should be configured in production environment

define('SESSION_TTL_SECONDS', 600);


// Google OAuth 2.0 settings for supplier login (fill these with your own values)
define('GOOGLE_CLIENT_ID', '');
define('GOOGLE_CLIENT_SECRET', '');
// Example for local XAMPP: BASE_URL . 'oauth_callback.php'
define('GOOGLE_REDIRECT_URI', BASE_URL . 'oauth_callback.php');

// SMTP / Gmail settings for outgoing mail (update these with real values)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587); // 587 for TLS
define('SMTP_USERNAME', 'alphanum****@gmail.com'); //change to real Gmail
define('SMTP_PASSWORD', ''); //change to real app password
define('SMTP_FROM_EMAIL', 'alphanum0001@gmail.com'); //change to desired From address
define('SMTP_FROM_NAME', 'SDO-FTMS');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

