<?php
if(function_exists('opcache_reset')){opcache_reset();echo "OPcache reset. ";}
$c=0;foreach(glob(__DIR__.'/../app_core/storage/framework/views/*.php') as $f){if(@unlink($f))$c++;}
echo "Views: $c cleared. ";
// Also verify the search input type
$blade = file_get_contents(__DIR__.'/../app_core/resources/views/layouts/app.blade.php');
echo strpos($blade,'type="text"') !== false ? 'input=text OK' : 'input=text NOT FOUND';
@unlink(__FILE__);
