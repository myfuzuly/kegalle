<?php
// Probe Hutch SMS API endpoints to find the correct format
// Tests with dummy credentials to see what error we get (confirms endpoint is reachable)

$password = 'vi&&63FH';
$senderid = 'Kainglobe';
$testPhone = '94771234567';
$testMsg   = 'kegalle test';

$endpoints = [
    'JSON-body (current)' => [
        'url'  => 'https://api.hutch.lk/api/sms/send',
        'type' => 'json',
        'body' => ['username'=>'test','password'=>$password,'to'=>$testPhone,'message'=>$testMsg,'from'=>$senderid],
    ],
    'Form POST v1' => [
        'url'  => 'https://sms.hutch.lk/api/mt/SendSMS',
        'type' => 'form',
        'body' => ['user'=>'test','password'=>$password,'senderid'=>$senderid,'channel'=>'Trans','DCS'=>'0','flashsms'=>'0','number'=>$testPhone,'text'=>$testMsg,'route'=>'26'],
    ],
    'Form POST v2' => [
        'url'  => 'http://sms.hutch.lk/api/mt/SendSMS',
        'type' => 'form',
        'body' => ['user'=>'test','password'=>$password,'senderid'=>$senderid,'channel'=>'Trans','DCS'=>'0','flashsms'=>'0','number'=>$testPhone,'text'=>$testMsg,'route'=>'26'],
    ],
    'JSON v2 user field' => [
        'url'  => 'https://sms.hutch.lk/api/sms/send',
        'type' => 'json',
        'body' => ['user'=>'test','password'=>$password,'senderid'=>$senderid,'number'=>$testPhone,'text'=>$testMsg],
    ],
];

echo "<pre>";
foreach($endpoints as $label => $cfg){
    $ch = curl_init($cfg['url']);
    $headers = [];
    if($cfg['type'] === 'json'){
        $payload = json_encode($cfg['body']);
        $headers = ['Content-Type: application/json'];
    } else {
        $payload = http_build_query($cfg['body']);
        $headers = ['Content-Type: application/x-www-form-urlencoded'];
    }
    curl_setopt_array($ch,[
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 8,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    $final= curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    echo "[$label]\n";
    echo "  URL:      $final\n";
    echo "  HTTP:     $http\n";
    echo "  Response: ".substr($resp ?: "(empty/$err)", 0, 300)."\n\n";
}

@unlink(__FILE__);
echo "</pre>Done.";
