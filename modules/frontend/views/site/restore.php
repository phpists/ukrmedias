<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;

$this->title = 'Запит на відновлення паролю';
?>

<?php
$form = AppActiveForm::begin();
?>
<h6>Вхід</h6>
<p class="p1">
    <?php if ($model->hasErrors()): ?>
    <div class="alert alert-danger"><?php echo Html::errorSummary($model, ['header' => '']); ?></div>
<?php endif; ?>
</p>
<p class="p2">Введіть email</p>
<?php echo $form->field($model, 'email')->label(false)->input('text', ['class' => 'js-focus-on-load', 'placeholder' => '']); ?>
<input type="submit" value="Надіслати" class="bgg">
<div>
    <a href="<?php echo Url::toRoute('/frontend/site/login'); ?>" class="">скасувати</a>
</div>
<?php AppActiveForm::end(); ?>

