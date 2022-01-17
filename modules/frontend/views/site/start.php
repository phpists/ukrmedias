<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;
use app\components\assets\LoginAssets;

$this->title = 'Вхід до системи';
?>

<?php
LoginAssets::register($this);
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
<p class="p2">Введіть номер телефону</p>
<?php echo $form->field($model, 'email')->label(false)->input('text', ['id' => 'phone-input', 'class' => 'js-focus-on-load', 'data-phone-mask' => '1']); ?>
<input type="submit" value="Увійти" class="bgg">
<!--div>
    Не зареєстровані?
    <a href="<?php //echo Url::to(['/frontend/site/registration-step1']); ?>">Створіть обіковий запис</a>
</div-->
<?php AppActiveForm::end(); ?>


