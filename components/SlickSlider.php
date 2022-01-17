<?php

namespace app\components;

use yii\web\View;
use yii\web\AssetBundle;
use yii\helpers\Html;
use yii\helpers\Json;
use Yii;

class SlickSlider extends \yii\base\Widget {

    public $qty = 0;
    public $tag = 'div';
    public $options = [];
    public $tagOptions = [];

    public function init() {
        if ($this->qty === 0) {
            return;
        }
        _slick_slider_assets::register($this->getView());
        Yii::$app->view->registerJs('$("#' . $this->getId() . '").slick(' . Json::encode($this->options) . ');', View::POS_END, $this->getId());
        $this->tagOptions['id'] = $this->getId();
        echo Html::beginTag($this->tag, $this->tagOptions);
    }

    public function run() {
        if ($this->qty === 0) {
            return;
        }
        echo Html::endTag($this->tag);
    }

}

class _slick_slider_assets extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/files/system/slick.css',
    ];
    public $js = [
        '/files/system/slick.min.js',
    ];
    public $depends = [
        'app\components\assets\FrontendAssets',
    ];

}
