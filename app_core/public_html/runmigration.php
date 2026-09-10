<?php
header('Content-Type: text/plain; charset=UTF-8');

$host = '127.0.0.1';
$port = 3306;
$db   = 'kegalle_kegalle';
$user = 'kegalle_fuzz';
$pass = 'YOUR_DB_PASS'; // will be set via env

// Read from Laravel .env
$env = file_get_contents('/home/kegalle/app_core/.env');
preg_match('/^DB_PASSWORD=(.+)$/m', $env, $m);
$pass = trim($m[1] ?? '');

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if columns already exist
    $cols = $pdo->query("SHOW COLUMNS FROM `listings`")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('poster_name', $cols)) {
        $pdo->exec("ALTER TABLE `listings` ADD COLUMN `poster_name` VARCHAR(120) NULL AFTER `user_id`");
        echo "Added poster_name\n";
    } else {
        echo "poster_name already exists\n";
    }

    if (!in_array('poster_phone', $cols)) {
        $pdo->exec("ALTER TABLE `listings` ADD COLUMN `poster_phone` VARCHAR(30) NULL AFTER `poster_name`");
        echo "Added poster_phone\n";
    } else {
        echo "poster_phone already exists\n";
    }

    // Make user_id nullable (drop FK first, alter, re-add FK)
    // Check current nullable status
    $col = $pdo->query("SHOW COLUMNS FROM `listings` WHERE Field='user_id'")->fetch(PDO::FETCH_ASSOC);
    if ($col && $col['Null'] === 'NO') {
        // Find FK constraint name
        $fks = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA='{$db}' AND TABLE_NAME='listings' AND COLUMN_NAME='user_id' AND REFERENCED_TABLE_NAME='users'")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($fks as $fk) {
            $pdo->exec("ALTER TABLE `listings` DROP FOREIGN KEY `{$fk}`");
            echo "Dropped FK: {$fk}\n";
        }

        $pdo->exec("ALTER TABLE `listings` MODIFY COLUMN `user_id` BIGINT UNSIGNED NULL");
        echo "Made user_id nullable\n";

        // Re-add FK with SET NULL on delete instead of CASCADE
        $pdo->exec("ALTER TABLE `listings` ADD CONSTRAINT `listings_user_id_foreign`
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL");
        echo "Re-added FK (SET NULL on delete)\n";
    } else {
        echo "user_id already nullable\n";
    }

    // Record migration in migrations table
    $exists = $pdo->query("SELECT id FROM `migrations` WHERE migration='2026_07_24_000001_add_poster_fields_to_listings'")->fetch();
    if (!$exists) {
        $batch = (int) $pdo->query("SELECT MAX(batch) FROM `migrations`")->fetchColumn();
        $stmt = $pdo->prepare("INSERT INTO `migrations` (migration, batch) VALUES (?, ?)");
        $stmt->execute(['2026_07_24_000001_add_poster_fields_to_listings', $batch + 1]);
        echo "Migration recorded in migrations table\n";
    } else {
        echo "Migration already recorded\n";
    }

    echo "\nDone.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

unlink(__FILE__);
