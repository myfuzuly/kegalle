<?php
header('Content-Type: text/plain; charset=UTF-8');
// Read DB_PASSWORD from .env manually
$pass = '';
foreach(file('/home/kegalle/app_core/.env') as $line) {
    if(str_starts_with(trim($line),'DB_PASSWORD=')) {
        $pass = trim(substr(trim($line), 12), '"\'');
        break;
    }
}
echo "pass_len=".strlen($pass)."\n";
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=kegalle_kegalle", 'kegalle_fuzz', $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "=== Listings 92 & 93 ===\n";
    foreach($pdo->query("SELECT id,title,user_id,store_id,status FROM listings WHERE id IN (92,93) ORDER BY id")->fetchAll(PDO::FETCH_ASSOC) as $r) echo json_encode($r)."\n";
    echo "\n=== Stores 49 & 50 ===\n";
    foreach($pdo->query("SELECT id,name,user_id FROM stores WHERE id IN (49,50)")->fetchAll(PDO::FETCH_ASSOC) as $r) echo json_encode($r)."\n";
} catch(Exception $e) { echo "ERR: ".$e->getMessage(); }
unlink(__FILE__);
