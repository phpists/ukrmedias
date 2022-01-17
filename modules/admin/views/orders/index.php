<?php

use yii\helpers\Html;
use app\components\AdminActionColumn;
use app\components\Auth;
use app\models\PreOrders;
?>
<?php

$this->params['breadcrumbs'] = [
    $this->title = 'Замовлення',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'request_id',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('number')],
            'value' => function ($data) {
                return $data->getAdminRequestLink();
            },
        ],
        [
            'attribute' => 'number',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('number')],
        ],
        [
            'attribute' => 'date',
            'format' => 'raw',
            'filter' => false,
            'value' => function ($data) {
                return $data->getDate();
            },
        ],
        [
            'attribute' => 'firm_id',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getFirmTitle();
            },
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('firm_id')],
        ],
        [
            'attribute' => 'amount',
            'format' => 'raw',
            'filter' => false,
        ],
        [
            'attribute' => 'discount',
            'format' => 'raw',
            'filter' => false,
        ],
        [
            'attribute' => 'status_id',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getStatus();
            },
            'filter' => $model::$statusLabels,
            'filterInputOptions' => ['class' => 'form-control', 'prompt' => 'всі статуси'],
        ],
        [
            'class' => AdminActionColumn::className(),
            'template' => '{view} {xls} {np} {np_old}',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-list"></i>', $url, ['class' => '', 'title' => 'деталі', 'data-pjax' => 0]);
                },
                'xls' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-file-excel-o"></i>', $url, ['class' => '', 'title' => 'скачати в Excel', 'data-pjax' => 0]);
                },
                'np' => function ($url, $model, $key) {
                    return Html::a('<b style="color:red;">НП</b>', $url, ['class' => '', 'title' => 'Створити експрес-накладну', 'data-pjax' => 0]);
                },
                'np_old' => function ($url, $model, $key) {
                    return Html::a('<b style="color:gray;">НП</b>', 'javascript:void(0);', ['class' => '', 'title' => "Експрес-накладна {$model->np_express_num}", 'data-pjax' => 0]);
                },
            ],
            'visibleButtons' => [
                'np' => function ($model) {
                    return $model->isAllowNP();
                },
                'np_old' => function ($model) {
                    return $model->isOldNP();
                },
            ],
            'contentOptions' => ['class' => 'actions text-right pr-0'],
        ],
    ],
]);
?>

