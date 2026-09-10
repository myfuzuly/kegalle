<?php
// Quick DB state check — delete after use
function readEnv($path) {
    $vars = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $vars[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
    }
    return $vars;
}
$env = readEnv(dirname(__DIR__).'/app_core/.env');
$pdo = new PDO("mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4",
    $env['DB_USERNAME'], $env['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

echo "<pre>";

// Categories
$cats = $pdo->query("SELECT id, parent_id, name, slug FROM categories WHERE is_active=1 ORDER BY parent_id, sort_order, name")->fetchAll(PDO::FETCH_ASSOC);
echo "=== CATEGORIES (".count($cats)." total) ===\n";
foreach ($cats as $c) {
    $pad = $c['parent_id'] ? "  └─ " : "";
    echo $pad . "id={$c['id']} parent={$c['parent_id']} [{$c['slug']}] {$c['name']}\n";
}

// Brands
echo "\n=== BRANDS TABLE ===\n";
try {
    $brands = $pdo->query("SELECT id, name, category_group FROM brands LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
    echo count($brands) > 0 ? implode(', ', array_column($brands,'name'))."\n" : "EMPTY\n";
} catch(Exception $e) { echo "TABLE NOT FOUND\n"; }

// Custom fields
echo "\n=== CUSTOM FIELDS ===\n";
try {
    $fields = $pdo->query("SELECT id, name, label, type FROM custom_fields LIMIT 30")->fetchAll(PDO::FETCH_ASSOC);
    echo count($fields) > 0 ? print_r($fields, true) : "EMPTY\n";
} catch(Exception $e) { echo "TABLE NOT FOUND\n"; }

// Category custom field pivot
echo "\n=== CATEGORY_CUSTOM_FIELD PIVOT ===\n";
try {
    $rows = $pdo->query("SELECT COUNT(*) as cnt FROM category_custom_field")->fetch();
    echo "Rows: ".$rows['cnt']."\n";
} catch(Exception $e) { echo "TABLE NOT FOUND\n"; }

// Blog posts
echo "\n=== BLOG POSTS ===\n";
try {
    $posts = $pdo->query("SELECT id, title, slug FROM posts ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($posts as $p) echo "id={$p['id']} [{$p['slug']}] {$p['title']}\n";
} catch(Exception $e) { echo "TABLE NOT FOUND: ".$e->getMessage()."\n"; }

echo "</pre>";
