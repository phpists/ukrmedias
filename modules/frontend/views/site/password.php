<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;
use app\components\assets\LoginAssets;

$this->title = 'Пароль для входу в особистий кабінет';
?>

<?php
LoginAssets::register($this);
$form = AppActiveForm::begin();
?>
<h6>Вхід</h6>
<p class="p1">
    <?php if ($model->hasErrors()): ?>
    <div class="alert alert-danger">
        <?php echo Html::errorSummary($model, ['header' => '']); ?>
    </div>
<?php endif; ?>
</p>
<p class="p2">Введіть email</p>
<?php echo $form->field($model, 'password_new1')->label(false)->input('text', ['class' => 'form-default__input js-focus-on-load', 'placeholder' => 'пароль', 'autocomplete' => 'off']); ?>
<input type="submit" value="Добре" class="bgg">
<div>
    <a href="<?php echo Url::toRoute(['/frontend/site/password', 'id' => $model->id, 'key' => $model->key]); ?>" class="form-default__forgot">інший пароль</a>
</div>
<?php AppActiveForm::end(); ?>

