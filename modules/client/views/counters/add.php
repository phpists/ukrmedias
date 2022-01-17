<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Url;
use app\components\AppActiveForm;
use app\models\AccessLogic;
use app\models\Services;

app\components\assets\DatepickerAssets::register($this);
if ($flat_id > 0):
    $this->params['breadcrumbs'] = [
        ['label' => 'Адреси', 'url' => '/client/houses/index'],
        ['label' => "Будинок {$house->no}", 'url' => ['/client/houses/details', 'id' => $house->id, 'aid' => $service_id]],
    ];
else:
    $this->params['breadcrumbs'] = [
        ['label' => 'Адреси', 'url' => '/client/houses/index'],
    ];
endif;
$this->title = 'Новий лічильник';
?>
<div class="main-template-employ">
    <div class="employ-edit-title">
        <div class="h3"><?php echo $this->title; ?></div>
    </div>
    <?php $form = AppActiveForm::begin(['errorSummaryModels' => $model]); ?>
    <div class="calc-column">
        <div class="columns is-multiline columns-botoom--double">
            <div class="column is-6-desktop is-6-tablet is-12-mobile">
                <div class="form-default__row">
                    <?php echo $house->getTitle(); ?>
                </div>
                <div class="form-default__row">
                    <?php echo $form->autocomplete($model, 'flat_id', $flats, ['class' => 'js-dropdown-select2 form-default__select', 'prompt' => '']); ?>
                </div>
                <div class="form-default__row">
                    <?php
                    echo $form->field($model, 'service_id')->label(null, ['class' => 'form-default__label'])
                            ->dropDownList($services, ['class' => 'js-dropdown-select form-default__select', 'prompt' => '', 'onChange' => 'initBeginField();']);
                    ?>
                </div>
                <div class="form-default__row">
                    <?php echo $form->field($model, 'number')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
                </div>
                <div class="form-default__row">
                    <?php echo $form->field($model, 'model')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
                </div>
                <?php if (AccessLogic::isLoginAs()): ?>
                    <div class="form-default__row">
                        <?php
                        $label = "{$model->getAttributeLabel('begin')}"
                                . '<span style="display:none;" class="js_begin_' . Services::ID_HEAT . '">, ' . Services::$units[Services::ID_HEAT] . '</span>'
                                . '<span style="display:none;" class="js_begin_' . Services::ID_COOL_WATER . ' js_begin_' . Services::ID_HOT_WATER . '">, ' . Services::$units[Services::ID_COOL_WATER] . '</span>';
                        echo $form->field($model, 'begin')->label($label, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'placeholder' => '0.0000']);
                        ?>
                    </div>
                <?php endif; ?>
                <div class="form-default__row">
                    <?php echo $form->field($model, 'date')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'data-default-time' => $model->date]); ?>
                </div>
            </div>
        </div>
        <div class="calc-buttons-group">
            <div class="">
                <a href="<?php echo $returnUrl; ?>" class="close btn btn-profile bg-white">&larr; Назад</a>
            </div>
            <div class="">
                <button class="btn btn-profile btn-blue" aria-haspopup="true" name="action" value="save"><svg class="icon icon-check"><use xlink:href="img/sprite.svg#icon-check"></use></svg> Зберегти</button>
            </div>
        </div>
    </div>
    <?php AppActiveForm::end(); ?>
</div>

