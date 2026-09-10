<?php

// ── Static page-cache fast path (zero Laravel bootstrap cost) ──
// Only bypass PHP for cookie-less requests (crawlers, first-time visitors).
// Repeat visitors go through Laravel middleware which also reads the same file.
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_COOKIE)) {
    $qs      = $_SERVER['QUERY_STRING'] ?? '';
    $rawPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    // Normalise to match $request->path() — strip leading slash except for homepage
    $path = $rawPath === '/' ? '/' : ltrim($rawPath, '/');
    $key  = 'pagecache_' . sha1($path . ($qs ? '?' . $qs : ''));
    $file = '/home/kegalle/app_core/storage/framework/pagecache/' . $key . '.html';
    if (file_exists($file) && (time() - filemtime($file)) < 300) {
        header('Content-Type: text/html; charset=UTF-8');
        header('X-Cache: FILE-HIT');
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://code.jquery.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com data:; img-src 'self' data: blob: https:; connect-src 'self' https://www.google-analytics.com; frame-ancestors 'none'; base-uri 'self'; form-action 'self'; object-src 'none'");
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
