<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\AccessLogic;
use app\components\AppActiveForm;
use app\components\BaseActiveRecord;
use app\components\Misc;
use app\models\Services;
use app\models\Devices;
use app\models\CalculatorSettingsHouses;
use app\models\CalculatorSettingsFlats;

$this->title = $calculator->getPeriod();
$this->params['breadcrumbs'] = [
    ['label' => 'Адреси', 'url' => 'index'],
    ['label' => 'Розрахункові періоди', 'url' => ['period', 'id' => $house->id, 'aid' => $service_id]],
    $calculator->getPeriod(),
];
?>
<div style="margin-bottom:24px;"></div>
<?php $form = AppActiveForm::begin(['errorSummaryModels' => $settings->dataModels]); ?>
<div class="adaptive-column">
    <div class="column-narrow">
        <div class="card  card-fix-height" id="accordion_card_left">
            <div class="js-collapsed-block-alt">
                <div class="h card__header-bg__grey">
                    <a id="js-chevron" class="card__chevron icon-grey js-collapse" href="javascript:void(0);"><svg class="icon icon-icon-chevron-24"><use xlink:href="img/sprite.svg#icon-icon-chevron-24"></use></svg></a>
                </div>
                <div class="js-collapsed-block-label"><?php echo $house->getTitle(); ?></div>
            </div>
            <div class="js-collapsed-block">
                <div class="card__header card__header-bg__green">
                    <a href="javascript:void(0);" data-pjax="0">
                        <span class="card__header-text card__header-text__small  card__header-text__white js-active-text"><?php echo $house->getTitle(); ?></span>
                    </a>
                    <a class="card__chevron icon-white js-collapse" href="javascript:void(0);" data-action="collapse"><svg class="icon icon-icon-chevron-24"><use xlink:href="img/sprite.svg#icon-icon-chevron-24"></use></svg></a>
                </div>
                <div class="card__body is-collapsible is-active  card__body__gaped">
                    <?php foreach ($house->getClientsFlats() as $flatModel): ?>
                        <div class="card__body-item">
                            <a href="<?php echo Url::toRoute(['details', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => $service_id, 'fid' => $flatModel->id]); ?>" class="card__body-link  ">
                                <span class="card__body-number"> <?php echo $flatModel->no; ?> </span>
                                <span class="card__body-text">
                                    <?php if (isset($abonents[$flatModel->id])) : ?>
                                        <?php echo $abonents[$flatModel->id]->getNameCombined(); ?>
                                    <?php endif; ?>
                                </span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="column-wide">
        <div class="columns is-multiline columns-botoom">
            <div class="column is-flex-center">
                <div class="my-address w100">
                    <div class="h3">Адреса:</div>
                    <div class="text-dark">
                        <?php echo $house->getTitle(); ?>
                    </div>
                    <div class="text-info"><?php echo $house->clientNote; ?></div>
                </div>
            </div>
        </div>
        <?php if ($settings->isNewRecord): ?>
            <div class="alert alert-warning"><svg class="icon icon-alert"><use xlink:href="img/sprite.svg#icon-alert"></use></svg><span>Параметри не збережено.</span></div>
        <?php endif; ?>
        <div id="tabs-control" class="w100 tabs-type__wrapper">
            <?php
            foreach (Services::$labels as $sid => $label):
                if (in_array($sid, $services)):
                    ?>
                    <a href="<?php echo Url::toRoute(['details', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => $sid]); ?>" class="tabs__head-item <?php if ($sid == $service_id): ?>is-active service<?php echo $sid; ?><?php endif; ?>">
                        <icon class="tabs__head-item--icon  "><?php echo Services::getIcon($sid); ?></icon><span class="tabs__head-item--text"><?php echo $label; ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="">
            <div class="calc-column">
                <?php if (in_array(Services::ID_COOL_WATER, $services) && $service_id == Services::ID_COOL_WATER): ?>
                    <div class="tab tab<?php echo Services::ID_COOL_WATER; ?>">
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::TOTAL_HOUSE_QTY_COOL];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?>
                        </div>
                        <?php
                        foreach ($hardware as $device_type_id => $rows):
                            switch ($device_type_id) :
                                case Devices::DATA_TYPE_COMMON:
                                    $modelId = CalculatorSettingsHouses::GERENAL_PURPOSE_QTY_COOL;
                                    $modelHwId = CalculatorSettingsHouses::HARDWARE_WATER_GENERAL_COOL;
                                    break;
                                case Devices::DATA_TYPE_COOL_TO_HOT:
                                    $modelId = CalculatorSettingsHouses::TOTAL_QTY_COOL_TO_HOT;
                                    $modelHwId = CalculatorSettingsHouses::HARDWARE_WATER_COOL_TO_HOT_HOT;
                                    break;
                                default :
                                    continue 2;
                            endswitch;
                            ?>
                            <div class="mobile-group form-default__row">
                                <?php
                                $model = $settings->dataModels[$modelId];
                                echo Html::label("{$model->getLabel()}", null, ['class' => 'form-default__label']);
                                $model = $settings->dataModels[$modelHwId];
                                ?>
                                <?php foreach ($rows as $i => $hw): ?>
                                    <div class="d-flex-calculator-pair">
                                        <?php
                                        echo Html::label("- {$hw->getLabel()} ({$hw->getUnit()}):", null, ['class' => 'form-default__label']);
                                        echo $form->field($model, 'hardwareValues')->label(false)->input('text', [
                                            'class' => 'form-default__input',
                                            'readonly' => !$calculator->isAllowEdit($service_id),
                                            'name' => "CalculatorSettingsHouses[data][{$model->id}][hardwareValues][{$hw->getPairIds()}]",
                                            'value' => $model->getHwValue($hw->getPairIds()),
                                        ]);
                                        ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                        <div class="form-default__row">
                            <?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::COOL_WATER_PRICE];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?>
                        </div>
                        <div class="form-default__row">
                            <?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::COOL_WATER_DRAIN_PRICE];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?>
                        </div>
                        <div class="mobile-group form-default__row">
                            <?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::ADJUSTMENT_COOL_WATER];
                            echo Html::label($model->getLabel(), null, ['class' => 'form-default__label']);
                            ?>
                            <div class="d-flex-calculator-pair">
                                <?php
                                echo $form->field($model, "[data][{$model->id}]value")->label(false)->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                                ?>
                                <?php
                                echo $form->field($model, "[data][{$model->id}]info")->label(false)->input('text', ['class' => 'form-default__input', 'placeholder' => 'коментар', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                                ?>
                            </div>
                        </div>
                        <div class="mobile-group form-default__row">
                            <?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::GERENAL_PURPOSE_QTY_COOL_VARIANT];
                            echo Html::label($model->getLabel(), null, ['class' => 'form-default__label']);
                            ?>
                            <div class="d-flex-calculator-pair">
                                <?php
                                #$model = $settings->dataModels[CalculatorSettingsHouses::GERENAL_PURPOSE_QTY_COOL_VARIANT];
                                $values = CalculatorSettingsHouses::$generalPurposeQtyValues;
                                if (!$calculator->isAllowEdit($service_id)) {
                                    $values = [$model->value => $values[$model->value]];
                                }
                                echo $form->field($model, "[data][{$model->id}]value")->label(false)->dropDownList($values,
                                        ['class' => 'js-dropdown-select form-default__select', 'onChange' => 'changeSettingsValue(' . CalculatorSettingsHouses::GERENAL_PURPOSE_QTY_COOL_VARIANT . ',this)']);
                                ?>
                                <div class="js_value_<?php echo CalculatorSettingsHouses::GERENAL_PURPOSE_QTY_COOL_VARIANT; ?>_2" <?php if ($model->value <> '2'): ?>style="display:none;"<?php endif; ?>><?php
                                    $model = $settings->dataModels[CalculatorSettingsHouses::GERENAL_PURPOSE_QTY_COOL];
                                    echo $form->field($model, "[data][{$model->id}]value")->label(false)->input('text', ['class' => 'form-default__input', 'placeholder' => $model->getLabel(), 'readonly' => !$calculator->isAllowEdit($service_id)]);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="calc-buttons-group">
                            <div class="">
                                <a href="<?php echo Url::toRoute(['period', 'id' => $house->id, 'aid' => $service_id]); ?>" class="close btn btn-profile bg-white">&larr; Назад</a>
                            </div>
                            <?php if ($calculator->isAllowEdit($service_id)): ?>
                                <div class="text-right">
                                    <button class="btn btn-profile btn-blue" aria-haspopup="true" name="action" value="save"><svg class="icon icon-check"><use xlink:href="img/sprite.svg#icon-check"></use></svg> Зберегти</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (AccessLogic::isLoginAs()): ?>
                            <div class="calc-buttons-group">
                                <div class="">
                                    <?php if ($settings->isNewRecord): ?>
                                        <div class="alert alert-warning"><span>Перегляд розрахунків буде доступний після збереження параметрів.</span></div>
                                    <?php else: ?>
                                        <a href="<?php echo Url::toRoute(['preview', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => Services::ID_COOL_WATER, 'period' => 'month']); ?>" class="btn btn-add-gray btn-profile w100" target="check_window">
                                            Переглянути
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (in_array(Services::ID_HOT_WATER, $services) && $service_id == Services::ID_HOT_WATER): ?>
                    <div class="tab tab<?php echo Services::ID_HOT_WATER; ?>">
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::TOTAL_HOUSE_QTY_HOT];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?>
                        </div>
                        <?php
                        foreach ($hardware as $device_type_id => $rows):
                            switch ($device_type_id) :
                                case Devices::DATA_TYPE_COMMON:
                                    $modelId = CalculatorSettingsHouses::GERENAL_PURPOSE_QTY_HOT;
                                    $modelHwId = CalculatorSettingsHouses::HARDWARE_WATER_GENERAL_HOT;
                                    break;
                                default :
                                    continue 2;
                            endswitch;
                            ?>
                            <div class="mobile-group form-default__row">
                                <?php
                                $model = $settings->dataModels[$modelId];
                                echo Html::label("{$model->getLabel()}", null, ['class' => 'form-default__label']);
                                $model = $settings->dataModels[$modelHwId];
                                ?>
                                <?php foreach ($rows as $i => $hw): ?>
                                    <div class="d-flex-calculator-pair">
                                        <?php
                                        echo Html::label("- {$hw->getLabel()} ({$hw->getUnit()}):", null, ['class' => 'form-default__label']);
                                        echo $form->field($model, 'hardwareValues')->label(false)->input('text', [
                                            'class' => 'form-default__input',
                                            'readonly' => !$calculator->isAllowEdit($service_id),
                                            'name' => "CalculatorSettingsHouses[data][{$model->id}][hardwareValues][{$hw->getPairIds()}]",
                                            'value' => $model->getHwValue($hw->getPairIds()),
                                        ]);
                                        ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::HOT_WATER_PRICE];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?></div>
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::HOT_WATER_PRICE_HEAT_TOWEL];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?></div>
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::HOT_WATER_DRAIN_PRICE];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?></div>
                        <div class="mobile-group form-default__row">
                            <?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::ADJUSTMENT_HOT_WATER];
                            echo Html::label($model->getLabel(), null, ['class' => 'form-default__label']);
                            ?>
                            <div class="d-flex-calculator-pair">
                                <?php
                                echo $form->field($model, "[data][{$model->id}]value")->label(false)->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                                ?>
                                <?php
                                echo $form->field($model, "[data][{$model->id}]info")->label(false)->input('text', ['class' => 'form-default__input', 'placeholder' => 'коментар', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                                ?>
                            </div>
                        </div>
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::HOT_WATER_SUPPLY];
                            $values = CalculatorSettingsHouses::$hotWaterSupplyValues;
                            if (!$calculator->isAllowEdit($service_id)) {
                                $values = [$model->value => $values[$model->value]];
                            }
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->dropDownList($values, ['class' => 'js-dropdown-select form-default__select']);
                            ?></div>
                        <div class="mobile-group form-default__row">
                            <?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::GERENAL_PURPOSE_QTY_HOT_VARIANT];
                            echo Html::label($model->getLabel(), null, ['class' => 'form-default__label']);
                            ?>
                            <div class="d-flex-calculator-pair">
                                <?php
                                #$model = $settings->dataModels[CalculatorSettingsHouses::GERENAL_PURPOSE_QTY_HOT_VARIANT];
                                $values = CalculatorSettingsHouses::$generalPurposeQtyValues;
                                if (!$calculator->isAllowEdit($service_id)) {
                                    $values = [$model->value => $values[$model->value]];
                                }
                                echo $form->field($model, "[data][{$model->id}]value")->label(false)->dropDownList($values,
                                        ['class' => 'js-dropdown-select form-default__select', 'onChange' => 'changeSettingsValue(' . CalculatorSettingsHouses::GERENAL_PURPOSE_QTY_HOT_VARIANT . ',this)']);
                                ?>
                                <div class="js_value_<?php echo CalculatorSettingsHouses::GERENAL_PURPOSE_QTY_HOT_VARIANT; ?>_2" <?php if ($model->value <> '2'): ?>style="display:none;"<?php endif; ?>><?php
                                    $model = $settings->dataModels[CalculatorSettingsHouses::GERENAL_PURPOSE_QTY_HOT];
                                    echo $form->field($model, "[data][{$model->id}]value")->label(false)->input('text', ['class' => 'form-default__input', 'placeholder' => $model->getLabel(), 'readonly' => !$calculator->isAllowEdit($service_id)]);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="calc-buttons-group">
                            <div class="">
                                <a href="<?php echo Url::toRoute(['period', 'id' => $house->id, 'aid' => $service_id]); ?>" class="close btn btn-profile bg-white">&larr; Назад</a>
                            </div>
                            <?php if ($calculator->isAllowEdit($service_id)): ?>
                                <div class="text-right">
                                    <button class="btn btn-profile btn-blue" aria-haspopup="true" name="action" value="save"><svg class="icon icon-check"><use xlink:href="img/sprite.svg#icon-check"></use></svg> Зберегти</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (AccessLogic::isLoginAs()): ?>
                            <div class="calc-buttons-group">
                                <div class="">
                                    <?php if ($settings->isNewRecord): ?>
                                        <div class="alert alert-warning"><span>Перегляд розрахунків буде доступний після збереження параметрів.</span></div>
                                    <?php else: ?>
                                        <a href="<?php echo Url::toRoute(['preview', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => Services::ID_HOT_WATER, 'period' => 'month']); ?>" class="btn btn-add-gray btn-profile w100" target="check_window">
                                            Переглянути
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (in_array(Services::ID_HEAT, $services) && $service_id == Services::ID_HEAT): ?>
                    <div class="tab tab<?php echo Services::ID_HEAT; ?>">
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::Q_OP_BUD];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?>
                        </div>
                        <?php
                        foreach ($hardware as $device_type_id => $rows):
                            switch ($device_type_id) :
                                case Devices::DATA_TYPE_HEAT_TO_HOT_WATER:
                                    $modelId = CalculatorSettingsHouses::TOTAL_HEAT_TO_HOT;
                                    $modelHwId = CalculatorSettingsHouses::HARDWARE_HEAT_TO_HOT_WATER;
                                    break;
                                case Devices::DATA_TYPE_MZK:
                                    $modelId = CalculatorSettingsHouses::TOTAL_QTY_MZK;
                                    $modelHwId = CalculatorSettingsHouses::HARDWARE_HEAT_MZK;
                                    break;
                                default :
                                    continue 2;
                            endswitch;
                            ?>
                            <div class="mobile-group form-default__row">
                                <?php
                                $model = $settings->dataModels[$modelId];
                                echo Html::label("{$model->getLabel()}", null, ['class' => 'form-default__label']);
                                $model = $settings->dataModels[$modelHwId];
                                ?>
                                <?php foreach ($rows as $i => $hw): ?>
                                    <div class="d-flex-calculator-pair">
                                        <?php
                                        echo Html::label("- {$hw->getLabel()} ({$hw->getUnit()}):", null, ['class' => 'form-default__label']);
                                        echo $form->field($model, 'hardwareValues')->label(false)->input('text', [
                                            'class' => 'form-default__input',
                                            'readonly' => !$calculator->isAllowEdit($service_id),
                                            'name' => "CalculatorSettingsHouses[data][{$model->id}][hardwareValues][{$hw->getPairIds()}]",
                                            'value' => $model->getHwValue($hw->getPairIds()),
                                        ]);
                                        ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::HEAT_PRICE];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?></div>
                        <div class="mobile-group form-default__row">
                            <?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::ADJUSTMENT_HEAT];
                            echo Html::label($model->getLabel(), null, ['class' => 'form-default__label']);
                            ?>
                            <div class="d-flex-calculator-pair">
                                <?php
                                echo $form->field($model, "[data][{$model->id}]value")->label(false)->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                                ?>
                                <?php
                                echo $form->field($model, "[data][{$model->id}]info")->label(false)->input('text', ['class' => 'form-default__input', 'placeholder' => 'коментар', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                                ?>
                            </div>
                        </div>
                        <div class="mobile-group form-default__row">
                            <?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::MZK_STRATEGY];
                            echo Html::label($model->getLabel(), null, ['class' => 'form-default__label']);
                            ?>
                            <div class="d-flex-calculator-pair">
                                <?php
                                #$model = $settings->dataModels[CalculatorSettingsHouses::GERENAL_PURPOSE_QTY_COOL_VARIANT];
                                $values = CalculatorSettingsHouses::$mzkStrategyValues;
                                if (!$calculator->isAllowEdit($service_id)) {
                                    $values = [$model->value => $values[$model->value]];
                                }
                                echo $form->field($model, "[data][{$model->id}]value")->label(false)->dropDownList($values,
                                        ['class' => 'js-dropdown-select form-default__select', 'onChange' => 'changeSettingsValue(' . CalculatorSettingsHouses::MZK_STRATEGY . ',this)']);
                                ?>
                                <div class="js_value_<?php echo CalculatorSettingsHouses::MZK_STRATEGY; ?>_23" <?php if ($model->value <> '23'): ?>style="display:none;"<?php endif; ?>><?php
                                    $modelAlt = $settings->dataModels[CalculatorSettingsHouses::MZK_STRATEGY_III_4_QTY];
                                    $floors = $house->getFloors();
                                    $floorsTxt = $floors . ' ' . Misc::wordingFloors($floors) . ': ' . $modelAlt->getFloorK_txt($floors);
                                    echo Html::textInput(null, $floorsTxt, ['class' => 'form-default__input', 'readonly' => true, 'disabled' => true]);
                                    ?>
                                </div>
                                <div class="js_value_<?php echo CalculatorSettingsHouses::MZK_STRATEGY; ?>_4" <?php if ($model->value <> '4'): ?>style="display:none;"<?php endif; ?>><?php
                                    $modelAlt = $settings->dataModels[CalculatorSettingsHouses::MZK_STRATEGY_III_4_QTY];
                                    echo $form->field($modelAlt, "[data][{$modelAlt->id}]value")->label(false)->input('text', ['class' => 'form-default__input', 'placeholder' => $modelAlt->getLabel(), 'readonly' => !$calculator->isAllowEdit($service_id)]);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::QK];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?></div>
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::HEAT_SUPPLY];
                            $values = CalculatorSettingsHouses::$heatSupplyValues;
                            if (!$calculator->isAllowEdit($service_id)) {
                                $values = [$model->value => $values[$model->value]];
                            }
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->dropDownList($values, ['class' => 'js-dropdown-select form-default__select']);
                            ?></div>
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::SETUP_DISTRIBUTORS];
                            $values = CalculatorSettingsHouses::$setupDistrValues;
                            if (!$calculator->isAllowEdit($service_id)) {
                                $values = [$model->value => $values[$model->value]];
                            }
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->dropDownList($values, ['class' => 'js-dropdown-select form-default__select']);
                            ?></div>
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::IS_FIRST_PERIOD];
                            $values = BaseActiveRecord::$valuesTrueFalse;
                            if (!$calculator->isAllowEdit($service_id)) {
                                $values = [$model->value => $values[$model->value]];
                            }
                            echo $form->field($model, "[data][{$model->id}]value")->label($calculator->getPeriod() . ' - ' . $model->getLabel(), ['class' => 'form-default__label'])->dropDownList($values,
                                    ['class' => 'js-dropdown-select form-default__select']);
                            ?>
                        </div>
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::Q_MIN_SPECIFIC];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?></div>
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::AVG_TEMP_CURRENT];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?></div>
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsHouses::T_NORM];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?></div>
                        <div class="calc-buttons-group">
                            <div class="">
                                <a href="<?php echo Url::toRoute(['period', 'id' => $house->id, 'aid' => $service_id]); ?>" class="close btn btn-profile bg-white">&larr; Назад</a>
                            </div>
                            <?php if ($calculator->isAllowEdit($service_id)): ?>
                                <div class="text-right">
                                    <button class="btn btn-profile btn-blue" aria-haspopup="true" name="action" value="save"><svg class="icon icon-check"><use xlink:href="img/sprite.svg#icon-check"></use></svg> Зберегти</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (AccessLogic::isLoginAs()): ?>
                            <div class="calc-buttons-group">
                                <div class="">
                                    <?php if ($settings->isNewRecord): ?>
                                        <div class="alert alert-warning"><span>Перегляд розрахунків буде доступний після збереження параметрів.</span></div>
                                    <?php else: ?>
                                        <a href="<?php echo Url::toRoute(['preview', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => Services::ID_HEAT, 'period' => 'month']); ?>" class="btn btn-add-gray btn-profile w100" target="check_window">
                                            Переглянути
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php AppActiveForm::end(); ?>
