<?php
if (($_GET['t'] ?? '') !== 'IOnM9RKHI05a5X8kRpMGThCE62Gu') { http_response_code(403); exit('Forbidden'); }
$r = function_exists('opcache_reset') ? opcache_reset() : false;
@unlink(__FILE__);
echo 'OPcache: '.($r ? 'cleared' : 'not available').'.';
