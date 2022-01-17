<?php

use app\models\Services;

$this->params['breadcrumbs'] = [
    ['label' => 'Розрахункові періоди', 'url' => 'index'],
];
$this->title = 'Поточний стан створення рахунків';
?>
<div class="main-template-employ main-template-address">
    <div class="flex-title-block">
        <div class="h3 main-template__header"><?php echo $this->title; ?></div>
        <div class="h5 main-template__header"><?php echo $calculator->getPeriod(); ?></div>
    </div>
    <?php
    foreach ($services as $service_id):
        $attr = "info_{$service_id}";
        if (strlen($calculator->{$attr}) === 0) {
            continue;
        }
        ?>
        <div class="columns">
            <div class="column is-12-mobile">
                <div class="card is-active card--auto">
                    <div class="card__body card__body--gap pt-3 is-active">
                        <!-- BEGIN .accouting__wrapper -->
                        <div class="tags mb-2"><?php echo Services::getIcon($service_id); ?> <?php echo Services::$labels[$service_id]; ?>:</div>
                        <?php echo nl2br($calculator->{$attr}); ?>
                        <!-- END .accouting__wrapper -->
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
