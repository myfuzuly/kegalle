<?php
// Parse .env manually (parse_ini_file fails on special chars)
$env = [];
foreach (file(__DIR__ . '/../app_core/.env') as $line) {
    $line = trim($line);
    if (!$line || $line[0] === '#' || strpos($line,'=') === false) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\n\r\"'");
}

$host = $env['MAIL_HOST']         ?? 'mail.kurulla.com';
$port = (int)($env['MAIL_PORT']   ?? 587);
$user = $env['MAIL_USERNAME']     ?? '';
$pass = $env['MAIL_PASSWORD']     ?? '';
$from = $env['MAIL_FROM_ADDRESS'] ?? $user;
$to   = 'fuzooly@gmail.com';

if (!$user || !$pass) { echo "No credentials in .env"; exit; }

function smtp_cmd($fp, $cmd, $expect) {
    if ($cmd !== null) fwrite($fp, $cmd . "\r\n");
    $resp = '';
    while ($line = fgets($fp, 512)) {
        $resp .= $line;
        if (preg_match('/^\d{3} /', $line)) break;
    }
    $code = (int)substr(trim($resp), 0, 3);
    echo ($code === $expect ? 'OK' : 'FAIL') . " [$code] " . trim($resp) . "\n";
    return $code === $expect;
}

echo "Host: $host:$port  User: $user\n\n";
$fp = @fsockopen($host, $port, $errno, $errstr, 10);
if (!$fp) { echo "CONNECT FAILED: $errstr ($errno)"; exit; }

smtp_cmd($fp, null, 220);
smtp_cmd($fp, "EHLO kurulla.com", 250);
smtp_cmd($fp, "STARTTLS", 220);
stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
smtp_cmd($fp, "EHLO kurulla.com", 250);
smtp_cmd($fp, "AUTH LOGIN", 334);
smtp_cmd($fp, base64_encode($user), 334);
$ok = smtp_cmd($fp, base64_encode($pass), 235);

if ($ok) {
    echo "\nAUTH OK — sending test email...\n";
    smtp_cmd($fp, "MAIL FROM:<$from>", 250);
    smtp_cmd($fp, "RCPT TO:<$to>", 250);
    smtp_cmd($fp, "DATA", 354);
    fwrite($fp, "From: kegalle <$from>\r\nTo: $to\r\nSubject: SMTP Test\r\n\r\nSMTP is working!\r\n.\r\n");
    smtp_cmd($fp, null, 250);
    smtp_cmd($fp, "QUIT", 221);
} else {
    echo "\nAUTH FAILED — 535 means wrong password or account missing in cPanel\n";
    smtp_cmd($fp, "QUIT", 221);
}
fclose($fp);
@unlink(__FILE__);
