<?php

namespace app\components\assets;

use yii\web\AssetBundle;

class ClientAssets extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/files/frontend/css/styles.css?v=' . PROJECT_VERSION,
        '/files/frontend/css/header.css?v=' . PROJECT_VERSION,
        '/files/frontend/css/product.css?v=' . PROJECT_VERSION,
        '/files/frontend/css/directory-w-c.css?v=' . PROJECT_VERSION,
        '/files/frontend/css/delivery.css?v=' . PROJECT_VERSION,
        '/files/frontend/css/orders.css?v=' . PROJECT_VERSION,
        '/files/frontend/css/order-detail.css?v=' . PROJECT_VERSION,
    ];
    public $cssOptions = [
        'media' => 'all'
    ];
    public $js = [
        '/files/frontend/js/jquery.inputmask.min.js?v=' . PROJECT_VERSION,
    ];
    public $depends = [
        'app\components\assets\FrontendAssets',
    ];

}
