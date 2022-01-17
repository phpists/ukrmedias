<?php

namespace app\components\assets;

use yii\web\AssetBundle;

class CatalogAssets extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/files/frontend/css/styles.css?v=' . PROJECT_VERSION,
        '/files/frontend/css/header.css?v=' . PROJECT_VERSION,
        '/files/frontend/css/product.css?v=' . PROJECT_VERSION,
        '/files/frontend/css/brands.css?v=' . PROJECT_VERSION,
        '/files/frontend/css/directory-w-c.css?v=' . PROJECT_VERSION,
        '/files/frontend/css/directory.css?v=' . PROJECT_VERSION,
    ];
    public $cssOptions = [
        'media' => 'all'
    ];
    public $js = [
    ];
    public $depends = [
        'app\components\assets\FrontendAssets',
    ];

}
