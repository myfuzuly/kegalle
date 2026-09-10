<?php
// Run 2026-08-21 migrations manually. DELETE after running.

$env = [];
foreach (file(__DIR__ . '/../app_core/.env') as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
}

$dsn = 'mysql:host=' . $env['DB_HOST'] . ';port=' . ($env['DB_PORT'] ?? 3306) . ';dbname=' . $env['DB_DATABASE'];
$pdo = new PDO($dsn, $env['DB_USERNAME'], $env['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

echo "<pre>\n";

// ── 1. FULLTEXT index on listings ──────────────────────────────────────────
$ft = $pdo->query("SHOW INDEX FROM listings WHERE Key_name = 'ft_listings_search'")->fetchAll();
if (empty($ft)) {
    $pdo->exec('ALTER TABLE listings ADD FULLTEXT INDEX ft_listings_search (title, description)');
    echo "OK: FULLTEXT index ft_listings_search added\n";
} else {
    echo "SKIP: ft_listings_search already exists\n";
}

$composites = [
    'idx_listings_status_cat_date'   => 'ADD INDEX idx_listings_status_cat_date (status, category_id, created_at)',
    'idx_listings_status_store_date' => 'ADD INDEX idx_listings_status_store_date (status, store_id, created_at)',
    'idx_listings_status_town_date'  => 'ADD INDEX idx_listings_status_town_date (status, town, created_at)',
];
foreach ($composites as $name => $sql) {
    $idx = $pdo->query("SHOW INDEX FROM listings WHERE Key_name = '$name'")->fetchAll();
    if (empty($idx)) {
        $pdo->exec("ALTER TABLE listings $sql");
        echo "OK: $name added\n";
    } else {
        echo "SKIP: $name already exists\n";
    }
}

// ── 2. jobs table ──────────────────────────────────────────────────────────
$tables = $pdo->query("SHOW TABLES LIKE 'jobs'")->fetchAll();
if (empty($tables)) {
    $pdo->exec("CREATE TABLE jobs (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        queue VARCHAR(255) NOT NULL,
        payload LONGTEXT NOT NULL,
        attempts TINYINT UNSIGNED NOT NULL,
        reserved_at INT UNSIGNED NULL,
        available_at INT UNSIGNED NOT NULL,
        created_at INT UNSIGNED NOT NULL,
        INDEX jobs_queue_index (queue)
    )");
    echo "OK: jobs table created\n";
} else {
    echo "SKIP: jobs table already exists\n";
}

// ── 3. failed_jobs table ───────────────────────────────────────────────────
$tables = $pdo->query("SHOW TABLES LIKE 'failed_jobs'")->fetchAll();
if (empty($tables)) {
    $pdo->exec("CREATE TABLE failed_jobs (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        uuid VARCHAR(255) NOT NULL UNIQUE,
        connection TEXT NOT NULL,
        queue TEXT NOT NULL,
        payload LONGTEXT NOT NULL,
        exception LONGTEXT NOT NULL,
        failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");
    echo "OK: failed_jobs table created\n";
} else {
    echo "SKIP: failed_jobs table already exists\n";
}

// ── 4. job_batches table ───────────────────────────────────────────────────
$tables = $pdo->query("SHOW TABLES LIKE 'job_batches'")->fetchAll();
if (empty($tables)) {
    $pdo->exec("CREATE TABLE job_batches (
        id VARCHAR(255) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        total_jobs INT NOT NULL,
        pending_jobs INT NOT NULL,
        failed_jobs INT NOT NULL,
        failed_job_ids LONGTEXT NOT NULL,
        options MEDIUMTEXT NULL,
        cancelled_at INT NULL,
        created_at INT NOT NULL,
        finished_at INT NULL
    )");
    echo "OK: job_batches table created\n";
} else {
    echo "SKIP: job_batches table already exists\n";
}

echo "\nAll done.\n</pre>";
