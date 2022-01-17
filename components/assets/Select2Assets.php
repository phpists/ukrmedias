<?php

namespace app\components\assets;

class Select2Assets extends \kartik\select2\ThemeAsset {

    #public $basePath = '@webroot';
    #public $baseUrl = '@web';
    public $css = [
        '/files/frontend/css/select2-custom.css?v=' . PROJECT_VERSION,
    ];
    public $cssOptions = [
        'media' => 'all'
    ];

}
