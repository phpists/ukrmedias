<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;

$this->title = 'Вхід до системи';
?>

<?php
$form = AppActiveForm::begin();
?>
<h6>Вхід</h6>
<p class="p1">
    <?php if ($model->hasErrors()): ?>
    <div class="alert alert-danger"><?php echo Html::errorSummary($model, ['header' => '']); ?></div>
<?php else: ?>
    Для роботи з порталом Ukrmedias необхідна авторизація.
<?php endif; ?>
</p>
<p class="p2">Введіть номер телефону або email</p>
<?php echo $form->field($model, 'email')->label(false)->input('text', ['class' => 'js-focus-on-load']); ?>
<?php echo $form->field($model, 'password')->label(false)->input('password', ['class' => '', 'placeholder' => 'пароль']); ?>
<input type="submit" value="Увійти" class="bgg">
<div>
    <a href="<?php echo Url::toRoute('/frontend/site/restore'); ?>" class="">Забули пароль?</a>
</div>
<?php AppActiveForm::end(); ?>


