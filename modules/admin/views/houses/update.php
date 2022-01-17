<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActiveForm;
use app\models\DataHelper;

$this->params['breadcrumbs'] = [
    ['label' => 'Довідник "Будинки"', 'url' => 'index'],
    $this->title = 'Профіль будинку ' . $model->no,
];
$form = AdminActiveForm::begin(['errorSummaryModels' => $model]);
?>
<div class="row">
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->autocomplete($model, 'region_id', $regions, ['prompt' => 'обласний центр', 'onChange' => 'changeRegion(this)']); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->autocomplete($model, 'area_id', $areas, ['prompt' => empty($this->region_id) ? '-' : 'районний центр', 'onChange' => 'loadCities(document.getElementById("addrhouses-region_id"), this, "#addrhouses-city_id")']); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->autocomplete($model, 'city_id', $cities, ['prompt' => '', 'onChange' => 'loadStreets(this, "#addrhouses-street_id")']); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->field($model, 'post_index')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->autocomplete($model, 'street_id', $streets); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->field($model, 'no')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-lg-3">
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->field($model, 'flats_qty')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->field($model, 'floors')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->field($model, 'square')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->field($model, 'heat_square')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->field($model, 'common_square')->input('text'); ?>
    </div>
</div>
<div class="row mt-5">
    <div class="col fixed-column">
        <div class="form-group mb-5">
            <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">зберегти</button>
        </div>
        <div class="form-group mb-5">
            <button class="btn btn-success btn-sm" type="submit" name="action" value="exit">зберегти та повернутись</button>
        </div>
        <div class="form-group mb-5">
            <a class="btn btn-info btn-sm" href="<?php echo Url::toRoute('index') ?>">повернутись</a>
        </div>
    </div>
</div>
<?php if (!$model->isNewRecord): ?>
    <hr/>
    Видалення приміщень будинку:
    <div class="row mb-5">
        <div class="col-sm-12 col-md-4">
            <a class="btn btn-dark btn-sm mt-3" href="<?php echo Url::toRoute(['delete-flats', 'id' => Yii::$app->security->hashData(json_encode(['id' => $model->id, 'time' => time()]), DataHelper::HASH_KEY)]); ?>" data-confirm-click="Разом з приміщеннями будуть видалені пов`язані з ними дані про споживання, рахунки, події та параметри для розрахунків. Після видалення будуть втрачені прив`язки Lora-пристроїв та приладів обліку.<br/>Ви підтверджуєте видалення всіх приміщень адреси '<i><?php echo Html::encode($model->getTitle()); ?></i>' ?">видалити все</a>
        </div>
        <div class="col-sm-12 col-md-4">
            <a class="btn btn-dark btn-sm mt-3" href="<?php echo Url::toRoute(['delete-flats-safe', 'id' => Yii::$app->security->hashData(json_encode(['id' => $model->id, 'time' => time()]), DataHelper::HASH_KEY)]); ?>" data-confirm-click="Будуть видалені всі приміщення, які не мають пов'язаних Lora-пристроїв та приладів обліку. Разом з приміщеннями будуть видалені пов`язані з ними старі дані про споживання, рахунки, події та параметри для розрахунків.<br/>Ви підтверджуєте видалення приміщень адреси '<i><?php echo Html::encode($model->getTitle()); ?></i>' ?">безпечне видалення</a>
        </div>
    </div>
<?php endif; ?>
<?php AdminActiveForm::end(); ?>
<script>
    function changeRegion(input) {
        loadAreas(input, "#addrhouses-area_id", function () {
            loadCities(input, document.getElementById("addrhouses-area_id"), "#addrhouses-city_id", "#addrhouses-street_id");
        });
    }
</script>