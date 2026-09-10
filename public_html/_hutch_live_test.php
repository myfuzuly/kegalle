<?php
$username = 'fuzuly@kainglobe.com';
$password = 'vi&&63FH';
$mask     = 'Kainglobe';

// Use your own number here to actually receive the SMS
// Defaulting to a dummy — change to real number if you want to test receipt
$testPhone = '94771234567'; // ← change to real number for live test

echo "<pre>";

// Step 1: Login
echo "=== Step 1: OAuth Login ===\n";
$ch = curl_init('https://bsms.hutch.lk/api/login');
curl_setopt_array($ch,[
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode(['username'=>$username,'password'=>$password]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json','X-API-VERSION: v1','Accept: */*'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$resp = curl_exec($ch);
$http = curl_getinfo($ch,CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

echo "HTTP: $http\n";
echo "Response: $resp\n";
if ($err) echo "cURL error: $err\n";

$data = json_decode($resp, true);
$accessToken  = $data['accessToken']  ?? null;
$refreshToken = $data['refreshToken'] ?? null;

if (!$accessToken) {
    echo "\n❌ LOGIN FAILED — cannot proceed to send SMS\n";
    @unlink(__FILE__);
    echo "</pre>Done.";
    exit;
}
echo "\n✅ Login OK — access token obtained (".strlen($accessToken)." chars)\n";

// Step 2: Send SMS
echo "\n=== Step 2: Send SMS to $testPhone ===\n";
$ch = curl_init('https://bsms.hutch.lk/api/sendsms');
curl_setopt_array($ch,[
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'campaignName' => 'kegalle_Test',
        'mask'         => $mask,
        'numbers'      => $testPhone,
        'content'      => 'kegalle test OTP: 123456. Valid 10 min. Do not share.',
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-API-VERSION: v1',
        'Accept: */*',
        'Authorization: Bearer '.$accessToken,
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$resp2 = curl_exec($ch);
$http2 = curl_getinfo($ch,CURLINFO_HTTP_CODE);
$err2  = curl_error($ch);
curl_close($ch);

echo "HTTP: $http2\n";
echo "Response: $resp2\n";
if ($err2) echo "cURL error: $err2\n";

$ref = json_decode($resp2,true)['serverRef'] ?? null;
if ($ref) echo "\n✅ SMS SENT — serverRef: $ref\n";
else echo "\n⚠️ Response received but check content above\n";

@unlink(__FILE__);
echo "</pre>Done.";
