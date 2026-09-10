<?php
$php = '/opt/alt/php83/usr/bin/php';
$out = shell_exec("$php /home/kegalle/public_html/mailrun.php 2>&1");
echo nl2br(htmlspecialchars($out ?: '(no output)'));
@unlink('/home/kegalle/public_html/mailrun.php');
unlink(__FILE__);
