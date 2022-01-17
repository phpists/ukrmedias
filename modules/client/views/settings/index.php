<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;

$this->title = 'Налаштування';
?>
<div class="h3 main-template__header settings-page"><?php echo $this->title; ?></div>
<div class="balance-wrap">
    <div class="title-block">БАЛАНС</div>
    <div class="balance-list">
        <div class="balance-list__item">
            <div class="title">Залишок</div>
            <div class="value">₴ <span><?php echo $model->balance; ?></span></div>
        </div>
        <div class="balance-list__item">
            <div class="title">Абонплата</div>
            <div class="value">₴ <span><?php echo $taxAmount; ?></span></div>
        </div>
    </div>
</div>
<div class="profile__wrap profile__wrap-client">
    <?php $form = AppActiveForm::begin(['errorSummaryModels' => $model]); ?>
    <div class="form-default__row">
        <?php echo $form->field($model, 'title')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
    </div>
    <div class="form-default__row">
        <?php echo $form->field($model, 'code')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
    </div>
    <div class="form-default__row">
        <?php echo $form->field($model, 'bank')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
    </div>
    <div class="form-default__row">
        <?php echo $form->field($model, 'iban')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
    </div>
    <div class="form-default__row">
        <?php echo $form->field($model, 'liqpay_id')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
    </div>
    <div class="form-default__row">
        <?php echo $form->field($model, 'liqpay_key')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
    </div>
    <div class="calc-buttons-group">
        <div class="">
            <button class="btn btn-profile btn-blue" aria-haspopup="true" name="action" value="save"><svg class="icon icon-check"><use xlink:href="img/sprite.svg#icon-check"></use></svg> Зберегти</button>
        </div>
    </div>
    <?php AppActiveForm::end(); ?>
</div>
