<?php

namespace app\components\assets;

use yii\web\AssetBundle;

class ExcelViewer extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/files/frontend/css/excel_viewer.css?v=' . PROJECT_VERSION,
    ];
    public $cssOptions = [
        'media' => 'all'
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
