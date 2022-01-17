<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\components\AdminActiveForm;

$this->params['breadcrumbs'] = [
    ['label' => 'Слайдери', 'url' => 'index'],
    $this->title = $model->isNewRecord ? 'Новий слайд' : 'Редагування',
];
$form = AdminActiveForm::begin(['errorSummaryModels' => [$model]]);
?>

<div class="row">
    <div class="col-sm-12 col-md-9">
        <?php echo $form->field($model, 'place_id')->dropDownList($model::$placeLabels); ?>
    </div>
    <div class="col-sm-12 col-md-3">
        <?php echo $form->field($model, 'visible')->dropDownList($model::$valuesTrueFalse); ?>
    </div>
    <div class="col-sm-12 col-md-9">
        <?php echo $form->field($model, 'url')->input('text'); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-3">
        <?php echo $model->getModelFiles('photo')->fileField(); ?>
    </div>
</div>
<br/>
<div class="fixed-column">
    <div class="form-group mt-5 mb-5">
        <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">зберегти</button>
    </div>
    <div class="form-group mb-5">
        <button class="btn btn-success btn-sm" type="submit" name="action" value="exit">зберегти та повернутись</button>
    </div>
    <div class="form-group mb-5">
        <a class="btn btn-info btn-sm" href="<?php echo yii\helpers\Url::toRoute(['index']); ?>">повернутись</a>
    </div>
</div>
<?php AdminActiveForm::end(); ?>