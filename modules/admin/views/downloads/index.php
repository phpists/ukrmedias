<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActionColumn;
?>
<?php
$this->params['breadcrumbs'] = [
    $this->title = 'Завантаження',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'brand_id',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('brand_id')],
            'value' => function ($model) {
                return $model->getBrandTitle();
            }
        ],
        [
            'attribute' => 'type_id',
            'format' => 'raw',
            'filter' => $model::$typeLabels,
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('type_id')],
            'value' => function ($model) {
                return $model->getType();
            }
        ],
        [
            'attribute' => 'title',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('title')],
        ],
        [
            'class' => AdminActionColumn::className(),
            'template' => '{update} {delete}',
            'contentOptions' => ['class' => 'actions pr-0'],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-edit"></i>', $url, ['class' => '', 'title' => 'редагування', 'data-pjax' => 0]);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-trash"></i>', $url, [
                        'title' => 'видалити',
                        'data-confirm-message' => Html::encode('Ви підтверджуєте видалення документу ' . Html::encode($model->title) . '?'),
                    ]);
                },
            ],
        ],
    ],
]);
?>
<a class="btn btn-sm btn-primary mb-5" href="<?php echo Url::toRoute('update'); ?>"><i class="fa fa-plus"></i> додати</a>