<?php
$env  = parse_ini_file('/home/kegalle/app_core/.env');
$dir  = '/home/kegalle/backups';
if (!is_dir($dir)) { mkdir($dir, 0750, true); }
$file = $dir . '/kegalle-' . date('Y-m-d') . '.sql.gz';
$cmd  = sprintf(
    'mysqldump --host=%s --port=%s -u%s -p%s %s 2>/dev/null | gzip > %s',
    escapeshellarg($env['DB_HOST'] ?? '127.0.0.1'),
    escapeshellarg($env['DB_PORT'] ?? '3306'),
    escapeshellarg($env['DB_USERNAME'] ?? ''),
    escapeshellarg($env['DB_PASSWORD'] ?? ''),
    escapeshellarg($env['DB_DATABASE'] ?? ''),
    escapeshellarg($file)
);
shell_exec($cmd);
if (file_exists($file) && filesize($file) > 0) {
    echo "OK: " . round(filesize($file)/1048576, 2) . " MB — " . basename($file) . "\n";
} else {
    echo "FAILED — check DB creds or mysqldump path\n";
    echo "DB_HOST=" . ($env['DB_HOST'] ?? 'missing') . "\n";
    echo "DB_DATABASE=" . ($env['DB_DATABASE'] ?? 'missing') . "\n";
    echo "DB_USERNAME=" . ($env['DB_USERNAME'] ?? 'missing') . "\n";
}
unlink(__FILE__);
