<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;
use app\components\assets\LoginAssets;

$this->title = 'Реєстрація';
?>

<?php
LoginAssets::register($this);
$form = AppActiveForm::begin();
?>
<h6>Реєстрація</h6>
<?php if ($model->hasErrors()): ?>
    <div class="alert alert-danger"><?php echo Html::errorSummary($model, ['header' => '']); ?></div>
<?php else: ?>
    <p class="p1">Зареєструйтесь, щоб розпочати роботу з порталом Ukrmedias.</p>
<?php endif; ?>
<a href="<?php echo Url::to(['registration-step1']); ?>">
    <picture><source srcset="img/login/left.svg" type="image/webp"><img src="img/login/left.svg" alt=""></picture>
    Назад
</a>
<h6>Персональні дані</h6>
<p class="p6">Номер телефону</p>
<?php echo $form->field($model, 'phone')->label(false)->input('text', ['class' => 'js-focus-on-load', 'data-phone-mask' => '1']); ?>
<p class="p6">Прізвище</p>
<?php echo $form->field($model, 'first_name')->label(false)->input('text', ['class' => '']); ?>
<p class="p6">Ім’я</p>
<?php echo $form->field($model, 'second_name')->label(false)->input('text', ['class' => '']); ?>
<input type="submit" value="Зареєструватись" class="bgg">
<div>
    Вже зареєстровані?
    <a href="<?php echo Url::toRoute('/frontend/site/index'); ?>">Увійдіть</a>
</div>
<?php AppActiveForm::end(); ?>

