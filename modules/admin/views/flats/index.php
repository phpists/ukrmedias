<?php

use yii\helpers\Html;
use app\components\AdminActionColumn;
use app\components\Auth;
use app\models\Devices;
use app\models\Services;
?>
<?php
$this->params['breadcrumbs'] = [
    $this->title = 'Приміщення',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'rowOptions' => function ($model, $index, $widget, $grid) {
        return $model->deleted ? ['class' => 'text-muted deleted'] : [];
    },
    'columns' => [
        [
            'attribute' => 'search_city',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('search_city')],
        ],
        [
            'attribute' => 'search_street',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('search_street')],
        ],
        [
            'attribute' => 'search_house',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('search_house')],
        ],
        [
            'attribute' => 'no',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('no')],
        ],
        [
            'class' => AdminActionColumn::className(),
            'template' => '{update} {delete}',
            'contentOptions' => ['class' => 'actions text-right pr-0'],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-edit"></i>', $url, ['class' => '', 'title' => 'редагування профілю', 'data-pjax' => 0]);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-trash"></i>', $url, [
                                'title' => 'видалити',
                                'data-confirm-message' => 'Разом з приміщенням будуть видалені пов`язані з ним дані про споживання, рахунки, події та параметри для розрахунків. Після видалення будуть втрачені прив`язки Lora-пристроїв та приладів обліку.<br/>Ви підтверджуєте видалення приміщення з адресою "<i>' . Html::encode($model->getTitle()) . '</i>"?',
                    ]);
                },
            ],
        ],
    ],
]);
?>
<div class="fixed-column">
    <div class="form-group mb-5">
        <a class="btn btn-sm btn-primary" href="<?php echo yii\helpers\Url::toRoute('update'); ?>"><i class="fa fa-plus"></i> додати</a>
    </div>
</div>
