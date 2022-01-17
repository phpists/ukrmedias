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
<?php
if ($model->hasErrors('cmd')):
    echo $form->errorSummary($model, ['header' => false, 'footer' => false, 'class' => 'alert alert-danger']);
endif;
?>
<div class="row">
    <div class="col-sm-12">
        <?php echo DevicesCommands::$labels[DevicesCommands::CMD_INTERVAL]; ?>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-2">
        <?php echo $form->field($model, 'interval0')->label('Інтервал 1')->dropDownList(DevicesCommands::$interval_0); ?>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-2">
        <?php echo $form->field($model, 'interval1')->label('Інтервал 2')->dropDownList(DevicesCommands::$interval_1); ?>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-2">
        <?php echo $form->field($model, 'interval2')->label('Виборка')->dropDownList(DevicesCommands::$interval_2); ?>
    </div>
    <div class="col-sm-6 col-md-3 col-lg-2">
        <?php echo $form->datetime($model, 'date', ['class' => 'form-control', 'placeholder' => 'Відстрочка'])->label('&nbsp;'); ?>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <label>&nbsp;</label>
        <button class="btn btn-primary btn-sm" type="submit" name="cmd" value="<?php echo DevicesCommands::CMD_INTERVAL; ?>"
                data-confirm-click="<?php echo Html::encode('Ви підтверджуєте відправку команди "<b>' . DevicesCommands::$labels[DevicesCommands::CMD_INTERVAL] . '</b>" пристрою <i>' . $device->deveui . '</i> ?'); ?>">надіслати</button>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <label>&nbsp;</label>
        <div class="text-muted"><?php echo $model->getDateInterval(); ?></div>
    </div>
</div>
<?php AdminActiveForm::end(); ?>
<hr class="mb-4">

<?php $form = AdminActiveForm::begin(); ?>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-4">
        <?php echo DevicesCommands::$labels[DevicesCommands::CMD_SPRIDDING]; ?>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-2">
        <?php echo $form->field($model, 'spridding')->label(false)->dropDownList(DevicesCommands::$sf); ?>
    </div>
    <div class="col-sm-6 col-md-3 col-lg-2">
        <?php echo $form->datetime($model, 'date', ['class' => 'form-control', 'placeholder' => 'Відстрочка'])->label(false); ?>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <button class="btn btn-primary btn-sm" type="submit" name="cmd" value="<?php echo DevicesCommands::CMD_SPRIDDING; ?>"
                data-confirm-click="<?php echo Html::encode('Ви підтверджуєте відправку команди "<b>' . DevicesCommands::$labels[DevicesCommands::CMD_SPRIDDING] . '</b>" пристрою <i>' . $device->deveui . '</i> ?'); ?>">надіслати</button>
    </div>
    <div class="col-6 col-md-6 col-lg-4">
        <div class="text-muted"><?php echo $model->getDateSpridding(); ?></div>
    </div>
</div>
<?php AdminActiveForm::end(); ?>
<hr class="mb-4">

<?php $form = AdminActiveForm::begin(); ?>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-4">
        <?php echo DevicesCommands::$labels[DevicesCommands::CMD_PIN]; ?>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-2">
        <?php echo $form->field($model, 'pin')->label(false)->input('text'); ?>
    </div>
    <div class="col-sm-6 col-md-3 col-lg-2">
        <?php echo $form->datetime($model, 'date', ['class' => 'form-control', 'placeholder' => 'Відстрочка'])->label(false); ?>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <button class="btn btn-primary btn-sm" type="submit" name="cmd" value="<?php echo DevicesCommands::CMD_PIN; ?>"
                data-confirm-click="<?php echo Html::encode('Ви підтверджуєте відправку команди "<b>' . DevicesCommands::$labels[DevicesCommands::CMD_PIN] . '</b>" пристрою <i>' . $device->deveui . '</i> ?'); ?>">надіслати</button>
    </div>
    <div class="col-6 col-md-6 col-lg-4">
        <div class="text-muted"><?php echo $model->getDatePin(); ?></div>
    </div>
</div>
<?php AdminActiveForm::end(); ?>
<hr class="mb-4">

<?php $form = AdminActiveForm::begin(); ?>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-4">
        <?php echo DevicesCommands::$labels[DevicesCommands::CMD_MONTH]; ?>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-2">
        <?php echo $form->field($model, 'month')->label(false)->dropDownList(DevicesCommands::$months); ?>
    </div>
    <div class="col-sm-6 col-md-3 col-lg-2">
        <?php echo $form->datetime($model, 'date', ['class' => 'form-control', 'placeholder' => 'Відстрочка'])->label(false); ?>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <button class="btn btn-primary btn-sm" type="submit" name="cmd" value="<?php echo DevicesCommands::CMD_MONTH; ?>"
                data-confirm-click="<?php echo Html::encode('Ви підтверджуєте відправку команди "<b>' . DevicesCommands::$labels[DevicesCommands::CMD_MONTH] . '</b>" пристрою <i>' . $device->deveui . '</i> ?'); ?>">надіслати</button>
    </div>
    <div class="col-6 col-md-6 col-lg-4">
        <div class="text-muted"><?php echo $model->getDateMonth(); ?></div>
    </div>
</div>
<?php AdminActiveForm::end(); ?>
<hr class="mb-4">

<?php $form = AdminActiveForm::begin(); ?>
<div class="row">
    <div class="col-sm-12 col-md-9 col-lg-6">
        <?php echo DevicesCommands::$labels[DevicesCommands::CMD_QTY]; ?>
    </div>
    <div class="col-sm-6 col-md-3 col-lg-2">
        <?php echo $form->datetime($model, 'date', ['class' => 'form-control', 'placeholder' => 'Відстрочка'])->label(false); ?>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <button class="btn btn-primary btn-sm" type="submit" name="cmd" value="<?php echo DevicesCommands::CMD_QTY; ?>"
                data-confirm-click="<?php echo Html::encode('Ви підтверджуєте відправку команди "<b>' . DevicesCommands::$labels[DevicesCommands::CMD_QTY] . '</b>" пристрою <i>' . $device->deveui . '</i> ?'); ?>">надіслати</button>
    </div>
    <div class="col-6 col-md-6 col-lg-4">
        <div class="text-muted"><?php echo $model->getDateBytes(); ?></div>
    </div>
</div>
<?php AdminActiveForm::end(); ?>
