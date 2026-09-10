<?php
echo "<pre>";
$paths = ['/','/api/','/sms/','/send','/SendSMS','/v1/','/v2/','/api/v1/','/api/v2/','/rest/','/ws/','/service/'];
foreach($paths as $p){
    $ch = curl_init('https://bulksms.hutch.lk'.$p);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>5,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_FOLLOWLOCATION=>false]);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "  GET $p => HTTP $http  ".substr(strip_tags($resp??''),0,120)."\n";
}
@unlink(__FILE__);
echo "</pre>Done.";
