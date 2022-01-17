<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use app\components\Auth;
use app\components\AdminActiveForm;

$this->params['breadcrumbs'] = [
    $this->title = 'Мій профіль',
];
$form = AdminActiveForm::begin(['errorSummaryModels' => $model]);
?>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-4">
        <?php echo $form->field($model, 'second_name')->input('text'); ?>
        <?php echo $form->field($model, 'first_name')->input('text'); ?>
        <?php echo $form->field($model, 'middle_name')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-4">
        <?php echo $form->field($model, 'email')->input('text'); ?>
        <?php echo $form->field($model, 'phone')->input('text', ['placeholder' => '+38']); ?>
    </div>
</div>
<div class="row mt-5">
    <div class="col fixed-column">
        <div class="form-group mb-5">
            <button class="btn btn-primary btn-sm" type="submit">зберегти</button>
        </div>
        <?php if (Yii::$app->user->can(Auth::ROLE_DEVELOPER)): ?>
            <div class="form-group mb-5">
                <a class="btn btn-info btn-sm" href="<?php echo yii\helpers\Url::toRoute('password') ?>">змінити пароль</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php AdminActiveForm::end(); ?>