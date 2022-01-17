<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActionColumn;
?>
<?php
$this->params['breadcrumbs'] = [
    $this->title = 'Категорії',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'rowOptions' => function ($model, $index, $widget, $grid) {
        return ['class' => $model->isVisible() ? '' : 'text-muted'];
    },
    'columns' => [
        [
            'attribute' => 'title',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getAdminTitle();
            },
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('title')],
        ],
        [
            'attribute' => 'title_alt',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('title_alt')],
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
                        'data-confirm-message' => yii\helpers\Html::encode('Ви підтверджуєте видалення ' . $model->getTitle() . '?'),
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
<a class="btn btn-sm btn-primary mb-5" href="<?php echo yii\helpers\Url::toRoute(['/admin/category/update']); ?>"><i class="fa fa-plus"></i> додати</a>

