<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\components\AdminActiveForm;
use app\models\Settings;

$this->params['breadcrumbs'] = [
    $this->title = 'Налаштування',
];
$form = AdminActiveForm::begin([
            'fieldConfig' => [
                'template' => "{label}{input}\n{error}\n",
            ]
        ]);
?>
<div class="row">
    <?php foreach (Settings::$sections as $dataList): ?>
        <div class="col-sm-12 col-md-6">
            <?php foreach ($dataList as $id): ?>
                <div class="row">
                    <div class="col-sm-12">
                        <label><?php echo Settings::$labels[$id]; ?></label>
                    </div>
                    <div class="col-sm-12">
                        <?php echo $form->field(Settings::$dataModels[$id], '[data][' . $id . ']value')->label(false)->input('text'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
<div class="row mt-5">
    <div class="col fixed-column">
        <div class="form-group mb-5">
            <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">зберегти</button>
        </div>
    </div>
</div>
<?php AdminActiveForm::end(); ?>