<?php
$password = 'vi&&63FH';
$senderid = 'Kainglobe';
$base = 'https://bulksms.hutch.lk/sendsms.php';
// Use a real test number — put yours here to actually receive if creds work
// Using dummy 94771234567 for format testing only
$testPhone = '94771234567';
$testMsg   = 'kegalle OTP test: 123456';

echo "<pre>";

// Try different field name combinations
$variants = [
    'GET username+password+senderid' => [
        'method' => 'GET',
        'url' => $base.'?'.http_build_query(['username'=>$senderid,'password'=>$password,'to'=>$testPhone,'message'=>$testMsg,'senderid'=>$senderid]),
    ],
    'GET user+pass' => [
        'method' => 'GET',
        'url' => $base.'?'.http_build_query(['user'=>$senderid,'pass'=>$password,'msisdn'=>$testPhone,'message'=>$testMsg,'sender'=>$senderid,'action'=>'send']),
    ],
    'POST form user+password+msisdn' => [
        'method' => 'POST',
        'url' => $base,
        'body' => http_build_query(['user'=>$senderid,'password'=>$password,'msisdn'=>$testPhone,'message'=>$testMsg,'senderid'=>$senderid]),
        'ct' => 'application/x-www-form-urlencoded',
    ],
    'POST form username+password+to' => [
        'method' => 'POST',
        'url' => $base,
        'body' => http_build_query(['username'=>$senderid,'password'=>$password,'to'=>$testPhone,'message'=>$testMsg,'sender'=>$senderid]),
        'ct' => 'application/x-www-form-urlencoded',
    ],
    'POST JSON' => [
        'method' => 'POST',
        'url' => $base,
        'body' => json_encode(['username'=>$senderid,'password'=>$password,'to'=>$testPhone,'message'=>$testMsg,'senderid'=>$senderid]),
        'ct' => 'application/json',
    ],
];

foreach($variants as $label => $cfg){
    $ch = curl_init($cfg['url']);
    $opts = [CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>8,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_FOLLOWLOCATION=>true];
    if($cfg['method']==='POST'){
        $opts[CURLOPT_POST] = true;
        $opts[CURLOPT_POSTFIELDS] = $cfg['body'];
        $opts[CURLOPT_HTTPHEADER] = ['Content-Type: '.$cfg['ct']];
    }
    curl_setopt_array($ch,$opts);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    echo "[$label]\n  HTTP: $http\n  Response: ".substr($resp?:("ERR:".$err),0,300)."\n\n";
}

@unlink(__FILE__);
echo "</pre>Done.";
