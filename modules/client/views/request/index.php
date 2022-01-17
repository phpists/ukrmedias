<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;
use app\components\Auth;

$this->title = 'Звернення до адміністратора';
?>

<div class="main-template-employ main-template-admin">
    <div class="flex-title-block">
        <div class="h3 main-template__header"><?php echo $this->title; ?></div>
    </div>
    <div class="feedback__wrap ">
        <?php $form = AppActiveForm::begin(['errorSummaryModels' => $model]); ?>
        <div class="form-default__row">
            <?php echo $form->field($model, 'subject_id')->label(null, ['class' => 'form-default__label'])->dropDownList($model::$subjectLabels, ['class' => 'js-dropdown-select form-default__select']); ?>
        </div>
        <div class="form-default__row">
            <?php echo $form->field($model, 'mess')->label(null, ['class' => 'form-default__label'])->textarea(['class' => 'form-default__textarea']); ?>
        </div>
        <div class="calc-buttons-group">
            <div class="">
                <button class="btn btn-profile btn-blue" aria-haspopup="true"><svg class="icon icon-check"><use xlink:href="img/sprite.svg#icon-check"></use></svg> Надіслати</button>
            </div>
        </div>
        <?php AppActiveForm::end(); ?>
    </div>
</div>