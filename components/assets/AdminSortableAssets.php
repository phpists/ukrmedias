<?php

namespace app\components\assets;

use yii\web\AssetBundle;

class AdminSortableAssets extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
            #'/files/admin/css/admin.css?v=' . PROJECT_VERSION,
    ];
    public $js = [
        '/files/admin/js/html5sortable.js',
        '/files/admin/js/adminSortable.js',
    ];
    public $depends = [
        'app\components\assets\AdminAssets',
    ];

}
