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

// Ensure column exists
$cols = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'phone_verified_at'")->fetchAll();
if (empty($cols)) {
    $pdo->exec("ALTER TABLE `users` ADD COLUMN `phone_verified_at` TIMESTAMP NULL DEFAULT NULL AFTER `phone`");
    echo "✅ Added phone_verified_at column\n";
} else {
    echo "✓ phone_verified_at column exists\n";
}

// Ensure phone_otps table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `phone_otps` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `phone` VARCHAR(30) NOT NULL,
    `code` VARCHAR(10) NOT NULL,
    `expires_at` TIMESTAMP NOT NULL,
    `used` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
echo "✅ phone_otps table ready\n";

// Show current phone verify status for all users
echo "\nUser phone verify status:\n";
$users = $pdo->query("SELECT id, name, phone, phone_verified_at FROM users ORDER BY id DESC LIMIT 15")->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $u) {
    echo "ID:{$u['id']} | {$u['name']} | phone=".($u['phone']?:'(empty)')." | verified=".($u['phone_verified_at']?:'NO')."\n";
}
echo "</pre>";
@unlink(__FILE__);
