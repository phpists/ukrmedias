<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActionColumn;
?>
<?php

$this->params['breadcrumbs'] = [
    $this->title = 'Акції',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'date_from',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('date_from')],
            'value' => function ($model) {
                return $model->getDateFrom();
            }
        ],
        [
            'attribute' => 'date_to',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('date_to')],
            'value' => function ($model) {
                return $model->getDateTo();
            }
        ],
        [
            'class' => AdminActionColumn::className(),
            'template' => '{update} {view}',
            'contentOptions' => ['class' => 'actions pr-0'],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-edit"></i>', $url, ['class' => '', 'title' => 'редагування', 'data-pjax' => 0]);
                },
                'view' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-list"></i>', $url, ['class' => '', 'title' => 'товари', 'data-pjax' => 0]);
                },
            ],
        ],
    ],
]);
?>

