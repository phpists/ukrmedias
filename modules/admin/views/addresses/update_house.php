<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActiveForm;
use app\models\AddrFlats;
?>
<?php
$this->params['breadcrumbs'] = [
    ['label' => 'Абоненти', 'url' => 'index'],
    $this->title = $model->getTitle(),
];
$form = AdminActiveForm::begin(['errorSummaryModels' => $model]);
?>
<div class="row">
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->field($model, 'floors')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->field($model, 'square')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->field($model, 'heat_square')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->field($model, 'common_square')->input('text'); ?>
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
        <a class="btn btn-info btn-sm" href="<?php echo Url::toRoute(['index']); ?>">повернутись</a>
    </div>
</div>
<?php AdminActiveForm::end(); ?>