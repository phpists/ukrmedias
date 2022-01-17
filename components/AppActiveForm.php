<?php

namespace app\components;

use yii\widgets\ActiveFormAsset;
use yii\validators\ValidationAsset;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\assets\FrontendAssets;
use \Yii;
use kartik\select2\Select2;

class AppActiveForm extends \yii\widgets\ActiveForm {

    public $validateOnChange = false;
    public $validateOnBlur = false;
    public $options = ['class' => 'form-default'];
    public $fieldConfig = [
        'template' => "{label}{input}\n{error}\n",
    ];

    public function init() {
        FrontendAssets::register($this->getView());
        parent::init();
        $this->options['novalidate'] = '';
    }

    public function autocomplete($model, $attribute, $data, $options = []) {
        //https://demos.krajee.com/widget-details/select2
        if (!isset($options['placeholder'])) {
            $options['placeholder'] = $model->getAttributeLabel($attribute);
        }
        $label = ArrayHelper::remove($options, 'label', null);
        return $this->field($model, $attribute)->label($label, ['class' => 'control-label'])->widget(Select2::classname(), [
                    //'bsVersion' => '4.x',
                    'data' => $data,
                    'language' => Yii::$app->language,
                    'options' => $options,
                    #'pluginLoading' => false,
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
        ]);
    }

}
