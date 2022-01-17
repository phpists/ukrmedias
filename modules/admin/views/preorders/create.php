<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Url;
use app\components\AdminActiveForm;
use app\components\Auth;
use app\components\BaseActiveRecord;

$this->params['breadcrumbs'] = [
    ['label' => 'Заявки', 'url' => 'index'],
    $this->title = 'Нова заявка',
];
$form = AdminActiveForm::begin();
?>
<div class="row">
    <div class="col-sm-12 col-md-4">
        <?php echo $form->autocomplete($model, 'firm_id', $firms, ['prompt' => '', 'onChange' => 'location.href="' . Url::to(['create']) . '?firm_id=".concat(this.value);']); ?>
    </div>
</div>
<div class="fixed-column">
    <div class="form-group mt-5 mb-5">
        <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">створити</button>
    </div>
    <div class="form-group mb-5">
        <a class="btn btn-info btn-sm" href="<?php echo yii\helpers\Url::toRoute('index') ?>">повернутись</a>
    </div>
</div>
<?php AdminActiveForm::end(); ?>