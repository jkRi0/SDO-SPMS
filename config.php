<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'spms');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3306');

define('BASE_URL', '');

define('LANDBANK_URL', 'https://www.lbpemds.com/');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

