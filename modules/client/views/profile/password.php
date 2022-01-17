<?php

use yii\helpers\Url;
use app\components\AppActiveForm;

$this->params['breadcrumbs'] = [
    ['label' => 'Профіль', 'url' => 'index'],
];
$this->title = 'Зміна паролю';
?><div class="h3 main-template__header"><?php echo $this->title; ?></div>
<div class="profile__wrap profile__wrap-client">
    <?php $form = AppActiveForm::begin(); ?>
    <div class="title-form h4">ОСОБИСТІ ДАНІ</div>
    <div class="form-default__row">
        <?php echo $form->field($model, 'password')->label('Поточний пароль', ['class' => 'form-default__label'])->input('password', ['class' => 'form-default__input']); ?>
    </div>
    <div class="form-default__row">
        <?php echo $form->field($model, 'password_new1')->label(null, ['class' => 'form-default__label'])->input('password', ['class' => 'form-default__input']); ?>
    </div>
    <div class="form-default__row">
        <?php echo $form->field($model, 'password_new2')->label(null, ['class' => 'form-default__label'])->input('password', ['class' => 'form-default__input']); ?>
    </div>
    <br/>
    <div class="calc-buttons-group">
        <div class="">
            <a href="<?php echo Url::toRoute('index'); ?>" class="close btn btn-profile bg-white">&larr; Назад</a>
        </div>
        <div class="">
            <button class="btn btn-profile btn-blue" aria-haspopup="true" name="action" value="save"><svg class="icon icon-check"><use xlink:href="img/sprite.svg#icon-check"></use></svg> Зберегти</button>
        </div>
    </div>
    <?php AppActiveForm::end(); ?>
</div>
