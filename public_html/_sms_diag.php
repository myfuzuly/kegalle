<?php
// Read .env
$env = [];
foreach (file(__DIR__.'/../app_core/.env', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line),'#') || !str_contains($line,'=')) continue;
    [$k,$v] = explode('=',$line,2);
    $env[trim($k)] = trim($v," \t\n\r\0\x0B\"'");
}

$user = $env['HUTCH_SMS_USERNAME'] ?? '(not set)';
$pass = $env['HUTCH_SMS_PASSWORD'] ?? '(not set)';
$sid  = $env['HUTCH_SMS_SENDER_ID'] ?? '(not set)';

echo "<pre>";
echo "HUTCH_SMS_USERNAME = $user\n";
echo "HUTCH_SMS_PASSWORD = " . (empty($pass) || $pass==='(not set)' ? '(not set)' : '(set, '.strlen($pass).' chars)') . "\n";
echo "HUTCH_SMS_SENDER_ID = $sid\n\n";

if (empty($user) || $user === '(not set)') {
    echo "❌ Credentials not in .env — will be no-op\n";
    echo "</pre>"; exit;
}

// Try login
echo "=== Testing login ===\n";
$ch = curl_init('https://bsms.hutch.lk/api/login');
curl_setopt_array($ch,[
    CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>json_encode(['username'=>$user,'password'=>$pass]),
    CURLOPT_HTTPHEADER=>['Content-Type: application/json','X-API-VERSION: v1','Accept: */*'],
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_TIMEOUT=>10,
    CURLOPT_SSL_VERIFYPEER=>false,
]);
$resp = curl_exec($ch);
$http = curl_getinfo($ch,CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP: $http\n";
echo "Response: $resp\n";
echo "</pre>";
@unlink(__FILE__);
