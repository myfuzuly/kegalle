<?php
if(function_exists('opcache_reset')){opcache_reset();echo "OPcache reset. ";}
$c=0;foreach(glob(__DIR__.'/../app_core/storage/framework/views/*.php') as $f){if(@unlink($f))$c++;}
echo "Views: $c cleared";
@unlink(__FILE__);
