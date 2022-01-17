<?php

use yii\helpers\Html;
use app\components\AdminActionColumn;
use app\components\Auth;
use app\models\Devices;
use app\models\Services;
?>
<?php
$this->params['breadcrumbs'] = [
    $this->title = 'Пристрої',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'label'=>'DevEUI / Сер.номер',
            'attribute' => 'deveui',
            'format' => 'raw',
            'value' => function($data) {
                return $data->getDevEuiAdmin();
            },
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('deveui')],
        ],
        [
            'attribute' => 'date',
            'format' => 'raw',
            'value' => function($data) {
                return $data->getDate();
            },
            'filter' => false,
        ],
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
            'attribute' => 'search_flat',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('search_flat')],
        ],
        [
            'class' => AdminActionColumn::className(),
            'template' => '{control} {update} {delete}',
            'contentOptions' => ['class' => 'actions text-right pr-0'],
            'buttons' => [
                'control' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-sliders"></i>', $url, ['class' => '', 'title' => 'керування', 'data-pjax' => 0]);
                },
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-edit"></i>', $url, ['class' => '', 'title' => 'редагування профілю', 'data-pjax' => 0]);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-trash"></i>', $url, [
                                'title' => 'видалити',
                                'data-confirm-message' => yii\helpers\Html::encode('Ви підтверджуєте видалення пристрою ' . $model->deveui . ' (' . $model->getType() . ') ?'),
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
    <div class="form-group mb-5">
        <a class="btn btn-sm btn-info" href="<?php echo yii\helpers\Url::toRoute('import'); ?>"><i class="fa fa-download"></i> імпорт CSV</a>
    </div>
</div>
