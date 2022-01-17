<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Url;
use app\components\AdminActiveForm;

$this->params['breadcrumbs'] = [
    ['label' => 'Шлюзи', 'url' => 'index'],
    $this->title = 'Профіль ' . $model->mac,
];
$form = AdminActiveForm::begin(['errorSummaryModels' => $model]);
?>
<div class="row">
    <div class="col-sm-12 col-md-6">
        <?php echo $form->field($model, 'mac')->input('text', ['readonly' => !$model->isNewRecord, 'onChange' => $model->isNewRecord ? 'getGatewayInfo()' : null, 'data-url' => Url::toRoute('getinfo')]); ?>
        <?php echo $form->field($model, 'tx_rfch')->input('text'); ?>
        <?php echo $form->field($model, 'desc')->input('text'); ?>
    </div>
</div>
<div class="row mt-5">
    <div class="col fixed-column">
        <div class="form-group mb-5">
            <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">зберегти</button>
        </div>
        <div class="form-group mb-5">
            <button class="btn btn-success btn-sm" type="submit" name="action" value="exit">зберегти та повернутись</button>
        </div>
        <div class="form-group mb-5">
            <a class="btn btn-info btn-sm" href="<?php echo yii\helpers\Url::toRoute('index') ?>">повернутись</a>
        </div>
    </div>
</div>
<?php AdminActiveForm::end(); ?>