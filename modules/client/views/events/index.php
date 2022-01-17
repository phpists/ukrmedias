<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\AppPjax;
use app\models\Events;

$this->title = 'Журнал подій';
AppPjax::begin();
?>
<div class="main-template-receipts main-template-address">
    <div class="h3 main-template__header"><?php echo $this->title; ?></div>
    <?php
    echo \app\components\FrontendGrid::widget([
        'cols' => ['125px', '', '', '120px'],
        'filterModel' => $model,
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'date',
                'format' => 'raw',
                'filterOptions' => ['class' => 'empty'],
                'value' => function ($data) {
                    return '<span class="mob-title">' . $data->getAttributeLabel('date') . ': </span><span class="mob-descr">' . $data->getDate() . '</span>';
                },
            ],
            [
                'label' => 'Адреса',
                'attribute' => 'search_city',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => $model->getAttributeLabel('search_city')],
                'value' => function ($data) {
                    return '<span class="mob-title">' . $data->getAttributeLabel('search_address') . ': </span><span class="mob-descr">' . $data->getAddress() . '</span>';
                },
            ],
            [
                'attribute' => 'info',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => $model->getAttributeLabel('info')],
                'value' => function ($data) {
                    return '<span class="mob-title">' . $data->getAttributeLabel('info') . ': </span><span class="mob-descr">' . $data->info . '</span>';
                },
            ],
            [
                'attribute' => 'status_id',
                'format' => 'raw',
                'filter' => false,
                'filterOptions' => ['class' => 'empty'],
                'value' => function ($data) {
                    return '<span class="mob-title">' . $data->getAttributeLabel('status_id') . ': </span><span class="mob-descr">'
                            . '<form class="js_ajax_form" method="post" action="' . Url::toRoute('toggle-status') . '" data-reloadurl="' . getenv('REQUEST_URI') . '">'
                            . Html::hiddenInput('id', $data->id)
                            . Html::dropDownList(null, $data->status_id, Events::$statusLabels, ['class' => 'js-dropdown-select form-default__select'])
                            . '</form></span>';
                },
            ],
        ],
    ]);
    ?>
</div>
<?php
AppPjax::end();
