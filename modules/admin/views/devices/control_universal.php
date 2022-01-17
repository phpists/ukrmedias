<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\components\AdminActiveForm;
use app\models\DevicesCommands;
use yii\helpers\Html;

$this->params['breadcrumbs'] = [
    ['label' => 'Пристрої', 'url' => 'index'],
    $this->title = 'Керування ' . $device->deveui,
];
?>

<?php $form = AdminActiveForm::begin(); ?>
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-4">
        <?php echo DevicesCommands::$labels[DevicesCommands::CMD_INTERVAL_UNIVERSAL]; ?>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-2">
        <?php echo $form->field($model, 'interval0')->label('Інтервал')->dropDownList(DevicesCommands::$interval_universal); ?>
    </div>
    <div class="col-sm-6 col-md-3 col-lg-2">
        <?php echo $form->datetime($model, 'date', ['class' => 'form-control', 'placeholder' => ''])->label('Відстрочка'); ?>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <label>&nbsp;</label>
        <button class="btn btn-primary btn-sm" type="submit" name="cmd" value="<?php echo DevicesCommands::CMD_INTERVAL_UNIVERSAL; ?>"
                data-confirm-click="<?php echo Html::encode('Ви підтверджуєте відправку команди "<b>' . DevicesCommands::$labels[DevicesCommands::CMD_INTERVAL_UNIVERSAL] . '</b>" пристрою <i>' . $device->deveui . '</i> ?'); ?>">надіслати</button>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <label>Остання дія</label>
        <div class="text-muted"><?php echo $model->getDateInterval(); ?></div>
    </div>
</div>
<?php AdminActiveForm::end(); ?>
<hr class="mb-4">

<?php $form = AdminActiveForm::begin(); ?>
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-4">
        <?php echo DevicesCommands::$labels[DevicesCommands::CMD_STATUS_UNIVERSAL]; ?>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-2">
    </div>
    <div class="col-sm-6 col-md-3 col-lg-2">
        <?php echo $form->datetime($model, 'date', ['class' => 'form-control', 'placeholder' => ''])->label('Відстрочка'); ?>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <label>&nbsp;</label>
        <button class="btn btn-primary btn-sm" type="submit" name="cmd" value="<?php echo DevicesCommands::CMD_STATUS_UNIVERSAL; ?>"
                data-confirm-click="<?php echo Html::encode('Ви підтверджуєте відправку команди "<b>' . DevicesCommands::$labels[DevicesCommands::CMD_STATUS_UNIVERSAL] . '</b>" пристрою <i>' . $device->deveui . '</i> ?'); ?>">надіслати</button>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <label>Остання дія</label>
        <div class="text-muted"><?php echo $model->getDateBytes(); ?></div>
    </div>
</div>
<?php AdminActiveForm::end(); ?>
<hr class="mb-4">

<?php $form = AdminActiveForm::begin(); ?>
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-4">
        <?php echo DevicesCommands::$labels[DevicesCommands::CMD_VERSION_UNIVERSAL]; ?>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-2">
    </div>
    <div class="col-sm-6 col-md-3 col-lg-2">
        <?php echo $form->datetime($model, 'date', ['class' => 'form-control', 'placeholder' => ''])->label('Відстрочка'); ?>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <label>&nbsp;</label>
        <button class="btn btn-primary btn-sm" type="submit" name="cmd" value="<?php echo DevicesCommands::CMD_VERSION_UNIVERSAL; ?>"
                data-confirm-click="<?php echo Html::encode('Ви підтверджуєте відправку команди "<b>' . DevicesCommands::$labels[DevicesCommands::CMD_VERSION_UNIVERSAL] . '</b>" пристрою <i>' . $device->deveui . '</i> ?'); ?>">надіслати</button>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <label>Остання дія</label>
        <div class="text-muted"><?php echo $model->getDatePin(); ?></div>
    </div>
</div>
<?php AdminActiveForm::end(); ?>
