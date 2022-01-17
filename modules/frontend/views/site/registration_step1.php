<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;

$this->title = 'Реєстрація';
?>

<?php
$form = AppActiveForm::begin();
?>
<h6>Реєстрація</h6>
<?php if ($model->hasErrors()): ?>
    <div class="alert alert-danger"><?php echo Html::errorSummary($model, ['header' => '']); ?></div>
<?php else: ?>
    <p class="p1">Зареєструйтесь, щоб розпочати роботу з порталом Ukrmedias.</p>
<?php endif; ?>
<h6>Дані контрагента</h6>
<p class="p6">Найменування юридичної особи або ФОП</p>
<?php echo $form->field($model, 'title')->label(false)->input('text', ['class' => 'js-focus-on-load']); ?>
<p class="p6">Контактний телефон</p>
<?php echo $form->field($model, 'phone')->label(false)->input('text', ['class' => 'js-focus-on-load', 'data-phone-mask' => '1']); ?>
<input type="submit" value="Продовжити" class="bgg">
<div>
    Вже зареєстровані?
    <a href="<?php echo Url::toRoute('/frontend/site/index'); ?>">Увійдіть</a>
</div>
<?php AppActiveForm::end(); ?>
