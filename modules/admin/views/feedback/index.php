<?php

use yii\helpers\Html;
use app\components\AdminActionColumn;

$this->params['breadcrumbs'] = [
    $this->title = 'Звернення користувачів',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'date',
            'format' => 'raw',
            'filter' => false,
            'value' => function ($data) {
                return $data->getDate();
            },
            'filterOptions' => ['class' => 'empty'],
        ],
        [
            'attribute' => 'subject_id',
            'format' => 'raw',
            'filter' => $model::$subjectLabels,
            'value' => function ($data) {
                return $data->getSubject();
            },
            'filterInputOptions' => ['class' => 'form-control', 'prompt' => 'всі теми'],
        ],
        [
            'header' => 'Контакти',
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getContacts();
            },
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('name')],
        ],
        [
            'attribute' => 'mess',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getMess();
            },
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('mess')],
        ],
        [
            'attribute' => 'status_id',
            'format' => 'raw',
            'filter' => $model::$statusLabels,
            'value' => function ($data) {
                return $data->getStatus();
            },
            'filterInputOptions' => ['class' => 'form-control', 'prompt' => 'всі статуси'],
        ],
        [
            'class' => AdminActionColumn::className(),
            'template' => '{status} {delete}',
            'contentOptions' => ['class' => 'actions text-right pr-0'],
            'buttons' => [
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-trash"></i>', $url, [
                        'title' => 'видалити',
                        'data-confirm-message' => yii\helpers\Html::encode('Ви підтверджуєте видалення запиту ' . $model->name . ' щодо ' . $model->getSubject() . '?'),
                    ]);
                },
                'status' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-refresh"></i>', $url, [
                        'title' => 'змінити статус',
                        'data-pjax' => '1',
                    ]);
                },
            ],
            'visibleButtons' => [
                'delete' => function ($model) {
                    return $model->isAllowDelete();
                },
            ],
        ],
    ],
]);
?>
