<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\AppActiveForm;

$this->title = 'Профіль';
?>
<div class="table">
    <div class="profile">
        <?php $form = AppActiveForm::begin(); ?>
        <div class="item">
            <h2>Персональна інформація</h2>
            <h5>Редагуйте свої персональні дані.</h5>
            <p>Прізвище</p>
            <?php echo $form->field($model, 'second_name')->label(false)->input('text', ['class' => '']); ?>
            <p>Ім'я</p>
            <?php echo $form->field($model, 'first_name')->label(false)->input('text', ['class' => '']); ?>
            <p>По-батькові</p>
            <?php echo $form->field($model, 'middle_name')->label(false)->input('text', ['class' => '']); ?>
            <p>Номер телефону</p>
            <?php echo $form->field($model, 'phone')->label(false)->input('text', ['data-phone-mask' => '1']); ?>
            <p>Email</p>
            <?php echo $form->field($model, 'email')->label(false)->input('text', ['class' => '']); ?>
        </div>
        <div class="item">
            <h2>Адреса доставки за замовчуванням</h2>
            <h5>Встановіть адресу доставки, яка буде обрана у новому замовленні автоматично.</h5>
            <?php echo Html::activeHiddenInput($firm, 'default_addr_id', ['value' => '', 'label' => false]); ?>
            <?php foreach ($addresses as $id => $title): ?>
                <label class="radio">
                    <?php
                    echo Html::activeRadio($firm, 'default_addr_id', [
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
            <h2>Доставка за замовчуванням</h2>
            <h5>Встановіть варіант доставки, який буде обраним у новому замовленні автоматично.</h5>
            <?php echo Html::activeHiddenInput($firm, 'default_delivery_id', ['value' => '', 'label' => false]); ?>
            <?php foreach ($deliveryVariants as $id => $title): ?>
                <label class="radio">
                    <?php
                    echo Html::activeRadio($firm, 'default_delivery_id', [
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
            <h2>Оплата за замовчуванням</h2>
            <h5>Встановіть варіант оплати за товар, який буде обраним у новому замовленні автоматично.</h5>
            <?php echo Html::activeHiddenInput($firm, 'default_payment_id', ['value' => '', 'label' => false]); ?>
            <?php foreach ($paymentVariants as $id => $title): ?>
                <label class="radio">
                    <?php
                    echo Html::activeRadio($firm, 'default_payment_id', [
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