<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActionColumn;
?>
<?php

$this->params['breadcrumbs'] = [
    $this->title = 'Цінові групи',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'title',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('title')],
        ],
        [
            'class' => AdminActionColumn::className(),
            'template' => '{view}',
            'contentOptions' => ['class' => 'actions pr-0'],
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-list"></i>', $url, ['class' => '', 'title' => 'товари', 'data-pjax' => 0]);
                },
            ],
        ],
    ],
]);
?>

