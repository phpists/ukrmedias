<?php

return [
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=localhost;dbname=ukrmedias',
            'username' => 'root',
            'password' => 'root',
        ],
    ],
    'params' => [
        'systemEmail' => 'no-reply@dev4.ingsot.com',
        'isTest' => true,
        'seo_js' => false,
        'apiTestUrl' => 'http://ukrmedias.loc',
    ],
];
