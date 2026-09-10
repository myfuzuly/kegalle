<?php
header('Content-Type: text/plain; charset=UTF-8');
echo file_get_contents('/home/kegalle/app_core/resources/views/admin/brands/index.blade.php');
unlink(__FILE__);
