<?php
// Read .env for current config
$env = [];
foreach (file(__DIR__.'/../app_core/.env', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line),'#') || !str_contains($line,'=')) continue;
    [$k,$v] = explode('=',$line,2);
    $env[trim($k)] = trim($v," \t\n\r\0\x0B\"'");
}

echo "<pre>";
echo "=== Current .env MAIL settings ===\n";
foreach (['MAIL_MAILER','MAIL_HOST','MAIL_PORT','MAIL_USERNAME','MAIL_PASSWORD','MAIL_ENCRYPTION','MAIL_FROM_ADDRESS'] as $k) {
    $v = $env[$k] ?? '(not set)';
    if ($k === 'MAIL_PASSWORD') $v = $v === '(not set)' ? '(not set)' : '(set, '.strlen($v).' chars)';
    echo "$k = $v\n";
}

echo "\n=== Testing SMTP connection ===\n";
$host = 'mail.kurulla.com';
$port = 587;
$user = 'no-reply@kurulla.com';
$pass = '1vDPwMqjGHNh';

// Test TCP connection first
$errno = $errstr = null;
$sock = @fsockopen($host, $port, $errno, $errstr, 10);
if (!$sock) {
    echo "❌ Cannot connect to $host:$port — $errstr ($errno)\n";
    echo "</pre>"; @unlink(__FILE__); exit;
}
echo "✅ TCP connection to $host:$port OK\n";

// Read greeting
$line = fgets($sock, 1024);
echo "Server: ".trim($line)."\n";

// EHLO
fputs($sock, "EHLO kurulla.com\r\n");
$resp = '';
while ($r = fgets($sock, 1024)) { $resp .= $r; if ($r[3] === ' ') break; }
echo "EHLO response:\n$resp\n";

// STARTTLS
fputs($sock, "STARTTLS\r\n");
$tls = fgets($sock, 1024);
echo "STARTTLS: ".trim($tls)."\n";

if (strpos($tls, '220') !== false) {
    // Upgrade to TLS
    stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
    echo "✅ TLS handshake OK\n";

    // Re-EHLO after TLS
    fputs($sock, "EHLO kurulla.com\r\n");
    $resp2 = '';
    while ($r = fgets($sock, 1024)) { $resp2 .= $r; if ($r[3] === ' ') break; }
    echo "Post-TLS EHLO:\n$resp2\n";

    // AUTH LOGIN
    fputs($sock, "AUTH LOGIN\r\n");
    $a1 = fgets($sock, 1024);
    echo "AUTH LOGIN: ".trim($a1)."\n";

    fputs($sock, base64_encode($user)."\r\n");
    $a2 = fgets($sock, 1024);
    echo "Username response: ".trim($a2)."\n";

    fputs($sock, base64_encode($pass)."\r\n");
    $a3 = fgets($sock, 1024);
    echo "Password response: ".trim($a3)."\n";

    if (strpos($a3, '235') !== false) {
        echo "\n✅ SMTP AUTH SUCCESS — email will work!\n";
    } else {
        echo "\n❌ SMTP AUTH FAILED\n";
    }
} else {
    echo "❌ STARTTLS not accepted\n";
}

fclose($sock);
echo "</pre>";
@unlink(__FILE__);
