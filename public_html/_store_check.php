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
$rows = $pdo->query("
    SELECT s.id, s.name, s.status, s.user_id, u.phone_verified_at
    FROM stores s
    JOIN users u ON u.id = s.user_id
    ORDER BY s.id DESC LIMIT 15
")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "Store:{$r['id']} | {$r['name']} | status={$r['status']} | user_id={$r['user_id']} | phone_verified=".($r['phone_verified_at']?:'NO')."\n";
}
echo "</pre>";
@unlink(__FILE__);
