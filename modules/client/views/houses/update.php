<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;
use app\models\Services;

$this->params['breadcrumbs'] = [
    ['label' => 'Адреси', 'url' => 'index'],
];
$this->title = 'Профіль адреси';
?>
<div class="main-template-employ">
    <div class="h3 main-template__header"><?php echo $this->title; ?></div>
    <div class="m24"><?php echo $model->getTitle(); ?></div>
    <div class="feedback__wrap ">
        <?php $form = AppActiveForm::begin(['errorSummaryModels' => $model]); ?>
        <div class="form-default__row">
            <?php echo $form->field($model, 'clientNote')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
        </div>
        <div class="title-form h4">Послуги</div>
        <?php
        foreach (Services::$labels as $service_id => $label):
            if (!in_array($service_id, $services)) {
                continue;
            }
            ?>
            <div class="form-default__row">
                <?php
                echo Html::activeCheckbox($model, "services_ids[{$service_id}]", [
                    'class' => 'form-default__checkbox',
                    'label' => false,
                ]);
                ?>
                <?php
                echo Html::activeLabel($model, "services_ids[{$service_id}]", [
                    'class' => 'form-default__checkbox-label',
                    'label' => $label,
                ]);
                ?>
            </div>
        <?php endforeach; ?>
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
</div>