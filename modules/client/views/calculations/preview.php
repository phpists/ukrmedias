<?php

use yii\helpers\Url;
use app\models\Services;
use app\models\Calculator;
use app\models\CalculatorTest7;

$this->title = 'Розрахунки';
$this->params['breadcrumbs'] = [
    ['label' => 'Адреси', 'url' => 'index'],
    ['label' => 'Розрахункові періоди', 'url' => ['period', 'id' => $house->id, 'aid' => $service_id]],
    ['label' => $calculator->getPeriod(), 'url' => ['details', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => $service_id, 'fid' => $flat === null ? null : $flat->id]],
    $flat === null ? "Будинок {$house->no}" : "Приміщення: {$flat->no}",
];
$flatList = $house->getClientsFlats();
$collapse = count($flatList) === 0;
?>
<div style="margin-bottom:24px;"></div>
<div class="main-template-receipts">
    <div class="adaptive-column preview">
        <div class="column-narrow">
            <div class="card  card-fix-height <?php if ($collapse): ?>collapsed<?php endif; ?>" id="accordion_card_left" style="width:<?php if ($collapse): ?>50<?php else: ?>300<?php endif; ?>px;">
                <div class="js-collapsed-block-alt">
                    <div class="h card__header-bg__grey">
                        <a id="js-chevron" class="card__chevron icon-grey js-collapse" href="javascript:void(0);"><svg class="icon icon-icon-chevron-24"><use xlink:href="img/sprite.svg#icon-icon-chevron-24"></use></svg></a>
                    </div>
                    <div class="js-collapsed-block-label"><?php echo $house->getTitle(); ?></div>
                </div>
                <div class="js-collapsed-block">
                    <div class="card__header <?php if ($flat === null): ?>card__header-bg__grey<?php else: ?>card__header-bg__green<?php endif; ?>">
                        <a href="<?php echo Url::toRoute(['preview', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => $service_id, 'period' => 'month']); ?>" data-pjax="0">
                            <span class="card__header-text__small  card__header-text__white <?php if ($flat === null): ?>js-active-text<?php endif; ?>"><?php echo $house->getTitle(); ?></span>
                        </a>
                        <a class="card__chevron icon-white js-collapse" href="javascript:void(0);" data-action="collapse"><svg class="icon icon-icon-chevron-24"><use xlink:href="img/sprite.svg#icon-icon-chevron-24"></use></svg></a>
                    </div>
                    <div class="card__body is-collapsible is-active card__body__gaped">
                        <?php foreach ($flatList as $dataModel): ?>
                            <div class="card__body-item <?php if ($flat && $dataModel->id == $flat->id): ?>card__header-bg__green js-active-text<?php endif; ?>">
                                <a href="<?php echo Url::toRoute(['preview', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => $service_id, 'fid' => $dataModel->id, 'period' => 'month']); ?>" class="card__body-link" data-pjax="0">
                                    <span class="card__body-number"> <?php echo $dataModel->no; ?> </span>
                                    <span class="card__body-text">
                                        <?php if (isset($abonents[$dataModel->id])) : ?>
                                            <?php echo $abonents[$dataModel->id]->getNameCombined(); ?>
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
                        <div class="h3"><?php echo $house->Invoice->details['period']; ?></div>
                    </div>
                </div>
            </div>
            <div class="w100 tabs-type__wrapper pt-0">
                <?php
                foreach (Services::$labels as $sid => $label):
                    if (in_array($sid, $client->houseServices) || in_array($sid, $client->services) && $client->isSupervisor()):
                        $params = ['preview', 'id' => $calculator->date, 'aid' => $house->id, 'sid' => $sid, 'period' => 'month'];
                        if ($flat !== null) {
                            $params['fid'] = $flat->id;
                        }
                        ?>
                        <a href="<?php echo Url::toRoute($params); ?>" class="tabs__head-item <?php if ($sid == $service_id): ?>is-active service<?php echo $sid; ?><?php endif; ?>" target="check_window">
                            <icon class="tabs__head-item--icon  "><?php echo Services::getIcon($sid); ?></icon>
                            <span class="tabs__head-item--text"><?php echo $label; ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php if ($flat === null): ?>
                <div class="card is-active card--auto card-auto m0 font14">
                    <a href="javascript:void(0);" class="card__header card__header-bg__white card__header--gap card__header--radius is-active">
                        <div class="flex-title-block w100 pr-5">
                            <div class="title-form h4 font14"><span><?php echo Services::getIcon($service_id); ?></span> <span><?php echo Services::$labels[$service_id]; ?></span></div>
                            <div><?php echo $house->getTitle(); ?></div>
                        </div>
                    </a>
                    <div class="card__body card__body--gap is-active is-collapsible text-dark" data-allow-multiple="true" aria-expanded="true">
                        <!-- BEGIN .accouting__wrapper -->
                        <div class="accouting__wrapper  columns is-multiline">
                            <div class="column is-12">
                                <?php
                                unset($house->Invoice->details['title'], $house->Invoice->details['period']);
                                echo implode('<br/>', $house->Invoice->details);
                                ?>
                            </div>
                        </div>
                        <!-- END .accouting__wrapper -->
                    </div>
                </div>
                <br/>
                <style>.text-danger{color:#dc3545;}.text-success{color:#28a745;}</style>
                <?php if ($service_id == Services::ID_HEAT): ?>
                    <table class="table table-striped table-bordered shadow">
                        <colgroup>
                            <col width="8%"/>
                            <col width=""/>
                            <col width="12%"/>
                            <col width="9%"/>
                            <col width="9%"/>
                            <col width="12%"/>
                            <col width="12%"/>
                            <col width="10%"/>
                            <col width="10%"/>
                            <col width="10%"/>
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Приміщення</th>
                                <th>Сума</th>
                                <th>За споживання</th>
                                <th>На ВБС</th>
                                <th>На МЗК</th>
                                <th>Донарахування</th>
                                <th>Перерахування</th>
                                <th>Зведення небалансу</th>
                                <th>Додаткові коригування</th>
                                <th>q <sub>пр-роз</sub></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            foreach ($house->getClientsFlats() as $flatModel):
                                $total += $flatModel->Invoice->getTotalAmount();
                                $altAmount = $flatModel->Invoice->getAmountMain() + $flatModel->Invoice->getAmountCommon() + $flatModel->Invoice->getAmountMZK() + $flatModel->Invoice->getAmountInc() - $flatModel->Invoice->getAmountDec() + $flatModel->Invoice->getAmountAdd() + $flatModel->Invoice->getAdjustmentAmount();
                                ?>
                                <tr>
                                    <td><?php echo $flatModel->no; ?></td>
                                    <td><?php echo CalculatorTest7::check(round($flatModel->Invoice->getTotalAmount(), 2), round($altAmount, 2)); ?></td>
                                    <td><?php echo $flatModel->Invoice->getAmountMain(); ?></td>
                                    <td><?php echo $flatModel->Invoice->getAmountCommon(); ?></td>
                                    <td><?php echo $flatModel->Invoice->getAmountMZK(); ?></td>
                                    <td><?php echo $flatModel->Invoice->getAmountInc(); ?></td>
                                    <td><?php echo $flatModel->Invoice->getAmountDecTxt(); ?></td>
                                    <td><?php echo $flatModel->Invoice->getAmountAdd(); ?></td>
                                    <td><?php echo $flatModel->Invoice->getAdjustmentAmount(); ?></td>
                                    <td><?php echo $flatModel->Invoice->q_pr_roz; ?></td>
                                </tr>
                                <?php
                            endforeach;
                            if ($house->Invoice->getAdjustmentAmount() <> 0):
                                $total += $house->Invoice->getAdjustmentAmount();
                                ?>
                                <tr>
                                    <td>Ручне коригування по будинку</td>
                                    <td><?php echo $house->Invoice->getAdjustmentAmount(); ?></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td>Всього:</td>
                                <td><?php echo CalculatorTest7::check(round($total, 2), round($house->Invoice->getTotalAmount(), 2)); ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                <?php else: ?>
                    <table class="table table-striped table-bordered shadow" style="width:auto;">
                        <colgroup>
                            <col width=""/>
                            <col width="12%"/>
                            <col width="11%"/>
                            <col width="11%"/>
                            <col width="11%"/>
                            <col width="11%"/>
                            <col width="11%"/>
                            <col width="11%"/>
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Приміщення</th>
                                <th>Споживання, куб.м</th>
                                <th>Сума</th>
                                <th>За постачання</th>
                                <th>За водовідведення</th>
                                <th>За витік</th>
                                <th>Зведення небалансу</th>
                                <th>Додаткові коригування</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $qty = 0;
                            $total = 0;
                            foreach ($house->getClientsFlats() as $flatModel):
                                $flatQty = $flatModel->Invoice->getWaterQty() + $flatModel->Invoice->getWaterQtyAdd();
                                $qty += $flatQty;
                                $total += $flatModel->Invoice->getTotalAmount();
                                $altAmount = $flatModel->Invoice->getWaterAmountSupply() + $flatModel->Invoice->getWaterAmountDrain()+ $flatModel->Invoice->getAmountInc()+ $flatModel->Invoice->getAmountAdd() + $flatModel->Invoice->getAdjustmentAmount();
                                ?>
                                <tr>
                                    <td><?php echo $flatModel->no; ?></td>
                                    <td><?php echo $flatQty; ?></td>
                                    <td><?php echo CalculatorTest7::check(round($flatModel->Invoice->getTotalAmount(), 2), round($altAmount, 2)); ?></td>
                                    <td><?php echo $flatModel->Invoice->getWaterAmountSupply(); ?></td>
                                    <td><?php echo $flatModel->Invoice->getWaterAmountDrain(); ?></td>
                                    <td><?php echo $flatModel->Invoice->getAmountInc(); ?></td>
                                    <td><?php echo $flatModel->Invoice->getAmountAdd(); ?></td>
                                    <td><?php echo $flatModel->Invoice->getAdjustmentAmount(); ?></td>
                                </tr>
                                <?php
                            endforeach;
                            if ($house->Invoice->getWaterCommonQty() > 0):
                                $qty += $house->Invoice->getWaterCommonQty();
                                $total += $house->Invoice->getWaterAmountCommon();
                                ?>
                                <tr>
                                    <td>На загальнобудинкові потреби</td>
                                    <td><?php echo $house->Invoice->getWaterCommonQty(); ?></td>
                                    <td><?php echo $house->Invoice->getWaterAmountCommon(); ?></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td></td>
                                </tr>
                                <?php
                            endif;
                            if ($house->Invoice->getAdjustmentAmount() <> 0):
                                $total += $house->Invoice->getAdjustmentAmount();
                                ?>
                                <tr>
                                    <td>Ручне коригування по будинку</td>
                                    <td>-</td>
                                    <td><?php echo $house->Invoice->getAdjustmentAmount(); ?></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td>Всього:</td>
                                <td><?php echo CalculatorTest7::check(round($qty, Calculator::ROUND_WATER_QTY), round($house->Invoice->qty, Calculator::ROUND_WATER_QTY)); ?></td>
                                <td><?php echo CalculatorTest7::check(round($total, 2), round($house->Invoice->getTotalAmount(), 2)); ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                <?php endif; ?>
                <br/>

            <?php endif; ?>
            <?php
            foreach ($house->getClientsFlats() as $flatModel) :
                if ($flat !== null && $flatModel->id !== $flat->id) {
                    continue;
                }
                ?>
                <div class="card is-active card--auto card-auto m0 font14">
                    <a href="javascript:void(0);" class="card__header card__header-bg__white card__header--gap card__header--radius is-active">
                        <div class="flex-title-block w100 pr-5">
                            <div class="title-form h4 font14"><span><?php echo Services::getIcon($service_id); ?></span> <span><?php echo Services::$labels[$service_id]; ?></span></div>
                            <div><?php echo $flatModel->getTitle(); ?></div>
                        </div>
                    </a>
                    <div id="collapsible-message-accordion-second-<?php echo $flatModel->id; ?>" class="card__body card__body--gap is-active is-collapsible" data-allow-multiple="true" aria-expanded="true">
                        <!-- BEGIN .accouting__wrapper -->
                        <div class="accouting__wrapper  columns is-multiline text-dark">
                            <div class="column is-12">
                                <?php
                                unset($flatModel->Invoice->details['title'], $flatModel->Invoice->details['period']);
                                echo implode('<br/>', $flatModel->Invoice->details);
                                ?>
                            </div>
                        </div>
                        <!-- END .accouting__wrapper -->
                    </div>
                </div>
                <br/>
            <?php endforeach; ?>
        </div>
    </div>
</div>