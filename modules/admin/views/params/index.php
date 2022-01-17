<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActionColumn;
?>
<?php
$this->params['breadcrumbs'] = [
    $this->title = 'Параметри',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'sortUrl' => ['update-pos'],
    'columns' => [
        [
            'attribute' => 'title',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('title')],
        ],
        [
            'attribute' => 'unit',
            'format' => 'raw',
            'filter' => false,
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('unit')],
        ],
        [
            'attribute' => 'type_id',
            'format' => 'raw',
            'filter' => $model::$types,
            'value' => function ($model) {
                return $model->getType();
            },
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('type_id')],
        ],
    ],
]);
?>
<?php if (Yii::$app->request->get('sortmode')): ?>
    <a class="btn btn-sm btn-info mb-5" href="<?php echo Url::toRoute($route) ?>"><i class="fa fa-arrow-left"></i> повернутись</a>
    <?php
else:
    $route['sortmode'] = 1;
    ?>
    <a class="btn btn-sm btn-info mb-5" href="<?php echo Url::toRoute($route) ?>"><i class="fa fa-arrows-v"></i> сортування записів</a>
<?php endif; ?>

