<?php

define('PROJECT_VERSION', 41);
define('HTML_CACHE_DURATION', 121);
$domain = getenv('HTTP_HOST');
return [
    'id' => 'ukrmedias',
    'name' => 'UkrMedias',
    'basePath' => __DIR__ . '/..',
    'bootstrap' => ['log'],
    'language' => 'uk',
    'sourceLanguage' => 'uk',
    'timeZone' => 'Europe/Kiev',
    'defaultRoute' => '/frontend/site/login',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'modules' => [
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
        'admin' => [
            'class' => 'app\modules\admin\Module',
            'defaultRoute' => '/admin/profile/update',
        ],
        'dev' => [
            'class' => 'app\modules\dev\Module',
            'defaultRoute' => '/dev/site/index',
        ],
        'frontend' => [
            'class' => 'app\modules\frontend\Module',
            'defaultRoute' => '/frontend/site/login',
            'layout' => '@app/modules/frontend/views/layouts/main.php',
        ],
        'client' => [
            'class' => 'app\modules\client\Module',
            'defaultRoute' => '/client/profile/update',
            'layout' => '@app/modules/client/views/layouts/main.php',
        ],
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'nWVtpfKMPiOlf043B3WetGbNy4GwKsaL',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'defaultDuration' => 60,
        ],
        'user' => [
            'enableAutoLogin' => true,
            'identityClass' => 'app\models\Users',
            'loginUrl' => ['/frontend/site/index'],
            'authTimeout' => 3600 * 24 * 30,
            'absoluteAuthTimeout' => 3600 * 24 * 30,
        ],
        'session' => [
            'timeout'=> 3600 * 24 * 14,
        ],
        'errorHandler' => [
            'errorAction' => 'frontend/site/error',
        ],
        'authManager' => [
            'class' => 'app\components\MyAuthManager',
            'itemFile' => '@app/components/rbac/items.php',
            'assignmentFile' => '@app/components/rbac/assignments.php',
            'ruleFile' => '@app/components/rbac/rules.php',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'baseUrl' => 'https://' . $domain,
            'rules' => [
                '/' => 'frontend/site/index',
                '<action:(start|login2|login|registration-step1|registration-step2|logout)>' => 'frontend/site/<action>',
                '<action:(password|activate)>/<key>/<id:\d+>/<uid:\d+>' => 'frontend/site/<action>',
                '<action:(search|novelty|promo)>' => 'client/catalog/<action>',
                'brands' => 'client/brands/index',
                '<brandCpu:[a-z0-9\-\_]+>/<cpu:[a-z0-9\-\_]+>' => 'client/catalog/index',
                '<cpu:[a-z0-9\-\_]+>' => 'client/catalog/index',
            //'<module:(client|abonent)>/<controller>/<action>/<id:(\d+|\d\d\d\d-\d\d-\d\d)>/<aid:\d+>/<sid:\d+>' => '<module>/<controller>/<action>',
            //'<module:(client)>/<controller>/<action>/<id:(\d+|\d\d\d\d-\d\d-\d\d)>/<aid:\d+>' => '<module>/<controller>/<action>',
            //'<module:(client)>/<controller>/<action>/<id:(\d+|\d\d\d\d-\d\d-\d\d)>' => '<module>/<controller>/<action>',
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            //'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'localhost',
                'username' => 'no-reply@dev4.ingsot.com',
                'password' => 'zN9n5gBFlklLuegbJ8A4q7PXx1j47WkB',
                'port' => '465',
                'encryption' => 'ssl',
                'streamOptions' => [
                    'ssl' => ['allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false],
                ]
            ],
        ],
        'log' => [
            'traceLevel' => 0,
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'except' => ['yii\web\HttpException:403', 'yii\web\HttpException:404', 'yii\web\HttpException:400'],
                    'logVars' => [],
                ],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=ukrmedias',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
        ],
    ],
    'params' => [
        'uploadBaseUrl' => '/files/images/upload',
        'systemEmail' => 'no-reply@dev4.ingsot.com',
        'bsDependencyEnabled' => false,
        'isTest' => false,
        'seo_js' => true,
        'watermark' => '@app/web/files/images/watermark.jpg',
        'apiTestUrl' => 'http://ukrmedias.loc',
    ],
];
