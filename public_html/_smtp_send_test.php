<?php
$env = [];
foreach (file(__DIR__.'/../app_core/.env', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line),'#') || !str_contains($line,'=')) continue;
    [$k,$v] = explode('=',$line,2);
    $env[trim($k)] = trim($v," \t\n\r\0\x0B\"'");
}

$host = 'mail.kurulla.com';
$port = 587;
$user = 'no-reply@kurulla.com';
$pass = '1vDPwMqjGHNh';
$to   = 'fuzooly@gmail.com';
$from = 'no-reply@kurulla.com';

echo "<pre>";

$sock = fsockopen($host, $port, $errno, $errstr, 10);
fgets($sock, 1024);
fputs($sock, "EHLO kurulla.com\r\n");
while ($r = fgets($sock, 1024)) { if ($r[3] === ' ') break; }
fputs($sock, "STARTTLS\r\n");
fgets($sock, 1024);
stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
fputs($sock, "EHLO kurulla.com\r\n");
while ($r = fgets($sock, 1024)) { if ($r[3] === ' ') break; }
fputs($sock, "AUTH LOGIN\r\n"); fgets($sock, 1024);
fputs($sock, base64_encode($user)."\r\n"); fgets($sock, 1024);
fputs($sock, base64_encode($pass)."\r\n");
$auth = fgets($sock, 1024);
echo "Auth: ".trim($auth)."\n";

fputs($sock, "MAIL FROM:<$from>\r\n"); echo "MAIL FROM: ".trim(fgets($sock,1024))."\n";
fputs($sock, "RCPT TO:<$to>\r\n");    echo "RCPT TO:   ".trim(fgets($sock,1024))."\n";
fputs($sock, "DATA\r\n");             fgets($sock, 1024);

$msg  = "From: kegalle Marketplace <$from>\r\n";
$msg .= "To: $to\r\n";
$msg .= "Subject: =?UTF-8?B?".base64_encode('kegalle SMTP Test ✅')."?=\r\n";
$msg .= "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n";
$msg .= "SMTP is working! Emails from kurulla.com are now live.\r\n";
fputs($sock, $msg."\r\n.\r\n");
$sent = fgets($sock, 1024);
echo "Send result: ".trim($sent)."\n";

fputs($sock, "QUIT\r\n");
fclose($sock);

if (strpos($sent, '250') !== false) echo "\n✅ Email sent to $to — check your inbox!\n";
else echo "\n❌ Send failed\n";

echo "</pre>";
@unlink(__FILE__);
