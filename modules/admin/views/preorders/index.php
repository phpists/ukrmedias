<?php

use yii\helpers\Html;
use app\components\AdminActionColumn;
use app\components\Auth;
use app\models\PreOrders;
?>
<?php
$this->params['breadcrumbs'] = [
    $this->title = 'Заявки',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'id',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('id')],
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
            'template' => '{view} {update} {delete}',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-list"></i>', $url, ['class' => '', 'title' => 'деталі', 'data-pjax' => 0]);
                },
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-edit"></i>', $url, ['class' => '', 'title' => 'редагування', 'data-pjax' => 0]);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-trash"></i>', $url, [
                        'title' => 'видалити',
                        'data-confirm-message' => yii\helpers\Html::encode('Ви підтверджуєте видалення заявки ' . $model->getNumber() . '?'),
                    ]);
                },
            ],
            'visibleButtons' => [
                'view' => function ($model) {
                    return $model->isAllowView();
                },
                'update' => function ($model) {
                    return $model->isAllowUpdate();
                },
                'delete' => function ($model) {
                    return $model->isAllowDelete();
                },
            ],
            'contentOptions' => ['class' => 'actions text-right pr-0'],
        ],
    ],
]);
?>
<a class="btn btn-sm btn-primary mb-5" href="<?php echo yii\helpers\Url::toRoute('create'); ?>"><i class="fa fa-plus"></i> нова заявка</a>
