<?php

use yii\helpers\Html;
use app\components\AdminActionColumn;
use app\components\Auth;
?>
<?php
$this->params['breadcrumbs'] = [
    $this->title = 'Шлюзи',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'mac',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('mac')],
        ],
        [
            'attribute' => 'desc',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('desc')],
        ],
        [
            'attribute' => 'status_id',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getStatus();
            },
            'filter' => false,
        ],
        [
            'class' => AdminActionColumn::className(),
            'template' => '{log} {update} {delete}',
            'contentOptions' => ['class' => 'actions text-right pr-0'],
            'buttons' => [
                'log' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-list-alt"></i>', $url, ['class' => '', 'title' => 'журнал', 'data-pjax' => 0]);
                },
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-edit"></i>', $url, ['class' => '', 'title' => 'редагування профілю', 'data-pjax' => 0]);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-trash"></i>', $url, [
                                'title' => 'видалити',
                                'data-confirm-message' => yii\helpers\Html::encode('Ви підтверджуєте видалення шлюзу ' . $model->mac . ' (' . $model->desc . ') ?'),
                    ]);
                },
            ],
        ],
    ],
]);
?>
<?php if (Yii::$app->user->can(Auth::ROLE_SUPERADMIN)): ?>
    <a class="btn btn-sm btn-primary mb-5" href="<?php echo yii\helpers\Url::toRoute('update'); ?>"><i class="fa fa-plus"></i> додати</a>
<?php endif; ?>
