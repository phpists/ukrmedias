<?php

use app\models\Services;

$this->title = 'Рахунок №' . $invoice->getNumber();
$this->params['breadcrumbs'] = [
    ['label' => 'Рахунки', 'url' => ['index', 'id' => $invoice->service_id, 'year' => $invoice->getYearFrom(), 'month' => $invoice->getMonthFrom()]],
];
?>
<div class="h3 main-template__header">Рахунок №<?php echo $invoice->getNumber(); ?></div>
<?php echo $invoice->search_address; ?>
<br/>
<br/>
<div class="columns">
    <div class="column is-12-mobile">
        <div class="is-active card--auto">
            <div class="card__body card__body--gap is-active">
                <!-- BEGIN .accouting__wrapper -->
                <div class="accouting__wrapper  columns is-multiline">
                    <div class="column is-12 pt-5">
                        <?php echo $invoice->getInfo(); ?>
                    </div>
                </div>
                <!-- END .accouting__wrapper -->
            </div>
        </div>
    </div>
</div>
