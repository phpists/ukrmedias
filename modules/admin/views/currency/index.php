<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActionColumn;
?>
<?php
$this->params['breadcrumbs'] = [
    $this->title = 'Курси валют',
];
?>
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-4">
        <?php
        echo app\components\AdminGrid::widget([
            'filterModel' => $model,
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'currency',
                    'format' => 'raw',
                    'filter' => $model::$labels,
                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('currency')],
                ],
                [
                    'attribute' => 'date',
                    'format' => 'raw',
                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('date')],
                    'value' => function ($model) {
                        return $model->getDate();
                    }
                ],
                [
                    'attribute' => 'rate',
                    'format' => 'raw',
                    'filter' => false,
                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('rate')],
                ],
            ],
        ]);
        ?>
    </div>
</div>
