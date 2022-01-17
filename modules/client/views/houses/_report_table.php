<?php

use app\models\Calculator;
?>
<div class="biginfo--item-head columns">
    <div class="biginfo--item-head__title column is-3-desktop text-medium"> Дата </div>
    <div class="biginfo--item-head__title column is-3-desktop text-medium"> Початок періоду, <?php echo $unit; ?></div>
    <div class="biginfo--item-head__title column is-3-desktop text-medium"> Витрата, <?php echo $unit; ?></div>
    <div class="biginfo--item-head__title column is-3-desktop text-medium"> Кінець періоду, <?php echo $unit; ?></div>
</div>
<div class="biginfo-body">
    <?php if (count($reportData) === 0): ?>
        <div class="biginfo--body-item columns  ">
            <div class="column">
                дані відсутні
            </div>
        </div>
    <?php endif; ?>
    <?php foreach ($reportData as $row): ?>
        <div class="biginfo--body-item columns  ">
            <div class="column is-3-desktop is-6-tablet">
                <span class="biginfo-date"><?php echo $row['reportDate']; ?></span>
            </div>
            <div class="column is-3-desktop">
                <span class="biginfo-value"><?php echo $row['prev_value']; ?></span>
            </div>
            <div class="column is-3-desktop">
                <span class="biginfo-value"><?php echo $row['qty'] > 0 ? "+{$row['qty']}" : 0; ?></span>
            </div>
            <div class="column is-3-desktop">
                <span class="biginfo-value"><?php echo $row['value']; ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>