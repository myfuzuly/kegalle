<?php
function req($url, $cookies = '') {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => ['Host: kurulla.com'],
        CURLOPT_HEADER         => true,
        CURLOPT_COOKIE         => $cookies,
    ]);
    curl_exec($ch);
    $t = round(curl_getinfo($ch, CURLINFO_STARTTRANSFER_TIME) * 1000);
    curl_close($ch);
    return $t;
}
$url = 'https://127.0.0.1:2083/';
echo "=== No cookies (FILE-HIT fast path, no Laravel) ===\n";
for ($i=1;$i<=4;$i++) echo "Run $i: " . req($url) . "ms\n";
echo "\n=== With cookie (middleware path, Laravel boots) ===\n";
for ($i=1;$i<=4;$i++) echo "Run $i: " . req($url,'laravel_session=abc') . "ms\n";
unlink(__FILE__);
