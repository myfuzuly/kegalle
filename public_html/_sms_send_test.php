<?php
$env = [];
foreach (file(__DIR__.'/../app_core/.env', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line),'#') || !str_contains($line,'=')) continue;
    [$k,$v] = explode('=',$line,2);
    $env[trim($k)] = trim($v," \t\n\r\0\x0B\"'");
}
$user = $env['HUTCH_SMS_USERNAME'];
$pass = $env['HUTCH_SMS_PASSWORD'];
$mask = $env['HUTCH_SMS_SENDER_ID'] ?? 'Kainglobe';

echo "<pre>";

// Login
$ch = curl_init('https://bsms.hutch.lk/api/login');
curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>json_encode(['username'=>$user,'password'=>$pass]),CURLOPT_HTTPHEADER=>['Content-Type: application/json','X-API-VERSION: v1','Accept: */*'],CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>10,CURLOPT_SSL_VERIFYPEER=>false]);
$resp = curl_exec($ch); curl_close($ch);
$data = json_decode($resp, true);
$token = $data['accessToken'] ?? null;
if (!$token) { echo "LOGIN FAILED\n$resp"; echo "</pre>"; exit; }
echo "✅ Login OK\n\n";

// Send SMS — use your own number here
$testPhone = '94777881233';
echo "=== Sending to $testPhone ===\n";
$payload = ['campaignName'=>'kegalle_OTP','mask'=>$mask,'numbers'=>$testPhone,'content'=>'kegalle test: 123456. Do not share.'];
echo "Payload: ".json_encode($payload)."\n\n";

$ch2 = curl_init('https://bsms.hutch.lk/api/sendsms');
curl_setopt_array($ch2,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>json_encode($payload),CURLOPT_HTTPHEADER=>['Content-Type: application/json','X-API-VERSION: v1','Accept: */*','Authorization: Bearer '.$token],CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>10,CURLOPT_SSL_VERIFYPEER=>false]);
$resp2 = curl_exec($ch2);
$http2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

echo "HTTP: $http2\n";
echo "Response: $resp2\n";
echo "</pre>";
@unlink(__FILE__);
