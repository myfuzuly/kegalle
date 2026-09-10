<?php
$lines = file('/home/kegalle/app_core/.env');
$show = ['CACHE_STORE','CACHE_DRIVER','SESSION_DRIVER','SESSION_STORE','REDIS_HOST','REDIS_PORT'];
foreach ($lines as $line) {
    foreach ($show as $k) {
        if (str_starts_with(trim($line), $k . '=')) {
            echo trim($line) . "\n";
        }
    }
}
unlink(__FILE__);
