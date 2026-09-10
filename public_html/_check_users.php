<?php
$env = file_get_contents('/home/kegalle/app_core/.env');
function ev($k,$s){preg_match('/^'.preg_quote($k,'/').'=(.*)$/m',$s,$m);return trim($m[1]??'');}
$pdo = new PDO(
    "mysql:host=".ev('DB_HOST',$env).";dbname=".ev('DB_DATABASE',$env).";charset=utf8mb4",
    ev('DB_USERNAME',$env), ev('DB_PASSWORD',$env)
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "<pre>";
echo "=== USERS (no passwords) ===\n";
foreach($pdo->query("SELECT id,name,email,role,status,email_verified_at,account_type FROM users ORDER BY id")->fetchAll(PDO::FETCH_ASSOC) as $r)
    echo "#{$r['id']} [{$r['role']}|{$r['account_type']}] {$r['name']} <{$r['email']}> status={$r['status']} verified=".($r['email_verified_at']?'YES':'NO')."\n";

// Fix: mark all existing users as email-verified
$n = $pdo->exec("UPDATE users SET email_verified_at=NOW() WHERE email_verified_at IS NULL");
echo "\nFixed $n unverified users — set email_verified_at=NOW()\n";

// Also make sure admin user exists / has right role
$admin = $pdo->query("SELECT id,email,role FROM users WHERE role='admin' OR role='super_admin' ORDER BY id LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
echo "\nAdmin users:\n";
foreach($admin as $a) echo "  #{$a['id']} {$a['email']} role={$a['role']}\n";

@unlink(__FILE__);
echo "</pre>Done.";
