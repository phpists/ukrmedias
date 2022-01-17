<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Services;
use app\models\Devices;
use app\models\AddrHouses;
use app\models\CalculatorSettingsHouses;
?>
<div class="card card--white card--pad10 card-auto">
    <div class="columns is-multiline">
        <?php if (!$model->isAllowSend($service_id) && !$model->isAllowMake($service_id) && !$model->isCreated($service_id)): ?>
            <div class="column is-12 is-12-mobile">
                <div class="card-header-title">
                    <?php echo $model->getPeriod(); ?>
                </div>
            </div>
        <?php endif; ?>
        <?php
        if ($model->isAllowMake($service_id)):
            $settingsModel = CalculatorSettingsHouses::getDefaultModel($model->date, Yii::$app->user->identity->client_id, $house->id, $service_id);
            ?>
            <div class="column is-12 is-12-mobile period-column">
                <div class="card-header-title">
                    <?php echo $model->getPeriod(); ?>
                </div>
            </div>
            <div class="column is-12 is-12-mobile">
                <div class="form-default__row">
                    <div class="form-group field-users-second_name">
                        <label class="form-default__label">споживання згідно показників вузла комерційного обліку</label>
                    </div>
                </div>
                <div class="form-default__row">
                    <div class="columns is-multiline">
                        <div class="column is-12 is-12-mobile">
                            <div class="form-group action-wrap adaptive-control-group">
                                <input id="qty<?php echo $index; ?>" type="text" id="users-second_name" class="form-default__input adaptive-control_1 h30 period-qty" name="qty" value="<?php echo $settingsModel->value; ?>"
                                       style="vertical-align:middle;padding:3px 5px;">
                                       <?php
                                       echo Html::tag('span', 'внести показання', [
                                           'class' => 'btn btn-m-size cursor adaptive-control_2',
                                           'data-make' => "#make{$index}",
                                           'data-input' => "#qty{$index}",
                                           'data-url' => Url::toRoute(['set-qty', 'date' => $model->date, 'id' => $house->id, 'sid' => $service_id]),
                                           'onClick' => 'setQty(this)',
                                       ]);
                                       ?>
                                       <?php
                                       echo Html::a('виставити рахунки', ['start', 'id' => $model->date, 'aid' => $house->id, 'sid' => $service_id], [
                                           'id' => "make{$index}",
                                           'data-pjax' => 0,
                                           'data-input' => "#qty{$index}",
                                           'class' => 'btn btn-m-size js_special_confirm adaptive-control_3',
                                           'onClick' => 'checkQty(event, this)',
                                           'style' => $settingsModel->value === '' ? 'display:none;' : 'display:inline-flex;',
                                           'data-message' => 'Дія зі створення рахунків абонентам не може бути відмінена. Ви підтверджуєте виконання цієї дії?',
                                           'data-alt-label' => 'Змінити параметри розахунків',
                                           'data-alt-url' => Url::to(['details', 'id' => $model->date, 'aid' => $house->id, 'sid' => $service_id]),
                                       ]);
                                       ?>
                                <a href="<?php echo Url::toRoute(['details', 'id' => $model->date, 'aid' => $house->id, 'sid' => $service_id]); ?>" data-pjax="0" class="btn btn-m-size adaptive-control_3 nowrap">параметри</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($model->isAllowSend($service_id) || $model->isAllowMake($service_id) || $model->isCreated($service_id)): ?>
            <div class="column is-12 is-12-mobile period-column">
                <div class="card-header-title">
                    <?php echo $model->getPeriod(); ?>
                </div>
            </div>
            <div class="column is-12 is-12-mobile">
                <div class="form-default__row">
                    <div class="form-group action-wrap adaptive-control-group">
                        <label class="form-default__label w100"><?php echo $model->getInfo($service_id); ?></label>
                    </div>
                </div>
                <div class="form-default__row">
                    <div class="form-group action-wrap adaptive-control-group">
                        <?php if ($model->isAllowSend($service_id)): ?>
                            <a href="<?php echo Url::toRoute(['send', 'id' => $model->date, 'aid' => $house->id, 'sid' => $service_id]); ?>" data-pjax="0"
                               class="btn btn-m-size adaptive-control_3 nowrap">
                                надіслати рахунки
                            </a>
                        <?php endif; ?>
                        <?php if ($model->isAllowMake($service_id) || $model->isCreated($service_id)): ?>
                            <a href="<?php echo Url::toRoute(['details', 'id' => $model->date, 'aid' => $house->id, 'sid' => $service_id]); ?>" data-pjax="0" class="btn btn-m-size adaptive-control_3 nowrap">параметри</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>