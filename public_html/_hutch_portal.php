<?php
echo "<pre>";
// Fetch root page to find form action / API hints
$ch = curl_init('https://bulksms.hutch.lk/');
curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>8,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_USERAGENT=>'Mozilla/5.0']);
$html = curl_exec($ch); curl_close($ch);

// Extract forms
preg_match_all('/<form[^>]*action=["\']([^"\']*)["\'][^>]*>/i',$html,$forms);
echo "Form actions found:\n";
foreach($forms[1] as $f) echo "  $f\n";

// Extract links / paths
preg_match_all('/(?:href|action|src)=["\']([^"\']*)["\']/',$html,$links);
$paths = array_unique($links[1]);
echo "\nAll links/src/action paths:\n";
foreach($paths as $p) if(trim($p) && $p[0]!==' ') echo "  $p\n";

// Extract any JS API URLs
preg_match_all('/(https?:\/\/[^\s\'"<>]+)/i',$html,$urls);
echo "\nURLs in page source:\n";
foreach(array_unique($urls[1]) as $u) echo "  $u\n";

@unlink(__FILE__);
echo "</pre>Done.";
