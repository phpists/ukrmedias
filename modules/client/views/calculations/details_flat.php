<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\AppActiveForm;
use app\components\BaseActiveRecord;
use app\models\Services;
use app\models\AddrFlats;
use app\models\AccessLogic;
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
                <div class="card__header card__header-bg__grey">
                    <a href="<?php echo Url::toRoute(['details', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => $service_id]); ?>" data-pjax="0">
                        <span class="card__header-text card__header-text__small  card__header-text__white"><?php echo $house->getTitle(); ?></span>
                    </a>
                    <a class="card__chevron icon-grey js-collapse" href="javascript:void(0);" data-action="collapse"><svg class="icon icon-icon-chevron-24"><use xlink:href="img/sprite.svg#icon-icon-chevron-24"></use></svg></a>
                </div>
                <div class="card__body is-collapsible is-active  card__body__gaped">
                    <?php foreach ($house->getClientsFlats() as $flatModel): ?>
                        <div class="card__body-item <?php if ($flatModel->id === $flat->id): ?>card__header-bg__green js-active-text<?php endif; ?>">
                            <a href="<?php echo Url::toRoute(['details', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => $service_id, 'fid' => $flatModel->id]); ?>" class="card__body-link" data-pjax="0">
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
                        <?php echo $flat->getTitle(); ?>
                    </div>
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
                    <a href="<?php echo Url::toRoute(['details', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => $sid, 'fid' => $flat->id]); ?>" class="tabs__head-item <?php if ($sid == $service_id): ?>is-active service<?php echo $sid; ?><?php endif; ?>">
                        <icon class="tabs__head-item--icon  "><?php echo Services::getIcon($sid); ?></icon><span class="tabs__head-item--text"><?php echo $label; ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="calc-column">
            <?php if (in_array(Services::ID_COOL_WATER, $services) && $service_id == Services::ID_COOL_WATER): ?>
                <div class="tab tab<?php echo Services::ID_COOL_WATER; ?>">
                    <?php if (count($hardware) > 0): ?>
                        <div class="mobile-group form-default__row">
                            <?php
                            $model = $settings->dataModels[CalculatorSettingsFlats::COOL_WATER_QTY];
                            echo Html::label($model->getLabel(), null, ['class' => 'form-default__label']);
                            ?>
                            <?php
                            $model = $settings->dataModels[CalculatorSettingsFlats::HARDWARE_WATER_COOL];
                            foreach ($hardware as $i => $hw):
                                ?>
                                <div class="d-flex-calculator-pair">
                                    <?php
                                    echo Html::label("- {$hw->getLabel()}:", null, ['class' => 'form-default__label']);
                                    echo $form->field($model, 'hardwareValues')
                                            ->label(false)->input('text', [
                                        'class' => 'form-default__input',
                                        'readonly' => !$calculator->isAllowEdit($service_id),
                                        'name' => "CalculatorSettingsFlats[data][{$model->id}][hardwareValues][{$hw->getPairIds()}]",
                                        'value' => $model->getHwValue($hw->getPairIds()),
                                    ]);
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-default__row"><?php
                            $modelForce = $settings->dataModels[CalculatorSettingsFlats::FORCE_NONE_COUNTER_WATER_COOL];
                            $values = BaseActiveRecord::$valuesTrueFalse;
                            if (!$calculator->isAllowEdit($service_id) && isset($values[$modelForce->value])) {
                                $values = [$modelForce->value => $values[$modelForce->value]];
                            }
                            echo $form->field($modelForce, "[data][{$modelForce->id}]value")->label($modelForce->getLabel(), ['class' => 'form-default__label'])->dropDownList($values, ['class' => 'js-dropdown-select form-default__select', 'onChange' => 'changeSettingsValue(' . CalculatorSettingsFlats::FORCE_NONE_COUNTER_WATER_COOL . ',this)']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="mobile-group form-default__row">
                        <?php
                        $model = $settings->dataModels[CalculatorSettingsFlats::ADJUSTMENT_COOL_WATER];
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
                    <div class="form-default__row js_value_<?php echo CalculatorSettingsFlats::FORCE_NONE_COUNTER_WATER_COOL; ?>_1" <?php if (isset($modelForce) && $modelForce->value <> '1'): ?>style="display:none;"<?php endif; ?>>
                        <?php
                        $model = $settings->dataModels[CalculatorSettingsFlats::WATER_USERS];
                        echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', [
                            'id' => 'waterUser_1', 'class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                        ?>
                    </div>
                    <div class="form-default__row">
                        <?php
                        $model = $settings->dataModels[CalculatorSettingsFlats::LEAK_QTY_COOL];
                        echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                        ?>
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
                                    <a href="<?php echo Url::toRoute(['preview', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => Services::ID_COOL_WATER, 'fid' => $flat->id, 'period' => 'month']); ?>" class="btn btn-add-gray btn-profile w100" target="check_window">
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
                    <?php if (count($hardware) > 0): ?>
                        <div class="mobile-group form-default__row">
                            <?php
                            $model = $settings->dataModels[CalculatorSettingsFlats::HOT_WATER_QTY];
                            echo Html::label($model->getLabel(), null, ['class' => 'form-default__label']);
                            ?>
                            <?php
                            $model = $settings->dataModels[CalculatorSettingsFlats::HARDWARE_WATER_HOT];
                            foreach ($hardware as $i => $hw):
                                ?>
                                <div class="d-flex-calculator-pair">
                                    <?php
                                    echo Html::label("- {$hw->getLabel()}:", null, ['class' => 'form-default__label']);
                                    echo $form->field($model, 'hardwareValues')
                                            ->label(false)->input('text', [
                                        'class' => 'form-default__input',
                                        'readonly' => !$calculator->isAllowEdit($service_id),
                                        'name' => "CalculatorSettingsFlats[data][{$model->id}][hardwareValues][{$hw->getPairIds()}]",
                                        'value' => $model->getHwValue($hw->getPairIds()),
                                    ]);
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-default__row"><?php
                            $modelForce = $settings->dataModels[CalculatorSettingsFlats::FORCE_NONE_COUNTER_WATER_HOT];
                            $values = BaseActiveRecord::$valuesTrueFalse;
                            if (!$calculator->isAllowEdit($service_id) && isset($values[$modelForce->value])) {
                                $values = [$modelForce->value => $values[$modelForce->value]];
                            }
                            echo $form->field($modelForce, "[data][{$modelForce->id}]value")->label($modelForce->getLabel(), ['class' => 'form-default__label'])->dropDownList($values, ['class' => 'js-dropdown-select form-default__select', 'onChange' => 'changeSettingsValue(' . CalculatorSettingsFlats::FORCE_NONE_COUNTER_WATER_COOL . ',this)']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="mobile-group form-default__row">
                        <?php
                        $model = $settings->dataModels[CalculatorSettingsFlats::ADJUSTMENT_HOT_WATER];
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
                        $model = $settings->dataModels[CalculatorSettingsFlats::HEAT_TOWEL];
                        $values = BaseActiveRecord::$valuesTrueFalse;
                        if (!$calculator->isAllowEdit($service_id) && isset($values[$model->value])) {
                            $values = [$model->value => $values[$model->value]];
                        }
                        echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->dropDownList($values, ['class' => 'js-dropdown-select form-default__select']);
                        ?>
                    </div>
                    <div class="form-default__row js_value_<?php echo CalculatorSettingsFlats::FORCE_NONE_COUNTER_WATER_HOT; ?>_1" <?php if (isset($modelForce) && $modelForce->value <> '1'): ?>style="display:none;"<?php endif; ?>>
                        <?php
                        $model = $settings->dataModels[CalculatorSettingsFlats::WATER_USERS];
                        echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', [
                            'id' => 'waterUser_2', 'class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                        ?>
                    </div>
                    <div class="form-default__row"><?php
                        $model = $settings->dataModels[CalculatorSettingsFlats::LEAK_QTY_HOT];
                        echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                        ?>
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
                                    <a href="<?php echo Url::toRoute(['preview', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => Services::ID_HOT_WATER, 'fid' => $flat->id, 'period' => 'month']); ?>" class="btn btn-add-gray btn-profile w100" target="check_window">
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
                    <?php
                    if (in_array($flat->heat_metering, [AddrFlats::HEAT_METERING_COUNTER, AddrFlats::HEAT_METERING_DISTRIBUTOR])):
                        ?>
                        <?php
                        if (count($hardware) > 0):
                            $unit = current($hardware)->getUnit();
                            ?>
                            <div class="mobile-group form-default__row">
                                <?php
                                $model = $settings->dataModels[CalculatorSettingsFlats::HEAT_QTY];
                                echo Html::label("{$model->getLabel()}, {$unit}", null, ['class' => 'form-default__label']);
                                $model = $settings->dataModels[CalculatorSettingsFlats::HARDWARE_HEAT];
                                foreach ($hardware as $i => $hw):
                                    ?>
                                    <div class="d-flex-calculator-pair">
                                        <?php
                                        echo Html::label("- {$hw->getLabel()}:", null, ['class' => 'form-default__label']);
                                        echo $form->field($model, 'hardwareValues')
                                                ->label(false)->input('text', [
                                            'class' => 'form-default__input',
                                            'readonly' => !$calculator->isAllowEdit($service_id),
                                            'name' => "CalculatorSettingsFlats[data][{$model->id}][hardwareValues][{$hw->getPairIds()}]",
                                            'value' => $model->getHwValue($hw->getPairIds()),
                                        ]);
                                        ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-default__row"><?php
                                $model = $settings->dataModels[CalculatorSettingsFlats::FORCE_NONE_COUNTER_HEAT];
                                $values = BaseActiveRecord::$valuesTrueFalse;
                                if (!$calculator->isAllowEdit($service_id) && isset($values[$model->value])) {
                                    $values = [$model->value => $values[$model->value]];
                                }
                                echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->dropDownList($values, ['class' => 'js-dropdown-select form-default__select']);
                                ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <div class="mobile-group form-default__row">
                        <?php
                        $model = $settings->dataModels[CalculatorSettingsFlats::ADJUSTMENT_HEAT];
                        echo Html::label($model->getLabel(), null, ['class' => 'form-default__label']);
                        ?>
                        <div class="d-flex-calculator-pair">
                            <?php
                            echo $form->field($model, "[data][{$model->id}]value")->label(false)->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?>
                            <?php
                            echo $form->field($model, "[data][{$model->id}]info")->label(false)->input('text', ['class' => 'form-default__input', 'placeholder' => 'коментар', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?>
                        </div>                </div>
                    <?php if (!in_array($flat->heat_metering, [AddrFlats::HEAT_METERING_INDIVIDUAL])): ?>
                        <div class="mobile-group form-default__row">
                            <div class="form-default__row"><?php
                                $model_claim = $settings->dataModels[CalculatorSettingsFlats::HAS_CLAIM];
                                $values = BaseActiveRecord::$valuesTrueFalse;
                                if (!$calculator->isAllowEdit($service_id) && isset($values[$model_claim->value])) {
                                    $values = [$model_claim->value => $values[$model_claim->value]];
                                }
                                echo $form->field($model_claim, "[data][{$model_claim->id}]value")->label($model_claim->getLabel(), ['class' => 'form-default__label'])->dropDownList($values, ['class' => 'js-dropdown-select form-default__select', 'onChange' => 'changeSettingsValue(' . CalculatorSettingsFlats::HAS_CLAIM . ',this)']);
                                ?>
                            </div>
                            <?php if (in_array($flat->heat_metering, [AddrFlats::HEAT_METERING_NONE, AddrFlats::HEAT_METERING_NONE_NON_LIVING])): ?>
                                <div class="js_value_<?php echo CalculatorSettingsFlats::HAS_CLAIM; ?>_1" <?php if ($model_claim->value <> '1'): ?>style="display:none;"<?php endif; ?>><?php
                                    $model = $settings->dataModels[CalculatorSettingsFlats::CLAIM_T_FACT];
                                    echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                                    ?>
                                </div>
                                <div class="js_value_<?php echo CalculatorSettingsFlats::HAS_CLAIM; ?>_1" <?php if ($model_claim->value <> '1'): ?>style="display:none;"<?php endif; ?>><?php
                                    $model = $settings->dataModels[CalculatorSettingsFlats::CLAIM_SQUARE];
                                    echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (in_array($flat->heat_metering, [AddrFlats::HEAT_METERING_NONE, AddrFlats::HEAT_METERING_NONE_NON_LIVING, AddrFlats::HEAT_METERING_INDIVIDUAL])): ?>
                        <div class="form-default__row"><?php
                            $model = $settings->dataModels[CalculatorSettingsFlats::INDIVIDUAL_HEAT_QTY];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?></div>
                    <?php endif; ?>
                    <div class="mobile-group form-default__row">
                        <div class="form-default__row"><?php
                            $model_inc = $settings->dataModels[CalculatorSettingsFlats::INC_QTY_STRATEGY];
                            $values = CalculatorSettingsFlats::$incQtyValues;
                            if (!$calculator->isAllowEdit($service_id) && isset($values[$model_inc->value])) {
                                $values = [$model_inc->value => $values[$model_inc->value]];
                            }
                            echo $form->field($model_inc, "[data][{$model_inc->id}]value")->label($model_inc->getLabel(), ['class' => 'form-default__label'])->dropDownList($values, ['class' => 'js-dropdown-select form-default__select', 'onChange' => 'changeSettingsValue(' . CalculatorSettingsFlats::INC_QTY_STRATEGY . ',this)']);
                            ?></div>
                        <div class="js_value_<?php echo CalculatorSettingsFlats::INC_QTY_STRATEGY; ?>_1" <?php if ($model_inc->value <> '1'): ?>style="display:none;"<?php endif; ?>><?php
                            $model = $settings->dataModels[CalculatorSettingsFlats::W_FACT];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?>
                        </div>
                        <div class="js_value_<?php echo CalculatorSettingsFlats::INC_QTY_STRATEGY; ?>_1" <?php if ($model_inc->value <> '1'): ?>style="display:none;"<?php endif; ?>><?php
                            $model = $settings->dataModels[CalculatorSettingsFlats::W_BASE];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?>
                        </div>
                        <div class="js_value_<?php echo CalculatorSettingsFlats::INC_QTY_STRATEGY; ?>_1" <?php if ($model_inc->value <> '1'): ?>style="display:none;"<?php endif; ?>><?php
                            $model = $settings->dataModels[CalculatorSettingsFlats::W_KTR];
                            echo $form->field($model, "[data][{$model->id}]value")->label($model->getLabel(), ['class' => 'form-default__label'])->input('text', ['class' => 'form-default__input', 'readonly' => !$calculator->isAllowEdit($service_id)]);
                            ?>
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
                                    <a href="<?php echo Url::toRoute(['preview', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => Services::ID_HEAT, 'fid' => $flat->id, 'period' => 'month']); ?>" class="btn btn-add-gray btn-profile w100" target="check_window">
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
<?php AppActiveForm::end(); ?>