<?php
$env = file_get_contents('/home/kegalle/app_core/.env');
function ev($k,$s){preg_match('/^'.preg_quote($k,'/').'=(.*)$/m',$s,$m);return trim($m[1]??'');}
$pdo = new PDO(
    "mysql:host=".ev('DB_HOST',$env).";dbname=".ev('DB_DATABASE',$env).";charset=utf8mb4",
    ev('DB_USERNAME',$env), ev('DB_PASSWORD',$env)
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "<pre>--- BEFORE ---\n";
foreach($pdo->query("SELECT status, COUNT(*) c FROM listings GROUP BY status ORDER BY c DESC")->fetchAll(PDO::FETCH_ASSOC) as $r)
    echo "  listings {$r['status']}: {$r['c']}\n";

// Normalise all live-ish statuses to 'approved'
$n = $pdo->exec("UPDATE listings SET status='approved' WHERE status IN ('published','active','available','live')");
echo "Normalised $n listing rows to 'approved'\n";

foreach($pdo->query("SELECT status, COUNT(*) c FROM listings GROUP BY status ORDER BY c DESC")->fetchAll(PDO::FETCH_ASSOC) as $r)
    echo "  listings {$r['status']}: {$r['c']}\n";

// Clear Laravel file cache
$cacheDir = '/home/kegalle/app_core/storage/framework/cache/data';
$cleared = 0;
if(is_dir($cacheDir)){
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS));
    foreach($it as $f){ if($f->isFile() && $f->getExtension()===''){@unlink($f->getPathname()); $cleared++;} }
}
echo "Cleared $cleared cache files\n";

@unlink(__FILE__);
echo "</pre>Done.";
