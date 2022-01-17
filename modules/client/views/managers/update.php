<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\AppActiveForm;

$this->title = $model->isNewRecord ? 'Новий співробітник' : $model->getName();
?>
<div class="table">
    <div class="profile">
        <?php $form = AppActiveForm::begin(); ?>
        <div class="item">
            <h2><?php echo $model->isNewRecord ? 'Новий співробітник' : $model->getName(); ?></h2>
            <h5>Персональна інформація.</h5>
            <p>Прізвище</p>
            <?php echo $form->field($model, 'second_name')->label(false)->input('text', ['class' => '']); ?>
            <p>Ім'я</p>
            <?php echo $form->field($model, 'first_name')->label(false)->input('text', ['class' => '']); ?>
            <p>По-батькові</p>
            <?php echo $form->field($model, 'middle_name')->label(false)->input('text', ['class' => '']); ?>
            <p>Номер телефону</p>
            <?php echo $form->field($model, 'phone')->label(false)->input('text', ['class' => '']); ?>
            <p>Email</p>
            <?php echo $form->field($model, 'email')->label(false)->input('text', ['class' => '']); ?>
        </div>
        <div class="item">
            <h2>Статус</h2>
            <?php foreach ($model::$statusLabels as $id => $title): ?>
                <label class="radio">
                    <?php
                    echo Html::activeRadio($model, 'active', [
                        'value' => $id,
                        'label' => false,
                        'uncheck' => false,
                    ]);
                    ?>
                    <span><?php echo $title; ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <div class="item">
            <h2>Інформація про замовлення</h2>
            <label class="checkbox">
                <?php echo Html::activeCheckbox($model, 'notify', ['label' => false]); ?>
                <span>Отримувати на email повідомлення про зміну статусу замовлень</span>
            </label>
        </div>
        <div class="item">
            <div class="buttons">
                <a class="btn cancel" href="<?php echo Url::to(['index']); ?>">Повернутись</a>
                <button class="btn save" name="action" value="save">Зберегти</button>
            </div>
        </div>
        <?php AppActiveForm::end(); ?>
    </div>
</div>