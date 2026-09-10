<?php
header('Content-Type: text/plain; charset=UTF-8');
$env = file_get_contents('/home/kegalle/app_core/.env');
preg_match('/^DB_PASSWORD=(.+)$/m', $env, $m); $pass = trim($m[1] ?? '');
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=kegalle_kegalle;charset=utf8mb4', 'kegalle_fuzz', $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $exists = $pdo->query("SHOW TABLES LIKE 'hero_slides'")->rowCount();
    if (!$exists) {
        $pdo->exec("CREATE TABLE `hero_slides` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `image` varchar(255) NOT NULL,
            `title` varchar(120) DEFAULT NULL,
            `sort_order` int NOT NULL DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "Created hero_slides table\n";

        $now = date('Y-m-d H:i:s');
        $pdo->exec("INSERT INTO `hero_slides` (image,title,sort_order,is_active,created_at,updated_at) VALUES
            ('/images/kegalle-town.png','Kegalle Town',1,1,'{$now}','{$now}'),
            ('/images/KEGALLE-TOWER.webp','Kegalle Clock Tower',2,1,'{$now}','{$now}')");
        echo "Seeded 2 slides\n";

        $batch = (int) $pdo->query("SELECT MAX(batch) FROM `migrations`")->fetchColumn();
        $pdo->prepare("INSERT INTO `migrations` (migration,batch) VALUES (?,?)")
            ->execute(['2026_08_22_000001_create_hero_slides_table', $batch + 1]);
        echo "Recorded in migrations table\n";
    } else {
        echo "hero_slides table already exists\n";
    }
    echo "\nDone.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
unlink(__FILE__);
