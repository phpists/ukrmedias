<?php

use yii\helpers\Url;
use app\components\AppListView;

$this->params['breadcrumbs'] = [
    ['label' => 'Розрахункові періоди', 'url' => 'index'],
];
$this->title = 'Адреси на обслуговуванні';
?>
<div class="main-template-employ main-template-address">
    <div class="flex-title-block">
        <div class="h3 main-template__header"><?php echo $this->title; ?></div>
        <div class="h5 main-template__header"><?php echo $calculator->getPeriod(); ?></div>
    </div>
    <?php
    echo AppListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_house',
        'searchModel' => $model,
        'headerAttrs' => ['search_city', 'search_street', 'Будинок', 'clientNote'],
        #'filter' => ['second_name' => 'text', 'active' => Users::$statusLabels],
        'viewParams' => ['services' => $services, 'date' => $date],
    ]);
    ?>
</div>
