<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActiveForm;
use app\components\Auth;
use app\models\Devices;
use app\models\DataHelper;
use app\models\Services;

$this->params['breadcrumbs'] = [
    ['label' => 'Пристрої', 'url' => 'index'],
    $this->title = 'Профіль ' . $model->deveui,
];
$form = AdminActiveForm::begin(['errorSummaryModels' => $model]);
?>
<?php if ($model->isNewRecord && Yii::$app->user->identity->role_id == Auth::ROLE_ADMIN): ?>
    <div class="alert alert-info">Додати пристрій можливо, якщо Клієнт надає відповідну послугу за обраною адресою.</div>
<?php endif; ?>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->autocomplete($model, 'region_id', $regions, ['prompt' => 'обласний центр', 'onChange' => 'changeRegion(this)']); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->autocomplete($model, 'area_id', $areas, ['prompt' => empty($this->region_id) ? '-' : 'районний центр', 'onChange' => 'loadCities(document.getElementById("devices-region_id"), this, "#devices-city_id")']); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->autocomplete($model, 'city_id', $cities, ['prompt' => '', 'onChange' => 'changeCity(this)']); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->autocomplete($model, 'street_id', $streets, ['onChange' => 'changeStreet(this)']); ?>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-2">
        <?php echo $form->autocomplete($model, 'house_id', $houses, ['onChange' => 'changeHouse(this)']); ?>
    </div>
    <div class="col-sm-12 col-md-3 col-lg-2">
        <?php echo $form->autocomplete($model, 'flat_id', $flats, ['prompt' => '', 'onChange' => 'loadCounters()']); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->field($model, 'type_id')->dropDownList(Devices::$types, ['prompt' => '', 'onChange' => 'changeType(this)']); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->field($model, 'data_type_id')->dropDownList(Devices::$dataTypes, ['prompt' => '', 'options' => $model->getDataTypeOptions()]); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->field($model, 'snumber')->input('text'); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->field($model, 'deveui')->input('text', ['readonly' => !$model->isNewRecord, 'onChange' => $model->isNewRecord ? 'getDeviceInfo()' : null, 'data-url' => Url::toRoute('getinfo')]); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->field($model, 'profile')->dropDownList($profiles); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->field($model, 'appeui')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-3">
        <?php echo $form->field($model, 'appkey')->input('text'); ?>
    </div>
</div>
<!--div class="row">
    <div class="col-sm-12">
<?php //echo $form->field($model, 'desc')->input('text'); ?>
    </div>
</div-->
<div class="row">
    <div class="col-sm-12 col-md-4 col-lg-3 js_type<?php echo Devices::TYPE_UNIVERSAL; ?>_type<?php echo Devices::TYPE_MODULARIS; ?>" style="<?php if (in_array($model->type_id, [Devices::TYPE_UNIVERSAL, Devices::TYPE_MODULARIS])): ?>display:block;<?php else: ?>display:none;<?php endif; ?>">
        <?php
        $options = $model->type_id == Devices::TYPE_UNIVERSAL ? [] : [Services::ID_HEAT => ['style' => 'display:none;']];
        echo $form->field($model, 'service_id_1')->dropDownList($services, ['onChange' => 'loadCounters(1)', 'options' => $options]);
        ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3 js_type<?php echo Devices::TYPE_UNIVERSAL; ?>" style="<?php if (in_array($model->type_id, [Devices::TYPE_UNIVERSAL])): ?>display:block;<?php else: ?>display:none;<?php endif; ?>">
        <?php echo $form->field($model, 'unit_1')->dropDownList(Devices::$units); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-2 js_type<?php echo Devices::TYPE_UNIVERSAL; ?>" style="<?php if (in_array($model->type_id, [Devices::TYPE_UNIVERSAL])): ?>display:block;<?php else: ?>display:none;<?php endif; ?>">
        <?php echo $form->field($model, 'rate_1')->dropDownList(Devices::$rates); ?>
    </div>
