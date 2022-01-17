<?php

error_reporting(-1);
date_default_timezone_set('Europe/Kiev');
mb_internal_encoding('UTF-8');
ini_set('display_errors', false);
ini_set('memory_limit', '512M');
ini_set('log_errors', true);
ini_set('error_log', __DIR__ . '/../runtime/logs/app.log');
define('YII_DEBUG', getenv('SERVER_INSTANCE_ID') == '');
require(__DIR__ . '/../components/Yii.php');
$config = require(__DIR__ . '/../config/web.php');
$local = __DIR__ . '/../config/local_' . getenv('SERVER_INSTANCE_ID') . '.php';
if (is_file($local)) {
    $config = (new yii\helpers\BaseArrayHelper)->merge($config, include($local));
}

(new yii\web\Application($config))->run();
