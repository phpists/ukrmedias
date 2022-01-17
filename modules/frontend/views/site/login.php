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
<a href="<?php echo Url::to(['index']); ?>">
    <picture><source srcset="img/login/left.svg" type="image/webp"><img src="img/login/left.svg" alt=""></picture>
    Назад
</a>
<h6>Введіть пароль</h6>

<p class="p4">Пароль для входу було відправлено на:</p>
<h6><?php echo $data; ?></h6>

<p class="p5">Введіть пароль (для сесії №<?php echo $sid; ?>)</p>
<div>
    <?php echo Html::textInput('password', '', ['autocomplete' => 'off','class'=>'js-focus-on-load']); ?>
    <picture><source srcset="img/login/eye.svg" type="image/webp"><img src="img/login/eye.svg" alt=""></picture>
</div>
<input type="submit" value="Увійти" class="bgr">
<a href="<?php echo Url::to(['index']); ?>" class="again">
    <picture><source srcset="img/login/again.svg" type="image/webp"><img src="img/login/again.svg" alt=""></picture>
    Отримати пароль ще раз
</a>
<?php AppActiveForm::end(); ?>
