<?php

$web = require(__DIR__ . '/web.php');
return [
    'id' => 'ukrmedias',
    'name' => $web['name'],
    'basePath' => __DIR__ . '/..',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'language' => 'uk',
    'sourceLanguage' => 'uk',
    'timeZone' => 'Europe/Kiev',
    'components' => [
        'log' => $web['components']['log'],
        'db' => $web['components']['db'],
        'mailer' => $web['components']['mailer'],
        'urlManager' => $web['components']['urlManager'],
        'cache' => $web['components']['cache'],
    ],
    'params' => $web['params'],
];
