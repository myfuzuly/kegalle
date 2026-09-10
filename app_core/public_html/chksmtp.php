<?php
$env = file('/home/kegalle/app_core/.env');
$keys = ['MAIL_MAILER','MAIL_HOST','MAIL_PORT','MAIL_USERNAME','MAIL_ENCRYPTION','MAIL_FROM_ADDRESS','MAIL_FROM_NAME'];
foreach ($env as $line) {
    foreach ($keys as $k) {
        if (str_starts_with(trim($line), $k . '=')) echo trim($line) . "\n";
    }
}
// Show password presence only
foreach ($env as $line) {
    if (str_starts_with(trim($line), 'MAIL_PASSWORD=')) {
        echo 'MAIL_PASSWORD=' . (strlen(trim(explode('=', $line, 2)[1])) > 0 ? '[SET]' : '[EMPTY]') . "\n";
    }
}
unlink(__FILE__);
