<?php
// One-time migration: add reply columns to reviews table.
// DELETE THIS FILE after running.

$env = [];
foreach (file(__DIR__ . '/../app_core/.env') as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
}

$dsn  = 'mysql:host=' . $env['DB_HOST'] . ';port=' . ($env['DB_PORT'] ?? 3306) . ';dbname=' . $env['DB_DATABASE'];
$pdo  = new PDO($dsn, $env['DB_USERNAME'], $env['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$cols = $pdo->query("SHOW COLUMNS FROM reviews")->fetchAll(PDO::FETCH_COLUMN);

if (!in_array('reply', $cols)) {
    $pdo->exec("ALTER TABLE reviews ADD COLUMN reply TEXT NULL AFTER comment");
    echo "Added: reply<br>";
} else {
    echo "Already exists: reply<br>";
}

if (!in_array('replied_at', $cols)) {
    $pdo->exec("ALTER TABLE reviews ADD COLUMN replied_at TIMESTAMP NULL AFTER reply");
    echo "Added: replied_at<br>";
} else {
    echo "Already exists: replied_at<br>";
}

echo "Done. Delete this file now.";
