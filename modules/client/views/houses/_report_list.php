<?php

use app\models\Calculator;
?>
<div class="biginfo--item-head columns ">
    <div class="biginfo--item-head__title column is-3-desktop is-6-tablet is-4-mobile text-medium"> Дата </div>
    <div class="biginfo--item-head__title column text-medium is-8-mobile"> Показання, <?php echo $unit; ?></div>
</div>
<div class="biginfo-body  ">
    <?php if (count($reportData) === 0): ?>
        <div class="biginfo--body-item columns  ">
            <div class="column">
                дані відсутні
            </div>
        </div>
    <?php endif; ?>
    <?php foreach ($reportData as $row): ?>
        <div class="biginfo--body-item columns  ">
            <div class="column is-3-desktop is-6-tablet is-4-mobile">
                <span class="biginfo-date"><?php echo $row['reportDate']; ?></span>
            </div>
            <div class="column is-8-mobile">
                <span class="biginfo-value"><?php echo $row['value']; ?></span>
                <?php if ($row['qty'] > 0): ?>
                    <span class="biginfo-value-percent biginfo-value-percent-top">
                        <?php echo "+{$row['qty']}"; ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>