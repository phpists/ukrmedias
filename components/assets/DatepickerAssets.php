<?php

namespace app\components\assets;

use yii\web\AssetBundle;

class DatepickerAssets extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/files/frontend/css/datepicker.css',
    ];
    public $js = [
        '/files/frontend/js/lib/datepicker/datepicker.js',
        '/files/frontend/js/date_picker_init.js',
    ];
    public $depends = [
        'app\components\assets\FrontendAssets',
    ];

}
