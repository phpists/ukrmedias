<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Url;
use app\components\AppActiveForm;
use app\models\AccessLogic;
use app\models\Services;

app\components\assets\DatepickerAssets::register($this);
$this->params['breadcrumbs'] = [
    ['label' => 'Прилади обліку', 'url' => 'index'],
];
$this->title = 'Лічильник ' . $model->number;
?>
<div class="main-template-employ">
    <div class="h3 main-template__header"><?php echo $this->title; ?></div>
    <?php $form = AppActiveForm::begin(['errorSummaryModels' => $model]); ?>
    <div class="calc-column">
        <div class="columns is-multiline columns-botoom--double">
            <div class="column is-6-desktop is-6-tablet is-12-mobile">
                <div class="form-default__row">
                    <?php
                    if (count($regions) > 1) {
                        echo $form->autocomplete($model, 'region_id', $regions, ['class' => 'js-dropdown-select2 form-default__select', 'prompt' => 'обласний центр', 'onChange' => 'changeRegion(this)']);
                    } else {
                        echo $form->field($model, 'region_id')->label(null, ['class' => 'form-default__label'])->dropDownList($regions, ['class' => 'js-dropdown-select form-default__select']);
                    }
                    ?>
                </div>
                <div class="form-default__row">
                    <?php
                    if (count($regions) > 1 || count($areas) > 1) {
                        echo $form->autocomplete($model, 'area_id', $areas, ['class' => 'js-dropdown-select2 form-default__select', 'prompt' => empty($model->region_id) ? '-' : 'районний центр', 'onChange' => 'changeArea(this)']);
                    } else {
                        echo $form->field($model, 'area_id')->label(null, ['class' => 'form-default__label'])->dropDownList($areas, ['class' => 'js-dropdown-select form-default__select']);
                    }
                    ?>
                </div>
                <div class="form-default__row">
                    <?php
                    if (count($regions) > 1 || count($areas) > 1 || count($cities) > 1) {
                        echo $form->autocomplete($model, 'city_id', $cities, ['class' => 'js-dropdown-select2 form-default__select', 'prompt' => '', 'onChange' => 'changeCity(this)']);
                    } else {
                        echo $form->field($model, 'city_id')->label(null, ['class' => 'form-default__label'])->dropDownList($cities, ['class' => 'js-dropdown-select form-default__select']);
                    }
                    ?>
                </div>
                <div class="form-default__row">
                    <?php echo $form->autocomplete($model, 'street_id', $streets, ['class' => 'js-dropdown-select2 form-default__select', 'prompt' => '', 'onChange' => 'changeStreet(this)']); ?>
                </div>
                <div class="form-default__row">
                    <?php echo $form->autocomplete($model, 'house_id', $houses, ['class' => 'js-dropdown-select2 form-default__select', 'prompt' => '', 'onChange' => 'changeHouse(this)']); ?>
                </div>
                <div class="form-default__row">
                    <?php echo $form->autocomplete($model, 'flat_id', $flats, ['class' => 'js-dropdown-select2 form-default__select', 'prompt' => '']); ?>
                </div>
            </div>
            <div class="column is-6-desktop is-6-tablet is-12-mobile">
                <div class="form-default__row">
                    <?php
                    echo $form->field($model, 'service_id')->label(null, ['class' => 'form-default__label'])
                            ->dropDownList($services, ['class' => 'js-dropdown-select form-default__select', 'prompt' => '', 'onChange' => 'initBeginField();']);
                    ?>
                </div>
                <div class="form-default__row">
                    <?php echo $form->field($model, 'number')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
                </div>
                <div class="form-default__row">
                    <?php echo $form->field($model, 'model')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
                </div>
                <?php if (AccessLogic::isLoginAs()): ?>
                    <div class="form-default__row">
                        <?php
                        $label = "{$model->getAttributeLabel('begin')}"
                                . '<span style="display:none;" class="js_begin_' . Services::ID_HEAT . '">, ' . Services::$units[Services::ID_HEAT] . '</span>'
                                . '<span style="display:none;" class="js_begin_' . Services::ID_COOL_WATER . ' js_begin_' . Services::ID_HOT_WATER . '">, ' . Services::$units[Services::ID_COOL_WATER] . '</span>';
                        echo $form->field($model, 'begin')->label($label, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'placeholder' => '0.0000']);
                        ?>
                    </div>
                <?php endif; ?>
                <div class="form-default__row">
                    <?php echo $form->field($model, 'date')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'data-default-time' => $model->date]); ?>
                </div>
            </div>
        </div>
        <div class="calc-buttons-group">
            <div class="">
                <a href="<?php echo Url::toRoute('index'); ?>" class="close btn btn-profile bg-white">&larr; Назад</a>
            </div>
            <div class="">
                <button class="btn btn-profile btn-blue" aria-haspopup="true" name="action" value="save"><svg class="icon icon-check"><use xlink:href="img/sprite.svg#icon-check"></use></svg> Зберегти</button>
            </div>
        </div>
    </div>
    <?php AppActiveForm::end(); ?>
</div>
<script>
    function changeRegion(input) {
        loadAreas(input, "#counters-area_id", function () {
            loadCities(input, document.getElementById("counters-area_id"), "#counters-city_id", "#counters-street_id", function () {
                $('select.js-dropdown-select').niceSelect('update');
            });
        });
    }
    function changeArea(input) {
        loadCities(document.getElementById("counters-region_id"), input, "#counters-city_id", "#counters-street_id", function () {
            $('select.js-dropdown-select').niceSelect('update');
        });
    }

    function changeCity(input) {
        loadStreets(input, "#counters-street_id", "#counters-house_id", "#counters-flat_id", function () {
            $('select.js-dropdown-select').niceSelect('update');
        });
    }
    function changeStreet(input) {
        loadHouses(input, "#counters-house_id", "#counters-flat_id", function () {
            $('select.js-dropdown-select').niceSelect('update');
        });
    }
    function changeHouse(input) {
        loadFlats(input, "#counters-flat_id", function () {
            $('#counters-flat_id').niceSelect('update');
        });
    }
</script>