<?php

use app\components\AdminActionColumn;
use yii\helpers\Html;
use yii\helpers\Url;

echo app\components\AdminGrid::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'first_name',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getName();
            },
        ],
        [
            'header' => 'Контакти',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getContacts();
            },
        ],
        [
            'attribute' => 'active',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getStatus();
            },
        ],
        [
            'class' => AdminActionColumn::className(),
            'template' => '{update} {remove} {delete} {loginas}',
            'buttons' => [
                'update' => function ($url, $model, $key)use($flat) {
                    return Html::a('<i class="fa fa-edit"></i>', Url::toRoute(['/admin/abonents/update', 'id' => $flat->house_id, 'aid' => $flat->id, 'uid' => $model->id]), ['class' => '', 'title' => 'редагування профілю', 'data-pjax' => 0]);
                },
                'remove' => function ($url, $model, $key)use($flat) {
                    return Html::a('<i class="fa fa-remove"></i>', Url::toRoute(['/admin/abonents/remove', 'id' => $flat->house_id, 'aid' => $flat->id, 'uid' => $model->id]), [
                                'title' => 'видалити з об`єкту',
                                'data-confirm-message' => Html::encode("Ви підтверджуєте видалення доступу користувача {$model->getName()} до об`єкту '{$flat->getTitle()}'?"),
                    ]);
                },
                'delete' => function ($url, $model, $key)use($flat) {
                    return Html::a('<i class="fa fa-trash"></i>', Url::toRoute(['/admin/abonents/delete', 'id' => $flat->house_id, 'aid' => $flat->id, 'uid' => $model->id]), [
                                'title' => 'видалити',
                                'data-confirm-message' => Html::encode("Ви підтверджуєте видалення користувача {$model->getName()}?"),
                    ]);
                },
                'loginas' => function ($url, $model, $key)use($flat) {
                    return Html::a('<i class="fa fa-sign-in"></i>', Url::toRoute(['/admin/abonents/loginas', 'id' => $flat->house_id, 'aid' => $flat->id, 'uid' => $model->id]), ['class' => '', 'title' => 'увійти як абонент', 'data-pjax' => 0]);
                },
            ],
//            'visibleButtons' => [
//                'loginas' => function ($model) {
//                    return false;
//                },
//            ],
            'contentOptions' => ['class' => 'actions text-right pr-0'],
        ],
    ],
]);
