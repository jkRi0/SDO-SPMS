<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();

$db = get_db();

// Reuse the shared transactions table markup
include __DIR__ . '/partials_transactions_table.php';
