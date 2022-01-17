<?php

use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\components\AdminActiveForm;
use app\models\Services;
?>
<?php
$this->params['breadcrumbs'] = [
    ['label' => 'Абоненти', 'url' => 'index'],
    $this->title = 'Трансфер адреси',
];
$form = AdminActiveForm::begin(['errorSummaryModels' => $model]);
?>
<div class="alert alert-warning mb-3">
    Трансфер адреси: <b><?php echo $model->getTitle(); ?></b>
    <div>Разом з будинком будуть перенесені приміщення, прилади обліку, події, налаштування для розрахунків.</div>
</div>
<div class="row mb-3">
    <div class="col-sm-12 col-md-6 col-lg-4">
        Поточний клієнт
    </div>
    <div class="col-sm-12 col-md-6 col-lg-4">
        Новий клієнт
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-4">
        <?php
        foreach ($clients as $dataModel):
            unset($clientsKeyval[$dataModel->id]);
            ?>
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    $label = $dataModel->getTitle() . '<div class="text-small text-muted">' . implode(', ', array_intersect_key(Services::$labels, array_flip($dataModel->houseServices))) . '</div>';
                    echo $form->field($model, 'current_client_id')->radio(['value' => $dataModel->id, 'label' => $label, 'uncheck' => null, 'checked' => count($clients) === 1]);
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-4">
        <?php echo $form->autocomplete($model, 'new_client_id', $clientsKeyval, ['prompt' => 'Клієнт', 'label' => false]); ?>
    </div>
</div>
<div class="fixed-column">
    <div class="form-group mt-5 mb-5">
        <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">зберегти</button>
    </div>
    <div class="form-group mb-5">
        <button class="btn btn-success btn-sm" type="submit" name="action" value="exit">зберегти та повернутись</button>
    </div>
    <div class="form-group mb-5">
        <a class="btn btn-info btn-sm" href="<?php echo Url::toRoute(['index']); ?>">повернутись</a>
    </div>
</div>
<?php AdminActiveForm::end(); ?>