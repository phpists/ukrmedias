<?php

use yii\helpers\Html;
use app\components\AdminActionColumn;
use app\components\Auth;
?>
<?php
$this->params['breadcrumbs'] = [
    $this->title = 'клієнти',
];
echo app\components\AdminGrid::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $model,
    'columns' => [
        [
            'attribute' => 'title',
            'format' => 'raw',
        ],
        [
            'attribute' => 'phones',
            'format' => 'raw',
            'value' => function ($model) {
                return implode('; ', $model->getPhones());
            }
        ],
        [
            'class' => AdminActionColumn::className(),
            'template' => '{users} {update} {delete} {loginas}',
            'contentOptions' => ['class' => 'actions text-right pr-0'],
            'buttons' => [
                'users' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-users"></i>', yii\helpers\Url::toRoute(['/admin/users/index', 'Users[firm_id]' => $model->getTitle()]), ['class' => '', 'title' => 'користувачі', 'data-pjax' => 0]);
                },
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-edit"></i>', $url, ['class' => '', 'title' => 'редагування профілю', 'data-pjax' => 0]);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-trash"></i>', $url, [
                        'title' => 'видалити',
                        'data-confirm-message' => yii\helpers\Html::encode('Ви підтверджуєте видалення клієнта ' . $model->title . '?'),
                    ]);
                },
                'loginas' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-sign-in"></i>', $url, ['class' => '', 'title' => 'увійти як клієнт', 'data-pjax' => 0]);
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
                    return true;
                },
            ],
        ],
    ],
]);
?>
<a class="btn btn-sm btn-primary mb-5" href="<?php echo yii\helpers\Url::toRoute('update'); ?>"><i class="fa fa-plus"></i> додати</a>
