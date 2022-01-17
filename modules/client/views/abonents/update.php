<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;
use app\models\AddrFlats;
use app\models\Users;

$this->params['breadcrumbs'] = [
    ['label' => 'Адреси', 'url' => '/client/houses/index'],
    ['label' => "Будинок {$house->no}", 'url' => ['/client/houses/details', 'id' => $house->id, 'aid' => $service_id]],
];
if ($flat instanceof AddrFlats) {
    $this->params['breadcrumbs'][] = ['label' => "{$flat->no}", 'url' => ['/client/flats/details', 'id' => $flat->id, 'aid' => $service_id]];
}
$this->title = $model->isNewRecord ? 'Новий абонент' : $model->getTitle();
?>
<div class="column ">
    <div class="columns is-multiline columns-botoom--double">
        <div class="column is-flex-center add-user ">
            <div class="my-address">
                <div class="h3">Абонент за адресою:</div>
                <div class="text-dark"><?php echo $house->getTitle(); ?></div>
            </div>
            <?php $form = AppActiveForm::begin(['errorSummaryModels' => $model]); ?>
            <div class="form-default__row">
                <?php echo $form->autocomplete($model, 'flat_id', $flats, ['class' => 'js-dropdown-select2 form-default__select', 'prompt' => '']); ?>
            </div>
            <div class="form-default__row">
                <?php echo $form->field($model, 'type_id')->label(null, ['class' => 'form-default__label'])->dropDownList($model::$typeLabels, ['class' => 'js-dropdown-select form-default__select', 'onChange' => 'changeUserType(this)']); ?>
            </div>
            <div class="js_type_<?php echo Users::TYPE_PRIVATE_PERSON; ?>" style="<?php if ($model->type_id != Users::TYPE_PRIVATE_PERSON): ?>display:none;<?php endif; ?>">
                <div class="form-default__row">
                    <?php echo $form->field($model, 'second_name')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
                </div>
                <div class="form-default__row">
                    <?php echo $form->field($model, 'first_name')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
                </div>
                <div class="form-default__row">
                    <?php echo $form->field($model, 'middle_name')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
                </div>
            </div>
            <div class="form-default__row js_type_<?php echo Users::TYPE_LEGAL_PERSON; ?>" style="<?php if ($model->type_id != Users::TYPE_LEGAL_PERSON): ?>display:none;<?php endif; ?>">
                <div class="">
                    <?php echo $form->field($model, 'title')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
                </div>
            </div>
            <div class="form-default__row">
                <?php echo $form->field($model, 'email')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
            </div>
            <div class="form-default__row">
                <?php echo $form->field($model, 'phone')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'placeholder' => '+38']); ?>
            </div>
            <div class="form-default__row checkbox-wrap">
                <?php
                echo Html::activeCheckbox($model, 'invite', [
                    'class' => 'form-default__checkbox',
                    'checked' => $model->isNewRecord && !is_numeric($model->invite) || $model->invite,
                    'label' => false,
                ]);
                echo Html::activeLabel($model, 'invite');
                ?>
            </div>
            <div class="calc-buttons-group">
                <div class="">
                    <?php if ($model->isNewRecord): ?>
                        <a href="<?php echo Url::toRoute(['/client/houses/details', 'id' => $house->id, 'aid' => $service_id]); ?>" class="close btn btn-profile bg-white">&larr; Назад</a>
                    <?php else: ?>
                        <a href="<?php echo Url::toRoute(['/client/flats/details', 'id' => $model->flat_id, 'aid' => $service_id]); ?>" class="close btn btn-profile bg-white">&larr; Назад</a>
                    <?php endif; ?>
                </div>
                <div>
                    <button class="btn btn-profile btn-blue" name="action" value="save"><svg class="icon icon-check"><use xlink:href="img/sprite.svg#icon-check"></use></svg> Зберегти</button>
                </div>
            </div>
            <?php AppActiveForm::end(); ?>
        </div>
    </div>
</div>