<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActionColumn;

?>
<?php

$this->params['breadcrumbs'] = [
    $this->title = 'Варіанти доставки',
];
echo app\components\AdminGrid::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'title',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('title')],
        ],
        [
            'attribute' => 'price',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('title')],
        ],
        [
            'attribute' => 'amount',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('title')],
        ],
        [
            'class' => AdminActionColumn::className(),
            'template' => '{update}',
            'contentOptions' => ['class' => 'actions pr-0'],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-edit"></i>', $url, ['class' => '', 'title' => 'редагування', 'data-pjax' => 0]);
                },
            ],
        ],
    ],
]);
?>
