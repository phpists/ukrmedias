<?php

use app\models\Users;

$this->beginContent("@app/modules/client/views/invoices/{$layout}.php");
$settings = $address->getSettings($invoice->from);
$total = 0;
$device_type_id = $address->getDeviceTypeId();
?>
<div class="page flex-column">
    <div class="flex-row space-between">
        <div>
            <div><?php echo $client->getTitle(); ?></div>
            <div>ЄДРПОУ: <?php echo $client->getCode(); ?></div>
            <div>Банк: <?php echo $client->getBank(); ?></div>
            <div>IBAN: <?php echo $client->getIban(); ?></div>
        </div>
        <div>
            <div><?php echo $address->getTitle(); ?></div>
            <?php if ($abonent): ?>
                <?php if ($abonent->type_id == Users::TYPE_LEGAL_PERSON): ?>
                    <div><?php echo $abonent->getTitle(); ?></div>
                <?php endif; ?>
                <div><?php echo $abonent->getName(); ?></div>
            <?php endif; ?>
            <div>Тип обліку в приміщенні: <?php echo $address->getHeatingMetering(); ?></div>
            <div>Загальна площа: <?php echo $address->getSquare(); ?> кв.м</div>
        </div>
    </div>
    <div>&nbsp;</div>
    <div class="bold"><span class="upper">Повідомленя-рахунок</span> на оплату послуг за <?php echo $invoice->getMonthFromTxt(); ?> <?php echo $invoice->getYearFrom(); ?></div>
    <div>&nbsp;</div>
    <div>Загальна сумма спожитої теплової енергії будинком за <?php echo $invoice->getMonthFromTxt(); ?> <?php echo $invoice->getYearFrom(); ?>: <?php echo $houseSettings->getValue(app\models\CalculatorSettingsHouses::Q_OP_BUD); ?> ГКал</div>
    <div>Тариф на постачання теплової енергії: <?php echo $houseSettings->getValue(app\models\CalculatorSettingsHouses::HEAT_PRICE); ?> грн./ГКал</div>
    <div>&nbsp;</div>
    <div>
        <table class="details" border="0" cellspacing="0" cellpadding="4">
            <thead>
                <tr class="centered">
                    <th>Послуга</th>
                    <th>Сума, грн.</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Споживаня теплової енергії приміщенням абонента</td>
                    <td class="price">
                        <?php
                        echo $amount = $invoice->getAmountMain();
                        $total += $amount;
                        ?>
                    </td>
                </tr>
                <?php if ($invoice->flat_id > 0): ?>
                    <tr>
                        <td>Витрати на обслуговування внутрішньобудинкової системи опалення</td>
                        <td class="price">
                            <?php
                            echo $amount = $invoice->getAmountCommon();
                            $total += $amount;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Витрати на опалення МЗК та допоміжних приміщень</td>
                        <td class="price">
                            <?php
                            echo $amount = $invoice->getAmountMzk();
                            $total += $amount;
                            ?>
                        </td>
                    </tr>
                    <?php if ($invoice->qty_inc <> 0): ?>
                        <tr>
                            <td>Донарахування до мінімального споживання</td>
                            <td class="price">
                                <?php
                                echo $amount = $invoice->getAmountInc();
                                $total += $amount;
                                ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($invoice->amount_dec <> 0): ?>
                        <tr>
                            <td>Перерахування</td>
                            <td class="price">
                                <?php
                                echo '-', $amount = $invoice->getAmountDec();
                                $total -= $amount;
                                ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($invoice->qty_add <> 0): ?>
                        <tr>
                            <td>Донарахування після зведення балансу</td>
                            <td class="price">
                                <?php
                                echo $amount = $invoice->getAmountAdd();
                                $total += $amount;
                                ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>
                <?php
                if ($invoice->adjustment <> 0):
                    $total += $invoice->adjustment;
                    ?>
                    <tr>
                        <td><?php echo $invoice->comment; ?></td>
                        <td class="price"><?php echo $invoice->adjustment; ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td class="price">До сплати:</td>
                    <td class="price"><?php echo \app\components\Misc::round($total); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>&nbsp;</div>
    <?php if ($invoice->flat_id > 0 && $device_type_id === app\models\Devices::TYPE_UNIVERSAL): ?>
        <table class="details" border="0" cellspacing="0" cellpadding="4">
            <thead>
                <tr class="centered">
                    <th rowspan="2">Номер лічильника</th>
                    <th colspan="2">Показники, Гкал</th>
                    <th rowspan="2">Витрати, Гкал</th>
                    <th rowspan="2">Поправковий коєфіцієнт</th>
                    <th rowspan="2">Тариф, грн./Гкал</th>
                    <th rowspan="2">Нараховано, грн.</th>
                </tr>
                <tr class="centered">
                    <th>початкові</th>
                    <th>кінцеві</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($hardwareDetails as $row):
                    $invoice->apiData[] = [
                        'counter' => $row['number'],
                        'start_value' => $row['start_value'],
                        'end_value' => $row['value'],
                        'qty' => $row['qty'],
                        'q_pr_roz' => null,
                        'ck' => $row['ck'],
                        'heat_k' => null,
                    ];
                    ?>
                    <tr>
                        <td><?php echo $row['number']; ?></td>
                        <td><?php echo $row['start_value']; ?></td>
                        <td><?php echo $row['value']; ?></td>
                        <td class="centered"><?php echo $row['qty']; ?></td>
                        <td class="centered"><?php echo $row['ck']; ?></td>
                        <td class="price"><?php echo $invoice->price_main; ?></td>
                        <td class="price"><?php echo $invoice->getAmountMain(); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($invoice->flat_id > 0 && $device_type_id === app\models\Devices::TYPE_DISTRIBUTOR): ?>
        <table class="details" border="0" cellspacing="0" cellpadding="4">
            <thead>
                <tr class="centered">
                    <th rowspan="2">Номер розподілювача</th>
                    <th colspan="2">Показники, у.о.</th>
                    <th rowspan="2">Витрати, у.о.</th>
                    <th rowspan="2">Радіаторний коефіцієнт</th>
                    <th rowspan="2">Поправковий коєфіцієнт</th>
                    <th rowspan="2">Питомий обсяг спожитої теплової енергії на опалення визначений на 1 у.о., Гкал</th>
                    <th rowspan="2">Розрахункове споживання, Гкал</th>
                    <th rowspan="2">Тариф, грн./Гкал</th>
                    <th rowspan="2">Нараховано, грн.</th>
                </tr>
                <tr class="centered">
                    <th>початкові</th>
                    <th>кінцеві</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                $totalQty = 0;
                foreach ($hardwareDetails as $row):
                    $invoice->apiData[] = [
                        'counter' => $row['number'],
                        'start_value' => $row['start_value'],
                        'end_value' => $row['value'],
                        'qty' => $row['qty'],
                        'q_pr_roz' => $invoice->q_pr_roz,
                        'ck' => $row['ck'],
                        'heat_k' => $row['heat_k'],
                    ];
                    $totalQty += $qty = round($row['qty'] * $row['heat_k'] * $row['ck'] * $invoice->q_pr_roz, \app\models\Calculator::ROUND_HEAT_QTY);
                    $total += $amount = round($qty * $invoice->price_main, 2);
                    ?>
                    <tr>
                        <td><?php echo $row['number']; ?></td>
                        <td><?php echo $row['start_value']; ?></td>
                        <td><?php echo $row['value']; ?></td>
                        <td class="centered"><?php echo $row['qty']; ?></td>
                        <td class="centered"><?php echo $row['heat_k']; ?></td>
                        <td class="centered"><?php echo $row['ck']; ?></td>
                        <td class="price"><?php echo $invoice->q_pr_roz; ?></td>
                        <td class="price"><?php echo $qty; ?></td>
                        <td class="price"><?php echo $invoice->price_main; ?></td>
                        <td class="price"><?php echo $amount; ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="7" class="price">Сума:</td>
                    <td class="price"><?php echo $totalQty; ?></td>
                    <td class="price"></td>
                    <td class="price"><?php echo $total; ?></td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
    <div class="note">
        * Загальна сума до сплати розраховується за показниками вузла комерційного обліку (загальнобудинковий лічильник),
        з урахуванням показань вузлів розподільчого обліку (індивідуальних лічильників/розподілюввачів) або, у разі їх відсутності, за загальними нормами споживання.
    </div>
</div>
<?php $this->endContent(); ?>