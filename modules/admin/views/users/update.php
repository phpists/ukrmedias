<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use app\components\AdminActiveForm;
use app\components\Auth;
use app\components\BaseActiveRecord;

$this->params['breadcrumbs'] = [
    ['label' => 'Співробітники клієнтів', 'url' => 'index'],
    $this->title = $model->isNewRecord ? 'Профіль' : $model->getName(),
];
$form = AdminActiveForm::begin(['errorSummaryModels' => $model]);
?>
<div class="row">
    <div class="col-sm-12 col-md-4">
        <?php echo $form->field($model, 'firm_id')->dropDownList($firms, ['prompt' => '']); ?>
    </div>
    <div class="col-sm-12 col-md-4">
        <?php echo $form->field($model, 'role_id')->dropDownList(Auth::$roleLabelsClient); ?>
    </div>
    <div class="col-sm-12 col-md-4">
        <?php echo $form->field($model, 'active')->dropDownList($model::$statusLabels); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-4">
        <?php echo $form->field($model, 'second_name')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-4">
        <?php echo $form->field($model, 'first_name')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-4">
        <?php echo $form->field($model, 'middle_name')->input('text'); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-4">
        <?php echo $form->field($model, 'email')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-4">
        <?php echo $form->field($model, 'phone')->input('text', ['placeholder' => '+38']); ?>
    </div>
</div>
<div class="fixed-column">
    <div class="form-group mt-5 mb-5">
        <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">зберегти</button>
    </div>
    <div class="form-group mb-5">
        <button class="btn btn-success btn-sm" type="submit" name="action" value="exit">зберегти та повернутись</button>
    </div>
    <div class="form-group mb-5">
        <a class="btn btn-info btn-sm" href="<?php echo yii\helpers\Url::toRoute('index') ?>">повернутись</a>
    </div>
</div>
<?php AdminActiveForm::end(); ?>