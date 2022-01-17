<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActionColumn;
?>
<?php
$this->params['breadcrumbs'] = [
    $this->title = 'Абоненти',
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
            'template' => '{transfer} {update} {view}',
            'contentOptions' => ['class' => 'actions text-right pr-0'],
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    $url = Url::toRoute(['view', 'id' => $model->house_id, 'aid' => $model->flat_id]);
                    return Html::a('<i class="fa fa-users"></i>', $url, ['class' => '', 'title' => 'детальна інформація', 'data-pjax' => 0]);
                },
                'update' => function ($url, $model, $key) {
                    $url = Url::toRoute(['update', 'id' => $model->house_id, 'aid' => $model->flat_id]);
                    return Html::a('<i class="fa fa-edit"></i>', $url, ['class' => '', 'title' => 'редагувати', 'data-pjax' => 0]);
                },
                'transfer' => function ($url, $model, $key) {
                    $url = Url::toRoute(['transfer', 'id' => $model->house_id]);
                    return Html::a('<i class="fa fa-share"></i>', $url, ['class' => '', 'title' => 'трансфер', 'data-pjax' => 0]);
                },
            ],
            'visibleButtons' => [
                'update' => function ($model) {
                    return false; #$model->flat_id === null;
                },
                'transfer' => function ($model) {
                    return $model->isAllowTransfer();
                },
            ],
        ],
    ],
]);
?>
<hr/>
Користувачі абонентів:
<div class="row mt-3">
    <div class="col-sm-12 col-md-4">
        <a class="btn btn-sm btn-info" href="<?php echo yii\helpers\Url::toRoute('import'); ?>"><i class="fa fa-download"></i> імпорт CSV</a>
    </div>
</div>
