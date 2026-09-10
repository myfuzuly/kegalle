<?php
/**
 * File Integrity Monitor — kegalle.com
 * Run via cPanel cron: php /home/USERNAME/app_core/integrity_monitor.php
 */

$ALERT_EMAIL = 'fuzooly@gmail.com';
$SITE        = 'kegalle.com';

// Known-good MD5 hashes (recorded clean state)
$KNOWN = [
    '/public_html/index.php'                                          => '390b39d93d045104af6ab00d248ee382',
    '/public_html/js/kegalle-main.js'                                 => '3835420b1aabad2d6c9a098c98cce63a',
    '/public_html/js/quill.min.js'                                    => '929349222da793a2128c4d55bebc2adc',
    '/public_html/js/category-fields.js'                              => 'cab1631b0ec48526bab8125dac08554d',
    '/app_core/resources/views/layouts/app.blade.php'                 => 'dbda7ba767bff8cf3c86e694d8eddb5c',
    '/app_core/app/Http/Middleware/SecurityHeaders.php'               => '5c4af95bc67f1475aa0b38a3e4aed49b',
];

// Resolve home directory from this script's location
// This script lives at /home/USER/app_core/integrity_monitor.php
$home = dirname(__DIR__); // one level up from app_core/

$changed = [];

foreach ($KNOWN as $rel => $expectedHash) {
    $full = $home . $rel;
    if (!file_exists($full)) {
        $changed[] = "MISSING: $rel";
        continue;
    }
    $actual = md5_file($full);
    if ($actual !== $expectedHash) {
        $changed[] = "CHANGED: $rel\n  Expected: $expectedHash\n  Actual:   $actual";
    }
}

if (empty($changed)) {
    // All clean — silent exit
    exit(0);
}

// Something changed — send alert
$body  = "SECURITY ALERT — File tampering detected on $SITE\n";
$body .= "Time: " . date('Y-m-d H:i:s T') . "\n\n";
$body .= "The following files have been modified:\n\n";
foreach ($changed as $line) {
    $body .= "  • $line\n";
}
$body .= "\nCheck your server immediately and restore from backup if needed.\n";
$body .= "\n-- kegalle Integrity Monitor";

$subject = "⚠️ ALERT: File tampering detected on $SITE";
$headers = "From: monitor@$SITE\r\nX-Mailer: KurillaMonitor/1.0";

mail($ALERT_EMAIL, $subject, $body, $headers);

// Also write to a local log
$log = dirname(__FILE__) . '/integrity_alerts.log';
file_put_contents($log, date('Y-m-d H:i:s') . "\n" . implode("\n", $changed) . "\n\n", FILE_APPEND);

exit(1);
