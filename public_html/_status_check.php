<?php
$env = file_get_contents('/home/kegalle/app_core/.env');
function ev($k,$s){preg_match('/^'.preg_quote($k,'/').'=(.*)$/m',$s,$m);return trim($m[1]??'');}
$pdo = new PDO(
    "mysql:host=".ev('DB_HOST',$env).";dbname=".ev('DB_DATABASE',$env).";charset=utf8mb4",
    ev('DB_USERNAME',$env), ev('DB_PASSWORD',$env)
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "<pre>--- LISTING STATUSES ---\n";
foreach($pdo->query("SELECT status, COUNT(*) c FROM listings GROUP BY status ORDER BY c DESC")->fetchAll(PDO::FETCH_ASSOC) as $r)
    echo "  {$r['status']}: {$r['c']}\n";

echo "\n--- STORE STATUSES ---\n";
foreach($pdo->query("SELECT status, COUNT(*) c FROM stores GROUP BY status ORDER BY c DESC")->fetchAll(PDO::FETCH_ASSOC) as $r)
    echo "  {$r['status']}: {$r['c']}\n";

echo "\n--- FEATURED LISTINGS (any status) ---\n";
foreach($pdo->query("SELECT status, COUNT(*) c FROM listings WHERE is_featured=1 GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) as $r)
    echo "  is_featured + {$r['status']}: {$r['c']}\n";

echo "\n--- FEATURED STORES (any status) ---\n";
foreach($pdo->query("SELECT status, COUNT(*) c FROM stores WHERE is_featured=1 GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) as $r)
    echo "  is_featured + {$r['status']}: {$r['c']}\n";

echo "\n--- USERS TABLE (first 5, no passwords) ---\n";
foreach($pdo->query("SELECT id, name, email, created_at FROM users LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) as $r)
    echo "  #{$r['id']} {$r['name']} <{$r['email']}> joined {$r['created_at']}\n";

@unlink(__FILE__);
echo "</pre>Done.";
