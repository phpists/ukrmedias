<?php

use yii\helpers\Html;
use app\components\AdminActionColumn;
use app\components\Auth;
use app\models\Users;
?>
<?php
$this->params['breadcrumbs'] = [
    $this->title = 'Співробітники клієнтів',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'firm_id',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getFirmTitle();
            },
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('firm_id')],
        ],
        [
            'label' => $model->getAttributeLabel('first_name'),
            'attribute' => 'second_name',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getName();
            },
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('first_name')],
        ],
        [
            'attribute' => 'role_id',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getRole();
            },
            'filter' => Auth::$roleLabelsClient,
            'filterInputOptions' => ['class' => 'form-control', 'prompt' => 'всі ролі'],
        ],
        [
            'attribute' => 'active',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getStatus();
            },
            'filter' => Users::$statusLabels,
            'filterInputOptions' => ['class' => 'form-control', 'prompt' => 'всі статуси'],
        ],
        [
            'class' => AdminActionColumn::className(),
            'template' => '{update} {delete} {loginas}',
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-edit"></i>', $url, ['class' => '', 'title' => 'редагування профілю', 'data-pjax' => 0]);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-trash"></i>', $url, [
                        'title' => 'видалити',
                        'data-confirm-message' => yii\helpers\Html::encode('Ви підтверджуєте видалення користувача ' . $model->getName() . '?'),
                    ]);
                },
                'loginas' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-sign-in"></i>', $url, ['class' => '', 'title' => 'увійти як ' . $model->getName(), 'data-pjax' => 0]);
                },
            ],
            'visibleButtons' => [
                'update' => function ($model) {
                    return $model->isAllowUpdate();
                },
                'delete' => function ($model) {
                    return $model->isAllowDelete();
                },
                'loginas' => function ($model) {
                    return $model->isAllowControl();
                },
            ],
            'contentOptions' => ['class' => 'actions text-right pr-0'],
        ],
    ],
]);
?>
<a class="btn btn-sm btn-primary mb-5" href="<?php echo yii\helpers\Url::toRoute('update'); ?>"><i class="fa fa-plus"></i> додати</a>
