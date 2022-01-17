<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActionColumn;
?>
<?php
$this->params['breadcrumbs'] = [
    ['label' => 'Цінові групи', 'url' => 'index'],
    $this->title = $model->title,
];
echo app\components\AdminGrid::widget([
    'filterModel' => $goodsModel,
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'title',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $goodsModel->getAttributeLabel('title')],
        ],
        [
            'attribute' => 'article',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $goodsModel->getAttributeLabel('article')],
        ],
        [
            'attribute' => 'code',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $goodsModel->getAttributeLabel('code')],
        ],
        [
            'attribute' => 'price',
            'format' => 'raw',
            'filter' => false,
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $goodsModel->getAttributeLabel('price')],
        ],
        [
            'attribute' => 'typedPrice',
            'format' => 'raw',
            'filter' => false,
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $goodsModel->getAttributeLabel('typedPrice')],
        ],
        [
            'header' => 'Знижка',
            'format' => 'raw',
            'filter' => false,
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Знижка'],
            'value' => function ($model) {
                return $model->getDiscountTxt();
            }
        ],
        [
            'class' => AdminActionColumn::className(),
            'template' => '{update}',
            'contentOptions' => ['class' => 'actions pr-0'],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-edit"></i>', Url::to(['/admin/goods/update', 'id' => $model->id]), ['class' => '', 'title' => 'редагування', 'data-pjax' => 0, 'target' => '_blank']);
                },
            ],
        ],
    ],
]);
?>
<a class="btn btn-sm btn-info mb-5" href="<?php echo Url::toRoute('index') ?>"><i class="fa fa-arrow-left"></i> повернутись</a>

