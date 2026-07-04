<?php
/**
 * Create deals table. Upload to public_html/, run once, then DELETE.
 */
$lockFile = __DIR__ . '/create_deals_table.lock';
$guard = 'K3GALL3_DEALS_2026';

if (($_GET['key'] ?? '') !== $guard) { http_response_code(403); die('Forbidden'); }
if (file_exists($lockFile)) { die('Already executed.'); }

// Try multiple paths to find .env
$envPaths = [
    dirname(__DIR__) . '/app_core/.env',
    __DIR__ . '/../app_core/.env',
    dirname(__DIR__, 1) . '/app_core/.env',
    __DIR__ . '/../../app_core/.env',
];

$env = null;
foreach ($envPaths as $envPath) {
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            if (str_contains($line, '=')) {
                [$k, $v] = explode('=', $line, 2);
                $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
            }
        }
        break;
    }
}

if (!$env) {
    // Try config file
    $configPaths = [
        dirname(__DIR__) . '/app_core/config/database.php',
        __DIR__ . '/../app_core/config/database.php',
    ];
    foreach ($configPaths as $cp) {
        if (file_exists($cp)) {
            $config = include $cp;
            $db = $config['connections']['mysql'] ?? [];
            $env = [
                'DB_HOST' => $db['host'] ?? '127.0.0.1',
                'DB_PORT' => $db['port'] ?? 3306,
                'DB_DATABASE' => $db['database'] ?? '',
                'DB_USERNAME' => $db['username'] ?? '',
                'DB_PASSWORD' => $db['password'] ?? '',
            ];
            break;
        }
    }
}

if (!$env) { die('Cannot find .env or config. Tried: ' . implode(', ', $envPaths)); }

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? 3306;
$dbname = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('DB connection failed: ' . $e->getMessage());
}

echo "<pre>Creating deals table...\n\n";

$sql = "
CREATE TABLE IF NOT EXISTS `deals` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `listing_id` BIGINT UNSIGNED NOT NULL,
    `store_id` BIGINT UNSIGNED NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `deal_price` DECIMAL(12,2) NOT NULL,
    `original_price` DECIMAL(12,2) NOT NULL,
    `discount_percent` DECIMAL(5,2) NOT NULL DEFAULT 0,
    `starts_at` DATETIME NOT NULL,
    `ends_at` DATETIME NOT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'pending' COMMENT 'pending,approved,rejected,expired',
    `is_flash` TINYINT(1) NOT NULL DEFAULT 0,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `admin_note` TEXT NULL,
    `sold_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `stock_qty` INT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_deals_status` (`status`),
    INDEX `idx_deals_listing` (`listing_id`),
    INDEX `idx_deals_store` (`store_id`),
    INDEX `idx_deals_dates` (`starts_at`, `ends_at`),
    INDEX `idx_deals_flash` (`is_flash`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

$pdo->exec($sql);
echo "✓ deals table created\n";

// Add deals_count to stores for quick display (optional)
try {
    $pdo->exec("SELECT deals_count FROM stores LIMIT 0");
    echo "→ stores.deals_count already exists\n";
} catch (PDOException $e) {
    // Column doesn't exist, skip (not critical)
    echo "→ stores.deals_count skipped (optional)\n";
}

file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo "\n✓ Done!\n\n⚠️ DELETE create_deals_table.php and create_deals_table.lock now!\n</pre>";
