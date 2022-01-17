<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActiveForm;

$this->params['breadcrumbs'] = [
    ['label' => 'Торгові марки', 'url' => 'index'],
    $this->title = $model->title,
];
$form = AdminActiveForm::begin(['errorSummaryModels' => [$model]]);
?>

<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="form-group field-category-title required">
            <label class="control-label">Найменування</label>
            <input id="brands-title" type="text" class="form-control" value="<?php echo $model->title; ?>" disabled>
            <?php echo $form->field($model, 'id')->label(false)->hiddenInput(); ?>
        </div>
    </div>
    <div class="col-sm-12 col-md-3">
        <?php $model->getModelFiles('logo')->fileField(); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="form-group required">
            <label class="control-label"><?php echo $model->getCpuModel()->getAttributeLabel('cpu'); ?></label>
            <div class="input-group">
                <?php echo Html::activeTextInput($model->getCpuModel(), 'cpu', ['class' => 'form-control']); ?>
                <div class="input-group-append">
                    <span class="input-group-text cursor" onClick="createCpu('#brands-title', '#cpu-cpu');">@</span>
                </div>
            </div>
        </div>
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
        <a class="btn btn-info btn-sm" href="<?php echo yii\helpers\Url::to('index') ?>">повернутись</a>
    </div>
</div>
<?php AdminActiveForm::end(); ?>