<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require '/home/kurulla/app_core/vendor/autoload.php';

$app = require_once '/home/kurulla/app_core/bootstrap/app.php';

$app->handleRequest(Request::capture());