<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\components\AdminActiveForm;
use app\models\AddrFlats;

$this->params['breadcrumbs'] = [
    ['label' => 'Приміщення', 'url' => 'index'],
    $this->title = 'Профіль ' . $model->no,
];
$form = AdminActiveForm::begin(['errorSummaryModels' => $model]);
?>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->autocomplete($model, 'region_id', $regions, ['prompt' => 'обласний центр', 'onChange' => 'changeRegion(this)']); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->autocomplete($model, 'area_id', $areas, ['prompt' => empty($this->region_id) ? '-' : 'районний центр', 'onChange' => 'loadCities(document.getElementById("addrflats-region_id"), this, "#addrflats-city_id")']); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->autocomplete($model, 'city_id', $cities, ['prompt' => '', 'onChange' => 'changeCity(this)']); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->autocomplete($model, 'street_id', $streets, ['onChange' => 'changeStreet(this)']); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-2">
        <?php echo $form->autocomplete($model, 'house_id', $houses); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-4">
        <?php echo $form->field($model, 'no')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->field($model, 'square')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->field($model, 'heat_metering')->dropDownList(AddrFlats::$heatMeteringLabels); ?>
    </div>

    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->field($model, 'place_k')->input('text'); ?>
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
        loadAreas(input, "#addrflats-area_id", function () {
            loadCities(input, document.getElementById("addrflats-area_id"), "#addrflats-city_id", "#addrflats-street_id");
        });
    }

    function changeCity(input) {
        loadStreets(input, "#addrflats-street_id", "#addrflats-house_id");
    }
    function changeStreet(input) {
        loadHouses(input, "#addrflats-house_id");
    }
</script>