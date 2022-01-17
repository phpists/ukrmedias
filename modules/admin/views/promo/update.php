<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActiveForm;

$this->params['breadcrumbs'] = [
    ['label' => 'Акції', 'url' => 'index'],
    $this->title = $model->getRange(),
];
$form = AdminActiveForm::begin(['errorSummaryModels' => [$model]]);
?>

<div class="row">
    <div class="col-sm-12 col-md-3">
        <div class="form-group field-category-title required">
            <label class="control-label">Дата початку</label>
            <input type="text" class="form-control" value="<?php echo $model->getDateFrom(); ?>" disabled>
        </div>
    </div>
    <div class="col-sm-12 col-md-3">
        <div class="form-group field-category-title required">
            <label class="control-label">Дата закінчення</label>
            <input type="text" class="form-control" value="<?php echo $model->getDateTo(); ?>" disabled>
        </div>
    </div>
    <div class="col-sm-12 col-md-3">
        <?php $model->getModelFiles('cover')->fileField(); ?>
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