<?php

return [
    'm01' => [
        'type' => 1,
        'description' => '',
        'children' => ['m02'],
    ],
    'm02' => [
        'type' => 1,
        'description' => '',
        'children' => ['m03'],
    ],
    'm03' => [
        'type' => 1,
        'description' => '',
    ],
    'c00' => [
        'type' => 1,
        'description' => '',
    ],
    'c01' => [
        'type' => 1,
        'description' => '',
    ],
    '000' => [
        'type' => 1,
        'description' => 'developer',
        'children' => ['m01'],
    ],
];
