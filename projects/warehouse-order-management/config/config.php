<?php
// App-wide configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

// Every page here is dynamic and often shows per-user/per-record data, so never
// let the browser cache a response and reuse it for a different query string
// or after the underlying data changed (this caused stale user/order data to
// show after navigating between records).
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'warehouse_orders');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('APP_URL', getenv('APP_URL') ?: '');

// Secret key used to encrypt record IDs shown in URLs (see id_encode()/id_decode()
// in includes/functions.php), so a URL like order_view.php?id=... can't be
// decrypted or guessed without this key. Change this to your own random 64-hex-
// character value (e.g. via `php -r "echo bin2hex(random_bytes(32));"`) and keep
// it secret — changing it invalidates any links/bookmarks already shared.
define('APP_KEY', getenv('APP_KEY') ?: '331463451bea9ec2def550896541ce54458deb91b9ac1ee6d7768a753a1f520d');

error_reporting(E_ALL);
ini_set('display_errors', '0');
