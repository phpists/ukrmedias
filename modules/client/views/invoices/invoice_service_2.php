<?php

use app\models\Users;

$this->beginContent("@app/modules/client/views/invoices/{$layout}.php");
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
        </div>
    </div>
    <div>&nbsp;</div>
    <div class="bold"><span class="upper">Повідомленя-рахунок</span> на оплату послуг за <?php echo $invoice->getMonthFromTxt(); ?> <?php echo $invoice->getYearFrom(); ?></div>
    <div>&nbsp;</div>
    <div>
        <?php if (count($hardwareDetails) > 0): ?>
            <table class="details" border="0" cellspacing="0" cellpadding="4">
                <thead>
                    <tr class="centered">
                        <th rowspan="2">Послуга</th>
                        <th rowspan="2">Номер лічильника</th>
                        <th colspan="2">Показники, куб.м</th>
                        <th rowspan="2">Витрати, куб.м</th>
                        <th rowspan="2">Тариф, грн.</th>
                        <th rowspan="2">Нараховано, грн.</th>
                        <th rowspan="2">Коригування небалансу, грн.</th>
                        <th rowspan="2">Сума, грн.</th>
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
                        ];
                        ?>
                        <tr>
                            <td>Централізоване постачання гарячої води</td>
                            <td><?php echo $row['number']; ?></td>
                            <td><?php echo $row['start_value']; ?></td>
                            <td><?php echo $row['value']; ?></td>
                            <td class="centered"><?php echo $row['qty']; ?></td>
                            <td class="price"><?php echo $invoice->price_main; ?></td>
                            <td class="price"><?php echo $amountMain = round($row['qty'] * $invoice->price_main, 2); ?></td>
                            <td class="price"><?php echo $amountAdd = round($row['percent'] * ($invoice->qty_add * $invoice->price_main), 2); ?></td>
                            <td class="price">
                                <?php
                                echo $amount = $amountMain + $amountAdd;
                                $total += $amount;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Централізоване водовідведення гарячої води</td>
                            <td><?php echo $row['number']; ?></td>
                            <td><?php echo $row['start_value']; ?></td>
                            <td><?php echo $row['value']; ?></td>
                            <td class="centered"><?php echo $row['qty']; ?></td>
                            <td class="price"><?php echo $invoice->price_drain; ?></td>
                            <td class="price"><?php echo $amountMain = round($row['qty'] * $invoice->price_drain, 2); ?></td>
                            <td class="price"><?php echo $amountAdd = round($row['percent'] * ($invoice->qty_add * $invoice->price_drain), 2); ?></td>
                            <td class="price">
                                <?php
                                echo $amount = $amountMain + $amountAdd;
                                $total += $amount;
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($invoice->qty_inc > 0): ?>
                        <tr>
                            <td>Зареєстрований витік води</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td class="centered"><?php echo $invoice->qty_inc; ?></td>
                            <td class="price"><?php echo $invoice->price_main; ?></td>
                            <td class="price"><?php echo $amountMain = round($invoice->qty_inc * $invoice->price_main, 2); ?></td>
                            <td class="price">-</td>
                            <td class="price">
                                <?php
                                echo $amount = $amountMain + $amountAdd;
                                $total += $amount;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Зареєстрований витік води (водовідведення)</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td class="centered"><?php echo $invoice->qty_inc; ?></td>
                            <td class="price"><?php echo $invoice->price_drain; ?></td>
                            <td class="price"><?php echo $amountMain = round($invoice->qty_inc * $invoice->price_drain, 2); ?></td>
                            <td class="price">-</td>
                            <td class="price">
                                <?php
                                echo $amount = $amountMain + $amountAdd;
                                $total += $amount;
                                ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php
                    if ($invoice->adjustment <> 0):
                        $total += $invoice->adjustment;
                        ?>
                        <tr>
                            <td colspan="8" class="price"><?php echo $invoice->comment; ?></td>
                            <td class="price"><?php echo $invoice->adjustment; ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="8" class="price">До сплати:</td>
                        <td class="price"><?php echo \app\components\Misc::round($total); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <table class="details" border="0" cellspacing="0" cellpadding="4">
                <thead>
                    <tr class="centered">
                        <th>Послуга</th>
                        <th>Витрати, куб.м</th>
                        <th>Тариф, грн.</th>
                        <th>Нараховано, грн.</th>
                        <th>Коригування небалансу, грн.</th>
                        <th>Сума, грн.</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Централізоване постачання холодної води</td>
                        <td class="centered"><?php echo $invoice->qty; ?></td>
                        <td class="price"><?php echo $invoice->price_main; ?></td>
                        <td class="price"><?php echo $amountMain = round($invoice->qty * $invoice->price_main, 2); ?></td>
                        <td class="price"><?php echo $amountAdd = round($invoice->qty_add * $invoice->price_main, 2); ?></td>
                        <td class="price">
                            <?php
                            echo $amount = $amountMain + $amountAdd;
                            $total = $amount;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Централізоване водовідведення холодної води</td>
                        <td class="centered"><?php echo $invoice->qty; ?></td>
                        <td class="price"><?php echo $invoice->price_drain; ?></td>
                        <td class="price"><?php echo $amountMain = round($invoice->qty * $invoice->price_drain, 2); ?></td>
                        <td class="price"><?php echo $amountAdd = round($invoice->qty_add * $invoice->price_drain, 2); ?></td>
                        <td class="price">
                            <?php
                            echo $amount = $amountMain + $amountAdd;
                            $total += $amount;
                            ?>
                        </td>
                    </tr>
                    <?php if ($invoice->qty_inc > 0): ?>
                        <tr>
                            <td>Зареєстрований витік води</td>
                            <td class="centered"><?php echo $invoice->qty_inc; ?></td>
                            <td class="price"><?php echo $invoice->price_main; ?></td>
                            <td class="price"><?php echo $amountMain = round($invoice->qty_inc * $invoice->price_main, 2); ?></td>
                            <td class="price">-</td>
                            <td class="price">
                                <?php
                                echo $amount = $amountMain + $amountAdd;
                                $total += $amount;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Зареєстрований витік води (водовідведення)</td>
                            <td class="centered"><?php echo $invoice->qty_inc; ?></td>
                            <td class="price"><?php echo $invoice->price_drain; ?></td>
                            <td class="price"><?php echo $amountMain = round($invoice->qty_inc * $invoice->price_drain, 2); ?></td>
                            <td class="price">-</td>
                            <td class="price">
                                <?php
                                echo $amount = $amountMain + $amountAdd;
                                $total += $amount;
                                ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php
                    if ($invoice->adjustment <> 0):
                        $total += $invoice->adjustment;
                        ?>
                        <tr>
                            <td colspan="5" class="price"><?php echo $invoice->comment; ?></td>
                            <td class="price"><?php echo $invoice->adjustment; ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="5" class="price">До сплати:</td>
                        <td class="price"><?php echo \app\components\Misc::round($total); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <div>&nbsp;</div>
    <div>Тариф на послуги центрального постачання горячої води (за умови підключенної рушникосушарки) - <?php echo $houseSettings->getValue(app\models\CalculatorSettingsHouses::HOT_WATER_PRICE); ?> за 1 куб.м (з ПДВ)</div>
    <div>Тариф на послуги центрального постачання горячої води (за умови відсутності рушникосушарки) - <?php echo $houseSettings->getValue(app\models\CalculatorSettingsHouses::HOT_WATER_PRICE_HEAT_TOWEL); ?> за 1 куб.м (з ПДВ)</div>
    <div>Тариф на послуги центрального водовідведення гарячої води - <?php echo $invoice->price_drain; ?> за 1 куб.м (з ПДВ)</div>
    <div>&nbsp;</div>
    <div class="note">
        * Загальна сума до сплати розраховується за показниками вузла комерційного обліку (загальнобудинковий лічильник),
        з урахуванням показань вузлів розподільчого обліку (індивідуальних лічильників) або, у разі їх відсутності, за загальними нормами споживання.
    </div>
</div>
<?php $this->endContent(); ?>