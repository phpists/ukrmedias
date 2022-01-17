<?php

namespace app\components;

use yii\widgets\ActiveFormAsset;
use yii\validators\ValidationAsset;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\assets\AdminAssets;
use \Yii;
use kartik\select2\Select2;
use kartik\datetime\DateTimePicker;
use kartik\date\DatePicker;

class AdminActiveForm extends \yii\widgets\ActiveForm {

    public $errorSummaryModels;
    public $errorSummaryCssClass = 'alert alert-danger';
    public $validateOnChange = false;
    public $validateOnBlur = false;
    public $fieldConfig = [
        'template' => "{label}\n{input}\n",
    ];

    public function init() {
        AdminAssets::register($this->getView());
        parent::init();
        $this->options['novalidate'] = '';
    }

    public function run() {
        if (!empty($this->_fields)) {
            throw new InvalidCallException('Each beginField() should have a matching endField() call.');
        }
        $content = ob_get_clean();
        $html = Html::beginForm($this->action, $this->method, $this->options);
        if ($this->errorSummaryModels) {
            $html .= $this->errorSummary($this->errorSummaryModels, ['header' => false, 'footer' => false, 'class' => 'alert alert-danger']);
        }
        $html .= $content;
        if ($this->enableClientScript) {
            $this->registerClientScript();
        }
        $html .= Html::endForm();
        return $html;
    }

    public function registerClientScript() {
        $id = $this->options['id'];
        $options = Json::htmlEncode($this->getClientOptions());
        $attributes = Json::htmlEncode($this->attributes);
        $view = $this->getView();
        ValidationAsset::register($view);
        ActiveFormAsset::register($view);
        $view->registerJs("jQuery('#$id').yiiActiveForm($attributes, $options);");
//        $view->registerJs("$('#$id').on('afterValidate', function (e,messages,errors) {
//            errors.forEach(function(el){
//                document.getElementById(el.id).style.valid = false;
//            });
//            return true;
//        });");
    }

    public function autocomplete($model, $attribute, $data, $options = []) {
        //https://demos.krajee.com/widget-details/select2
        if (!isset($options['placeholder'])) {
            $options['placeholder'] = $model->getAttributeLabel($attribute);
        }
        $pluginOptions = ArrayHelper::remove($options, 'pluginOptions', []);
        $label = isset($options['label']) ? $options['label'] : null;
        return $this->field($model, $attribute)->label($label)->widget(Select2::classname(), [
                    'bsVersion' => '4.x',
                    'data' => $data,
                    'options' => $options,
                    'language' => Yii::$app->language,
                    'pluginOptions' => ArrayHelper::merge(['allowClear' => true], $pluginOptions),
        ]);
    }

    public function datetime($model, $attribute, $options = [], $pluginOptions = []) {
        //https://demos.krajee.com/widget-details/datetimepicker
        if (!isset($options['placeholder'])) {
            $options['placeholder'] = $model->getAttributeLabel($attribute);
        }
        $pOptions = [
            'weekStart' => 1,
            'readonly' => true,
            'autoclose' => true,
            'format' => 'd.m.yy H:i',
            'minuteStep' => 15,
        ];
        $id = uniqid('dp_');
        return $this->field($model, $attribute, [
                    'template' => '{label}{dp}{input}',
                    'parts' => [
                        '{dp}' => DateTimePicker::widget([
                            'bsVersion' => '4.x',
                            'name' => '',
                            'options' => $options,
                            'type' => DateTimePicker::TYPE_INPUT,
                            'language' => Yii::$app->language,
                            'pluginOptions' => \yii\helpers\ArrayHelper::merge($pOptions, $pluginOptions),
                            'pluginEvents' => [
                                'changeDate' => 'function(e) {
                                    let m="0".concat(e.date.getMonth()+1).slice(-2);
                                    let d="0".concat(e.date.getDate()).slice(-2);
                                    let h="0".concat(e.date.getHours()).slice(-2);
                                    let i="0".concat(e.date.getMinutes()).slice(-2);
                                    $("#' . $id . '").val("".concat(e.date.getFullYear(),"-",m,"-",d," ",h,":",i,":00"));
                                }',
                            ]
                        ]),
                    ],
                ])->input('hidden', ['id' => $id]);
    }

    public function date($model, $attribute, $options = [], $pluginOptions = []) {
        //https://demos.krajee.com/widget-details/datepicker
        if (!isset($options['placeholder'])) {
            $options['placeholder'] = $model->getAttributeLabel($attribute);
        }
        $pOptions = [
            'weekStart' => 1,
            'readonly' => true,
            'autoclose' => true,
            'format' => 'dd.mm.yyyy',
            'todayHighlight' => true,
        ];
        $id = uniqid('dp_');
        return $this->field($model, $attribute, [
                    'template' => '{label}{dp}{input}',
                    'parts' => [
                        '{dp}' => DatePicker::widget([
                            'bsVersion' => '4.x',
                            'name' => '',
                            'value' => $model->getDate(),
                            'options' => $options,
                            'type' => DatePicker::TYPE_INPUT,
                            'language' => Yii::$app->language,
                            'pluginOptions' => \yii\helpers\ArrayHelper::merge($pOptions, $pluginOptions),
                            'pluginEvents' => [
                                'changeDate' => 'function(e) {
                                    let m="0".concat(e.date.getMonth()+1).slice(-2);
                                    let d="0".concat(e.date.getDate()).slice(-2);
                                    $("#' . $id . '").val("".concat(e.date.getFullYear(),"-",m,"-",d));
                                }',
                            ]
                        ]),
                    ],
                ])->input('hidden', ['id' => $id]);
    }

}
