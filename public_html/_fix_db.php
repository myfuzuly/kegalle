<?php
// One-time DB fixes (continued). DELETE THIS FILE after running.

$env = [];
foreach (file(__DIR__ . '/../app_core/.env') as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
}

$dsn = 'mysql:host=' . $env['DB_HOST'] . ';port=' . ($env['DB_PORT'] ?? 3306) . ';dbname=' . $env['DB_DATABASE'];
$pdo = new PDO($dsn, $env['DB_USERNAME'], $env['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// Fix 3: Index on listings.status + created_at (prefix on status to stay under key length limit)
$idx = $pdo->query("SHOW INDEX FROM listings WHERE Key_name = 'listings_status_created_at_index'")->fetchAll();
if (empty($idx)) {
    $pdo->exec("ALTER TABLE listings ADD INDEX listings_status_created_at_index (status(20), created_at)");
    echo "Added index: listings(status(20), created_at)<br>";
} else {
    echo "Already exists: listings_status_created_at_index<br>";
}

// Fix 4: Index on listings.is_featured + status
$idx2 = $pdo->query("SHOW INDEX FROM listings WHERE Key_name = 'listings_featured_status_index'")->fetchAll();
if (empty($idx2)) {
    $pdo->exec("ALTER TABLE listings ADD INDEX listings_featured_status_index (is_featured, status(20))");
    echo "Added index: listings(is_featured, status(20))<br>";
} else {
    echo "Already exists: listings_featured_status_index<br>";
}

echo "<br><strong>Done. Delete this file now.</strong>";
