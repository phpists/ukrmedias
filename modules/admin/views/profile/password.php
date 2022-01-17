<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use app\components\AdminActiveForm;

$this->params['breadcrumbs'] = [
    $this->title = 'Зміна паролю',
];
if ($model->hasErrors()):
    ?>
    <div class="alert alert-danger"><?php echo Html::errorSummary($model, ['header' => '']); ?></div>
    <?php
endif;
$form = AdminActiveForm::begin([
            'errorSummaryModels' => $model,
            'options' => ['class' => 'fixed-column'],
        ])
?>
<?php echo $form->field($model, 'password')->label('Поточний пароль')->input('password', ['autocomplete' => 'off']); ?>
<?php echo $form->field($model, 'password_new1')->input('password', ['autocomplete' => 'off']); ?>
<?php echo $form->field($model, 'password_new2')->input('password', ['autocomplete' => 'off']); ?>
<div class="form-group mt-5 mb-5">
    <button class="btn btn-primary btn-sm" type="submit">зберегти</button>
</div>
<div class="form-group mb-5">
    <a class="btn btn-secondary btn-sm" href="<?php echo yii\helpers\Url::toRoute('update') ?>">повернутись</a>
</div>
<?php AdminActiveForm::end(); ?>


