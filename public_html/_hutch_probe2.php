<?php
$password = 'vi&&63FH';
$senderid = 'Kainglobe';
$testPhone = '94771234567'; // dummy number

echo "<pre>";

// Probe the bulksms.hutch.lk endpoints
$tests = [
    'JSON /api/mt/SendSMS' => [
        'url' => 'https://bulksms.hutch.lk/api/mt/SendSMS',
        'ct'  => 'application/json',
        'body'=> json_encode(['user'=>'test','password'=>$password,'senderid'=>$senderid,'channel'=>'Trans','DCS'=>'0','flashsms'=>'0','number'=>$testPhone,'text'=>'test','route'=>'26']),
    ],
    'Form /api/mt/SendSMS' => [
        'url' => 'https://bulksms.hutch.lk/api/mt/SendSMS',
        'ct'  => 'application/x-www-form-urlencoded',
        'body'=> http_build_query(['user'=>'test','password'=>$password,'senderid'=>$senderid,'channel'=>'Trans','DCS'=>'0','flashsms'=>'0','number'=>$testPhone,'text'=>'test','route'=>'26']),
    ],
    'Form /api/sms/send' => [
        'url' => 'https://bulksms.hutch.lk/api/sms/send',
        'ct'  => 'application/x-www-form-urlencoded',
        'body'=> http_build_query(['username'=>'test','password'=>$password,'from'=>$senderid,'to'=>$testPhone,'message'=>'test']),
    ],
    'GET /api/mt/SendSMS' => [
        'url' => 'https://bulksms.hutch.lk/api/mt/SendSMS?user=test&password='.urlencode($password).'&senderid='.$senderid.'&channel=Trans&DCS=0&flashsms=0&number='.$testPhone.'&text=test&route=26',
        'ct'  => null,
        'body'=> null,
    ],
];

foreach($tests as $label => $cfg){
    $ch = curl_init($cfg['url']);
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 8,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
    ];
    if($cfg['body'] !== null){
        $opts[CURLOPT_POST] = true;
        $opts[CURLOPT_POSTFIELDS] = $cfg['body'];
        $opts[CURLOPT_HTTPHEADER] = ['Content-Type: '.$cfg['ct']];
    }
    curl_setopt_array($ch, $opts);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    echo "[$label]\n  HTTP: $http\n  Response: ".substr($resp?:("ERR:".$err),0,400)."\n\n";
}

@unlink(__FILE__);
echo "</pre>Done.";
