<?php

namespace app\components\assets;

use yii\web\AssetBundle;

class AdminAssets extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        #'/files/admin/bootstrap-4.5.0/bootstrap.min.css',
        '/files/admin/fontawesome-free-5.13.0-web/css/all.min.css',
        '/files/admin/fontawesome-free-5.13.0-web/css/v4-shims.min.css',
        '/files/admin/css/admin.css?v=' . PROJECT_VERSION,
    ];
    public $js = [
        //'/files/js/jquery-3.3.1.min.js',
        '/files/admin/bootstrap-4.5.0/bootstrap.bundle.min.js',
        #'/files/fontawesome-free-5.13.0-web/js/all.min.js',
        '/files/admin/js/admin.js?v=' . PROJECT_VERSION,
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset'
    ];

}
