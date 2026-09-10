<?php
echo "<pre>";

// DNS resolution tests
$hosts = ['api.hutch.lk','sms.hutch.lk','hutch.lk','www.hutch.lk','dialog.lk','notify.lk'];
echo "=== DNS Resolution ===\n";
foreach($hosts as $h){
    $ip = gethostbyname($h);
    echo "  $h => ".($ip !== $h ? $ip : "FAIL (no DNS)")."\n";
}

// Check if outbound HTTP works at all
echo "\n=== Outbound HTTP test ===\n";
$ch = curl_init('https://httpbin.org/ip');
curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>6,CURLOPT_SSL_VERIFYPEER=>false]);
$r = curl_exec($ch); $code = curl_getinfo($ch,CURLINFO_HTTP_CODE); $err=curl_error($ch); curl_close($ch);
echo "  httpbin.org: HTTP $code ".($err?"ERR:$err":substr($r,0,100))."\n";

// Check server IP
echo "\n=== Server outbound IP ===\n";
$ch = curl_init('https://api.ipify.org');
curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>6,CURLOPT_SSL_VERIFYPEER=>false]);
$r = curl_exec($ch); curl_close($ch);
echo "  Server IP: ".($r?:'unknown')."\n";

// Try common SL SMS providers
echo "\n=== Other SL SMS API endpoints ===\n";
$urls = [
    'https://www.textit.biz/',
    'https://www.mobitel.lk/',
    'http://bulksms.hutch.lk/',
    'https://bulksms.hutch.lk/',
    'http://api.hutch.com.lk/',
];
foreach($urls as $u){
    $ch = curl_init($u);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>5,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_NOBODY=>true,CURLOPT_FOLLOWLOCATION=>true]);
    curl_exec($ch);
    $code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    $ip   = curl_getinfo($ch,CURLINFO_PRIMARY_IP);
    $err  = curl_error($ch);
    curl_close($ch);
    echo "  $u => HTTP $code IP=$ip ".($err?"ERR:$err":"")."\n";
}

@unlink(__FILE__);
echo "</pre>Done.";
