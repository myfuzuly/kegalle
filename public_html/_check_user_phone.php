<?php
$env = [];
foreach (file(__DIR__.'/../app_core/.env', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line),'#') || !str_contains($line,'=')) continue;
    [$k,$v] = explode('=',$line,2);
    $env[trim($k)] = trim($v," \t\n\r\0\x0B\"'");
}
$pdo = new PDO("mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4",
    $env['DB_USERNAME'], $env['DB_PASSWORD'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

echo "<pre>";
$users = $pdo->query("SELECT id, name, email, phone, phone_verified_at FROM users ORDER BY id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $u) {
    echo "ID:{$u['id']} | {$u['name']} | {$u['email']} | phone=".($u['phone']?:'(empty)')." | verified=".($u['phone_verified_at']?:'no')."\n";
}
echo "</pre>";
@unlink(__FILE__);
