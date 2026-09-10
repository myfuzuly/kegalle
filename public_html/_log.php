<?php
$log = __DIR__ . '/../app_core/storage/logs/laravel.log';
$lines = file($log);
// Show last 200 lines, filter for Hutch or show all if none found
$last200 = array_slice($lines, -200);
$hutch = array_filter($last200, fn($l) => stripos($l, 'hutch') !== false || stripos($l, 'sendsms') !== false || stripos($l, 'otp') !== false);
echo "<pre style='font-size:12px;white-space:pre-wrap'>";
if ($hutch) {
    echo "=== HUTCH/OTP LOG ENTRIES ===\n";
    echo htmlspecialchars(implode('', $hutch));
} else {
    echo "=== NO HUTCH ENTRIES FOUND — LAST 200 LINES ===\n";
    echo htmlspecialchars(implode('', $last200));
}
echo "</pre>";
