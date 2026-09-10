<?php
header('Content-Type: text/plain; charset=UTF-8');
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=kegalle_kegalle', 'kegalle_fuzz', 'eMRXlTWeBjcu');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Product 92 info
$s = $pdo->query("SELECT id, title, user_id, store_id, status FROM listings WHERE id IN (92,93) ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
echo "=== Products ===\n";
foreach($s as $r) { echo json_encode($r)."\n"; }

// Store 49, 50 info
$s2 = $pdo->query("SELECT id, name, user_id FROM stores WHERE id IN (49,50)")->fetchAll(PDO::FETCH_ASSOC);
echo "\n=== Stores ===\n";
foreach($s2 as $r) { echo json_encode($r)."\n"; }

// All stores for user who owns store 49
$s3 = $pdo->query("SELECT user_id FROM stores WHERE id=49")->fetch(PDO::FETCH_ASSOC);
if($s3) {
    $uid = $s3['user_id'];
    echo "\n=== All stores for user $uid ===\n";
    $s4 = $pdo->query("SELECT id, name FROM stores WHERE user_id=$uid")->fetchAll(PDO::FETCH_ASSOC);
    foreach($s4 as $r) { echo json_encode($r)."\n"; }
}
unlink(__FILE__);
