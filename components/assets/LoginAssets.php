<?php

namespace app\components\assets;

use yii\web\AssetBundle;

class LoginAssets extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $cssOptions = [
        'media' => 'all'
    ];
    public $js = [
        '/files/frontend/js/jquery.inputmask.min.js?v=' . PROJECT_VERSION,
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
