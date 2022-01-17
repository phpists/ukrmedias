<?php

use yii\helpers\Html;
use app\components\AdminActionColumn;
use app\components\Auth;
?>
<?php
$this->params['breadcrumbs'] = [
    $this->title = 'Довідник "Вулиці"',
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
            'attribute' => 'title',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('title')],
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
                                'data-confirm-message' => 'Разом з вулицею будуть видалені пов`язані з нею будинки, квартири, дані про споживання, рахунки, події та параметри для розрахунків. Після видалення будуть втрачені прив`язки Lora-пристроїв та приладів обліку.<br/>Ви підтверджуєте видалення вулиці "<i>' . Html::encode($model->getTitle()) . '</i>"?',
                    ]);
                },
            ],
        ],
    ],
]);
?>
<a class="btn btn-sm btn-primary mb-5" href="<?php echo yii\helpers\Url::toRoute('update'); ?>"><i class="fa fa-plus"></i> додати</a>
