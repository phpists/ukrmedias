<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\components\AdminActiveForm;

$this->params['breadcrumbs'] = [
    ['label' => 'Довідник "Вулиці"', 'url' => 'index'],
    $this->title = 'Профіль ' . $model->title,
];
$form = AdminActiveForm::begin(['errorSummaryModels' => $model]);
?>
<div class="row">
    <div class="col-sm-12 col-md-6">
        <?php echo $form->autocomplete($model, 'region_id', $regions, ['prompt' => 'обласний центр', 'onChange' => 'changeRegion(this)']); ?>
        <?php echo $form->autocomplete($model, 'area_id', $areas, ['prompt' => empty($this->region_id) ? '-' : 'районний центр', 'onChange' => 'loadCities(document.getElementById("addrstreets-region_id"), this, "#addrstreets-city_id")']); ?>
        <?php echo $form->autocomplete($model, 'city_id', $cities); ?>
        <?php echo $form->field($model, 'title')->input('text'); ?>
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
            <a class="btn btn-info btn-sm" href="<?php echo yii\helpers\Url::toRoute('index') ?>">повернутись</a>
        </div>
    </div>
</div>
<?php AdminActiveForm::end(); ?>
<script>
    function changeRegion(input) {
        loadAreas(input, "#addrstreets-area_id", function () {
            loadCities(input, document.getElementById("addrstreets-area_id"), "#addrstreets-city_id");
        });
    }
</script>