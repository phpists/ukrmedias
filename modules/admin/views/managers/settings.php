<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use app\components\AdminActiveForm;
use app\components\Auth;
use app\components\BaseActiveRecord;

$this->params['breadcrumbs'] = [
    ['label' => 'Співробітники', 'url' => 'index'],
    $this->title = 'Налаштування ' . $model->getName(),
];
$form = AdminActiveForm::begin([
            'options' => ['class' => ''],
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}\n",
            ],
        ]);
?>
<label>Дозволені клієнти</label>
<div class="row">

    <?php foreach ($clients as $id => $title): ?>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <?php
            echo $form->field($model, 'firm_ids[' . $id . ']')->checkbox([
                'checked' => in_array($id, $model->settings['firms']),
                'label' => '<span class="badge border badge-pill col" title="' . Html::encode($title) . '" style="overflow:hidden;">' . $title . ' <i class="fa fa-check"></i></span>',
                'value' => $id]);
            ?>
        </div>
    <?php endforeach; ?>
</div>
<div class="fixed-column">
    <div class="form-group mt-5 mb-5">
        <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">зберегти</button>
    </div>
    <div class="form-group mb-5">
        <button class="btn btn-success btn-sm" type="submit" name="action" value="exit">зберегти та повернутись</button>
    </div>
    <div class="form-group mb-5">
        <a class="btn btn-info btn-sm" href="<?php echo yii\helpers\Url::toRoute('index') ?>">повернутись</a>
    </div>
</div>
<?php AdminActiveForm::end(); ?>