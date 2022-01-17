<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;
use app\models\Services;

$this->params['breadcrumbs'] = [
    ['label' => 'Адреси', 'url' => 'index'],
];
$this->title = 'Профіль адреси';
?>
<?php $form = AppActiveForm::begin(['errorSummaryModels' => $model]); ?>
<div class="main-template-employ">
    <div class="h3 main-template__header"><?php echo $this->title; ?></div>
    <div class="form-flex-wrap">
        <div class="form-flex-column">
            <div class="form-default__row">
                <?php
                if (count($regions) > 1) {
                    echo $form->autocomplete($model, 'region_id', $regions, ['class' => 'js-dropdown-select2 form-default__select', 'prompt' => 'обласний центр', 'onChange' => 'changeRegion(this)']);
                } else {
                    echo $form->field($model, 'region_id')->dropDownList($regions, ['class' => 'js-dropdown-select form-default__select']);
                }
                ?>
            </div>
            <div class="form-default__row">
                <?php
                if (count($regions) > 1 || count($areas) > 1) {
                    echo $form->autocomplete($model, 'area_id', $areas, ['class' => 'js-dropdown-select2 form-default__select', 'prompt' => empty($model->region_id) ? '-' : 'районний центр', 'onChange' => 'changeArea(this)']);
                } else {
                    echo $form->field($model, 'area_id')->dropDownList($areas, ['class' => 'js-dropdown-select form-default__select']);
                }
                ?>
            </div>
            <div class="form-default__row">
                <?php
                if (count($regions) > 1 || count($areas) > 1 || count($cities) > 1) {
                    echo $form->autocomplete($model, 'city_id', $cities, ['class' => 'js-dropdown-select2 form-default__select', 'prompt' => '', 'onChange' => 'changeCity(this)']);
                } else {
                    echo $form->field($model, 'city_id')->dropDownList($cities, ['class' => 'js-dropdown-select form-default__select']);
                }
                ?>
            </div>
            <div class="form-default__row">
                <?php echo $form->autocomplete($model, 'street_id', $streets, ['class' => 'js-dropdown-select2 form-default__select', 'prompt' => '', 'onChange' => 'changeStreet(this)']); ?>
            </div>
            <div class="form-default__row">
                <?php echo $form->autocomplete($model, 'id', $houses, ['class' => 'js-dropdown-select2 form-default__select', 'prompt' => '']); ?>
            </div>
            <div class="form-default__row">
                <?php echo $form->field($model, 'clientNote')->label(null, ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input']); ?>
            </div>
        </div>
        <div class="form-flex-column">
            <label class="control-label">Послуги</label>
            <?php
            foreach (Services::$labels as $service_id => $label):
                if (!in_array($service_id, $services)) {
                    continue;
                }
                ?>
                <div class="form-default__row checkbox-row">
                    <?php
                    echo Html::activeCheckbox($model, "services_ids[{$service_id}]", [
                        'class' => 'form-default__checkbox',
                        'label' => false,
                    ]);
                    ?>
                    <?php
                    echo Html::activeLabel($model, "services_ids[{$service_id}]", [
                        'class' => 'form-default__checkbox-label',
                        'label' => $label,
                    ]);
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="form-flex-wrap">
        <div class="form-flex-column">
            <div class="feedback__wrap">
                <label class="form-default__label">&nbsp;</label>
                <div class="calc-buttons-group">
                    <div class="">
                        <a href="<?php echo Url::toRoute('index'); ?>" class="close btn btn-profile bg-white">&larr; Назад</a>
                    </div>
                    <div class="">
                        <button class="btn btn-profile btn-blue" aria-haspopup="true" name="action" value="save"><svg class="icon icon-check"><use xlink:href="img/sprite.svg#icon-check"></use></svg> Зберегти</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php AppActiveForm::end(); ?>
<script>
    function changeRegion(input) {
        loadAreas(input, "#addrhouses-area_id", function () {
            loadCities(input, document.getElementById("addrhouses-area_id"), "#addrhouses-city_id", "#addrhouses-street_id");
        });
    }
    function changeArea(input) {
        loadCities(document.getElementById("addrhouses-region_id"), input, "#addrhouses-city_id", "#addrhouses-street_id");
    }

    function changeCity(input) {
        loadStreets(input, "#addrhouses-street_id", "#addrhouses-id");
    }
    function changeStreet(input) {
        loadHouses(input, "#addrhouses-id");
    }
</script>