<?php
$envPath = '/home/kegalle/app_core/.env';
$env = file_get_contents($envPath);

$patches = [
    'HUTCH_SMS_URL'       => 'https://bsms.hutch.lk/api/sendsms',
    'HUTCH_SMS_USERNAME'  => 'fuzuly@kainglobe.com',
    'HUTCH_SMS_PASSWORD'  => 'vi&&63FH',
    'HUTCH_SMS_SENDER_ID' => 'Kainglobe',
];

foreach ($patches as $key => $value) {
    if (preg_match('/^'.preg_quote($key,'/').'=.*/m', $env)) {
        $env = preg_replace('/^'.preg_quote($key,'/').'=.*/m', $key.'='.$value, $env);
        echo "Updated: $key\n";
    } else {
        $env .= "\n$key=$value";
        echo "Added: $key\n";
    }
}

file_put_contents($envPath, $env);

// Clear Laravel config + cache
$cacheDir = '/home/kegalle/app_core/storage/framework/cache/data';
$cleared = 0;
if (is_dir($cacheDir)) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS)) as $f) {
        if ($f->isFile()) { @unlink($f->getPathname()); $cleared++; }
    }
}
echo "Cleared $cleared cache files\n";

// Also clear bootstrap/cache
foreach (glob('/home/kegalle/app_core/bootstrap/cache/*.php') as $f) {
    @unlink($f);
    echo "Cleared bootstrap cache: ".basename($f)."\n";
}

echo "\n--- Verify ---\n";
$env2 = file_get_contents($envPath);
foreach (array_keys($patches) as $key) {
    preg_match('/^'.preg_quote($key,'/').'=(.*)$/m', $env2, $m);
    $val = trim($m[1] ?? '(not found)');
    // Mask password
    if (str_contains($key,'PASSWORD')) $val = str_repeat('*', strlen($val));
    echo "$key=$val\n";
}

@unlink(__FILE__);
echo "\nDone.";
