<?php
// One-time script: adds FULLTEXT index to listings table for fast search
// Run once via browser, then delete (or it self-guards via lock file)

$lock = __DIR__ . '/setup_fulltext.lock';
if (file_exists($lock)) { die('Already run. Delete setup_fulltext.lock to re-run.'); }

// Bootstrap Laravel to get DB credentials
$base = dirname(__DIR__) . '/app_core';
require $base . '/vendor/autoload.php';
$app  = require_once $base . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$errors = [];

try {
    // Add FULLTEXT index (MySQL only — InnoDB supports FULLTEXT since 5.6)
    // Check if index already exists first
    $indexes = DB::select("SHOW INDEX FROM listings WHERE Key_name = 'listings_fulltext_search'");
    if (empty($indexes)) {
        DB::statement('ALTER TABLE listings ADD FULLTEXT INDEX listings_fulltext_search (title, description, location)');
        echo "✅ FULLTEXT index created on listings(title, description, location)<br>";
    } else {
        echo "ℹ️ FULLTEXT index already exists — no change needed<br>";
    }
} catch (\Throwable $e) {
    $errors[] = 'FULLTEXT index: ' . $e->getMessage();
    echo "❌ " . $e->getMessage() . "<br>";
}

if (empty($errors)) {
    file_put_contents($lock, date('Y-m-d H:i:s'));
    echo "<br><strong>Done.</strong> Lock file created. You can now delete this script.";
} else {
    echo "<br><strong>Completed with errors.</strong> Fix errors above and re-run.";
}
