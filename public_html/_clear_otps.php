<?php
$env = [];
foreach (file(__DIR__.'/../app_core/.env', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line),'#') || !str_contains($line,'=')) continue;
    [$k,$v] = explode('=',$line,2);
    $env[trim($k)] = trim($v," \t\n\r\0\x0B\"'");
}
$pdo = new PDO("mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4",
    $env['DB_USERNAME'], $env['DB_PASSWORD'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$deleted = $pdo->exec("DELETE FROM phone_otps WHERE user_id=21");
echo "<pre>✅ Cleared $deleted OTP record(s) for user 21. Try /phone/verify now.</pre>";
@unlink(__FILE__);
