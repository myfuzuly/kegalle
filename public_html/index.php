<?php

// ── Static page-cache fast path (zero Laravel bootstrap cost) ──
// Only bypass PHP for cookie-less requests (crawlers, first-time visitors).
// Repeat visitors go through Laravel middleware which also reads the same file.
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_COOKIE)) {
    $qs   = $_SERVER['QUERY_STRING'] ?? '';
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $key  = 'pagecache_' . sha1($path . ($qs ? '?' . $qs : ''));
    $file = '/home/kegalle/app_core/storage/framework/pagecache/' . $key . '.html';
    if (file_exists($file) && (time() - filemtime($file)) < 300) {
        header('Content-Type: text/html; charset=UTF-8');
        header('X-Cache: FILE-HIT');
        readfile($file);
        exit;
    }
}
// ──────────────────────────────────────────────────────────────

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require '/home/kegalle/app_core/vendor/autoload.php';

$app = require_once '/home/kegalle/app_core/bootstrap/app.php';

$app->handleRequest(Request::capture());