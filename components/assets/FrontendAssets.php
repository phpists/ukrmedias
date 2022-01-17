<?php

namespace app\components\assets;

use yii\web\AssetBundle;

class FrontendAssets extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css2?family=Commissioner:wght@400;500;600&display=swap',
        '/files/frontend/css/styles.css?v=' . PROJECT_VERSION,
        '/files/frontend/css/table_custom.css?v=' . PROJECT_VERSION,
    ];
    public $cssOptions = [
        'media' => 'all'
    ];
    public $js = [
        //'/files/frontend/js/jquery-3.6.0.min.js',
        '/files/frontend/js/jquery.inputmask.min.js?v=' . PROJECT_VERSION,
        '/files/frontend/js/script.js?v=' . PROJECT_VERSION,
    ];
    public $depends = [
        'yii\web\JqueryAsset',
            //'app\components\assets\Select2Assets',
    ];

}
