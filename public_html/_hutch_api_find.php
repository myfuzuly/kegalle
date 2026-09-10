<?php
$password = 'vi&&63FH';
$senderid = 'Kainglobe';
$base = 'https://bulksms.hutch.lk';

echo "<pre>";

// Try common controller paths
$paths = [
    '/controllers/send_sms_controller.php',
    '/controllers/api_controller.php',
    '/controllers/sms_controller.php',
    '/controllers/SendSMS.php',
    '/api.php',
    '/sendsms.php',
    '/smssend.php',
    '/http_api',
    '/http_api.php',
];
echo "=== Probing controller paths ===\n";
foreach($paths as $p){
    $ch = curl_init($base.$p);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>5,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_NOBODY=>true]);
    curl_exec($ch);
    $http = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "  $p => HTTP $http\n";
}

// Try login with mask ID as username to get session / discover API
echo "\n=== Login attempt (mask as username) ===\n";
$jar = tempnam(sys_get_temp_dir(),'hutch_cookie');
$ch = curl_init($base.'/controllers/login_controller.php');
curl_setopt_array($ch,[
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query(['username'=>$senderid,'password'=>$password,'submit'=>'Login']),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 8,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_COOKIEJAR => $jar,
    CURLOPT_COOKIEFILE => $jar,
    CURLOPT_HEADER => true,
]);
$resp = curl_exec($ch);
$http = curl_getinfo($ch,CURLINFO_HTTP_CODE);
$final = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
curl_close($ch);
echo "HTTP: $http\nFinal URL: $final\nResponse (first 500):\n".substr($resp,0,500)."\n";

@unlink($jar);
@unlink(__FILE__);
echo "</pre>Done.";
