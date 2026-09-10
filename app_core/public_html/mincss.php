<?php
// Minify kegalle-frontend.css in-place
$cssDir = '/home/kegalle/public_html/css/';
$files  = [
    'kegalle-frontend.css',
    'kegalle-classified-premium.css',
    'kegalle-mobile-ux.css',
    'kegalle-events.css',
    'kegalle-utilities.css',
];

function minifyCss(string $css): string {
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    // Remove whitespace around selectors, rules, values
    $css = preg_replace('/\s*([{};:,>~+])\s*/', '$1', $css);
    // Remove leading/trailing whitespace per line
    $css = preg_replace('/^\s+|\s+$/m', '', $css);
    // Collapse multiple spaces/newlines
    $css = preg_replace('/\s{2,}/', ' ', $css);
    // Remove semicolon before closing brace
    $css = str_replace(';}', '}', $css);
    return trim($css);
}

$report = [];
foreach ($files as $f) {
    $path = $cssDir . $f;
    if (!file_exists($path)) { $report[] = "$f: NOT FOUND"; continue; }
    $before = filesize($path);
    $min    = minifyCss(file_get_contents($path));
    file_put_contents($path, $min);
    $after  = filesize($path);
    $report[] = "$f: " . number_format($before) . "B → " . number_format($after) . "B (saved " . round(($before-$after)/$before*100) . "%)";
}

foreach ($report as $r) echo $r . "\n";
unlink(__FILE__);
