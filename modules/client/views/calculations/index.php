<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\Icons;
use app\components\AppPjax;

$this->title = 'Розрахунки';
AppPjax::begin();
?>
<div class="main-template-employ main-template-address">
    <div class="h3 main-template__header m24"><?php echo $this->title; ?></div>
    <?php
    echo \app\components\FrontendGrid::widget([
        'cols' => ['17%', '17%', '24%', '100px', ''],
        'filterModel' => $model,
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => 'Область/район',
                'attribute' => 'region_id',
                'format' => 'raw',
                'contentOptions' => ['class' => 'nowrap'],
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => 'Область/район'],
                'value' => function ($data) use($service_id) {
                    $url = Url::toRoute(['period', 'id' => $data->id, 'aid' => $service_id]);
                    return Html::a('<span class="mob-title">Область/район: </span><span class="mob-descr">' . $data->getRegionTxt() . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
            [
                'attribute' => 'search_city',
                'format' => 'raw',
                'contentOptions' => ['class' => 'nowrap'],
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => $model->getAttributeLabel('search_city')],
                'value' => function ($data) use($service_id) {
                    $url = Url::toRoute(['period', 'id' => $data->id, 'aid' => $service_id]);
                    return Html::a('<span class="mob-title">' . $data->getAttributeLabel('search_city') . ': </span><span class="mob-descr">' . $data->getSearchCity() . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
            [
                'attribute' => 'search_street',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => $model->getAttributeLabel('search_street')],
                'value' => function ($data) use($service_id) {
                    $url = Url::toRoute(['period', 'id' => $data->id, 'aid' => $service_id]);
                    return Html::a('<span class="mob-title">' . $data->getAttributeLabel('search_street') . ': </span><span class="mob-descr">' . $data->search_street . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
            [
                'label' => 'Будинок',
                'attribute' => 'no',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => 'Будинок'],
                'value' => function ($data) use($service_id) {
                    $url = Url::toRoute(['period', 'id' => $data->id, 'aid' => $service_id]);
                    return Html::a('<span class="mob-title">Будинок: </span><span class="mob-descr">' . $data->no . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
            [
                'attribute' => 'clientNote',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => $model->getAttributeLabel('clientNote')],
                'value' => function ($data) use($service_id) {
                    $url = Url::toRoute(['period', 'id' => $data->id, 'aid' => $service_id]);
                    return Html::a('<span class="mob-title">' . $data->getAttributeLabel('clientNote') . ': </span><span class="mob-descr">' . $data->getClientNote() . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
        ],
    ]);
    ?>
</div>
<?php
AppPjax::end();