</div>
<div class="js_type<?php echo Devices::TYPE_UNIVERSAL; ?>_type<?php echo Devices::TYPE_MODULARIS; ?>" style="<?php if (in_array($model->type_id, [Devices::TYPE_UNIVERSAL, Devices::TYPE_MODULARIS])): ?>display:block;<?php else: ?>display:none;<?php endif; ?>">
    <div class="row">
        <div class="col-4 col-md-12 col-lg-3">
            <?php echo $form->field($model, 'counter_1')->dropDownList($counters_1); ?>
        </div>
        <div class="col-6 col-md-6 col-lg-3 pt-1">
            <div class="form-group field-devices-unit_2">
                <label class="control-label d-none d-sm-block">&nbsp;</label>
                <?php if ($model->sync_date_1 === null): ?>
                    <div class="badge badge-info">не сихнронизований</div>
                <?php else: ?>
                    <div class="badge badge-info js_reset_element"><?php echo date('d.m.Y H:i', strtotime($model->sync_date_1)); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="js_type<?php echo Devices::TYPE_UNIVERSAL; ?>" style="<?php if (in_array($model->type_id, [Devices::TYPE_UNIVERSAL])): ?>display:block;<?php else: ?>display:none;<?php endif; ?>">
    <div class="row">
        <div class="col-sm-12 col-md-4 col-lg-3">
            <?php echo $form->field($model, 'service_id_2')->dropDownList($services, ['prompt' => '', 'onChange' => 'loadCounters(2)']); ?>
        </div>
        <div class="col-sm-12 col-md-4 col-lg-3">
            <?php echo $form->field($model, 'unit_2')->dropDownList(Devices::$units); ?>
        </div>
        <div class="col-sm-12 col-md-4 col-lg-2">
            <?php echo $form->field($model, 'rate_2')->dropDownList(Devices::$rates); ?>
        </div>
    </div>
</div>
<div class="js_type<?php echo Devices::TYPE_UNIVERSAL; ?>" style="<?php if (in_array($model->type_id, [Devices::TYPE_UNIVERSAL])): ?>display:block;<?php else: ?>display:none;<?php endif; ?>">
    <div class="row">
        <div class="col-6 col-md-6 col-lg-3">
            <?php echo $form->field($model, 'counter_2')->dropDownList($counters_2, ['prompt' => '']); ?>
        </div>
        <div class="col-6 col-md-6 col-lg-3 pt-1">
            <div class="form-group field-devices-unit_2">
                <label class="control-label d-none d-sm-block">&nbsp;</label>
                <?php if ($model->sync_date_2 === null): ?>
                    <div class="badge badge-info">не сихнронизований</div>
                <?php else: ?>
                    <div class="badge badge-info js_reset_element"><?php echo date('d.m.Y H:i', strtotime($model->sync_date_2)); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-4 col-lg-3">
        <?php echo $form->field($model, 'place_id')->dropDownList(DataHelper::$placeLabels, ['prompt' => '']); ?>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-3 js_type<?php echo Devices::TYPE_DISTRIBUTOR; ?>" style="<?php if (in_array($model->type_id, [Devices::TYPE_DISTRIBUTOR])): ?>display:block;<?php else: ?>display:none;<?php endif; ?>">
        <?php echo $form->field($model, 'heat_k')->input('text'); ?>
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
        loadAreas(input, "#devices-area_id", function () {
            loadCities(input, document.getElementById("devices-area_id"), "#devices-city_id", "#devices-street_id");
        });
    }

    function changeCity(input) {
        loadStreets(input, "#devices-street_id", "#devices-house_id", "#devices-flat_id", function () {
            loadCounters();
        });
    }
    function changeStreet(input) {
        loadHouses(input, "#devices-house_id", "#devices-flat_id", function () {
            loadCounters();
        });
    }
    function changeHouse(input) {
        loadFlats(input, "#devices-flat_id", function () {
            loadCounters();
        });
    }
    function loadCounters(channel) {
        $.ajax({
            url: "<?php echo yii\helpers\Url::toRoute('/admin/data/counters-options'); ?>",
            type: "post",
            data: $("form").serialize(),
            dataType: "json",
            success: function (resp) {
                if (channel === 1) {
                    $("#devices-counter_1").html(resp.channel_1);
                } else if (channel === 2) {
                    $("#devices-counter_2").html(resp.channel_2);
                } else {
                    $("#devices-counter_1").html(resp.channel_1);
                    $("#devices-counter_2").html(resp.channel_2);
                }
            }
        });
    }
    function changeType(input) {
        var $selected = $("[class*=_type" + input.value + "]").slideDown();
        $("[class*=js_type]").not($selected).slideUp();
        var $heatOption = $("#devices-service_id_1 option[value=<?php echo Services::ID_HEAT; ?>]");
        input.value === "<?php echo Devices::TYPE_UNIVERSAL; ?>" ? $heatOption.show() : $heatOption.hide();
        var $abonentOption = $("#devices-data_type_id option[value=<?php echo Devices::DATA_TYPE_ABONENT; ?>],#devices-data_type_id option[value=<?php echo Devices::DATA_TYPE_MZK; ?>]");
        input.value === "<?php echo Devices::TYPE_DISTRIBUTOR; ?>" ? $("#devices-data_type_id option").not($abonentOption).hide() : $("#devices-data_type_id option").show();
    }
</script>