<?php
// Check actual URL seen by PHP and whether page cache is working
require '/home/kegalle/app_core/vendor/autoload.php';
$app = require '/home/kegalle/app_core/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
// Simulate a GET / request
$request = \Illuminate\Http\Request::create('http://kurulla.com/', 'GET');
$request->headers->set('Host', 'kurulla.com');
$fullUrl = $request->fullUrl();
$key = 'pagecache_' . sha1($fullUrl);
echo "Simulated fullUrl: $fullUrl\n";
echo "Key would be: $key\n";

// Check all pagecache_* keys in DB cache table
$rows = \Illuminate\Support\Facades\DB::table('cache')
    ->where('key', 'like', '%pagecache%')
    ->select('key', \Illuminate\Support\Facades\DB::raw('LENGTH(value) as vlen'), 'expiration')
    ->get();
echo "pagecache keys in DB: " . $rows->count() . "\n";
foreach ($rows as $r) {
    echo "  key={$r->key} size={$r->vlen} exp=" . date('H:i:s', $r->expiration) . "\n";
}
unlink(__FILE__);
