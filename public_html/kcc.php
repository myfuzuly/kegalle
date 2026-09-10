<?php
// Cache clear — single-use admin tool
if (($_GET['t'] ?? '') !== 'kegalle2024') { http_response_code(403); exit; }
$dir = __DIR__ . '/../app_core/storage/framework/pagecache';
$n = 0;
if (is_dir($dir)) {
    foreach (glob($dir . '/*.{html,lock}', GLOB_BRACE) as $f) { @unlink($f); $n++; }
}
echo "OK — cleared $n cache files. <a href='/'>Go to site</a>";
