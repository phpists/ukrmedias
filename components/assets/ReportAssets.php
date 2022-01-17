<?php

namespace app\components\assets;

use yii\web\AssetBundle;

class ReportAssets extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        '/files/frontend/js/lib/chart/chart.js',
        '/files/frontend/js/app_report.js?v=' . PROJECT_VERSION,
    ];
    public $depends = [
        'app\components\assets\FrontendAssets',
        'app\components\assets\DatepickerAssets',
    ];

}
