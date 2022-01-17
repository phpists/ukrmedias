<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActionColumn;
?>
<?php
$this->params['breadcrumbs'] = [
    $this->title = 'Сдайдери',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'sortUrl' => ['update-pos'],
    'columns' => [
        [
            'attribute' => 'place_id',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('place_id')],
            'filter' => $model::$placeLabels,
            'value' => function ($model) {
                return $model->getPlace();
            }
        ],
        [
            'header' => 'Зображення',
            'format' => 'raw',
            'filter' => false,
            'value' => function ($model) {
                return "<img src='{$model->getModelFiles('photo')->getSrc('small')}' style='max-height:100px;'/>";
            }
        ],
        [
            'attribute' => 'visible',
            'format' => 'raw',
            'filter' => $model::$valuesTrueFalse,
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('visible')],
            'value' => function ($model) {
                return $model->getVisible();
            }
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
                        'data-confirm-message' => yii\helpers\Html::encode('Ви підтверджуєте видалення цього запису?'),
                    ]);
                },
            ],
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
    <a class="btn btn-sm btn-primary mb-5" href="<?php echo yii\helpers\Url::toRoute(['update']); ?>"><i class="fa fa-plus"></i> додати</a>
    <a class="btn btn-sm btn-info mb-5" href="<?php echo Url::toRoute($route) ?>"><i class="fa fa-arrows-v"></i> сортування записів</a>
<?php endif; ?>


