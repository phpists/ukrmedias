<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\Icons;
use app\components\AppPjax;

$this->title = 'Адреси на обслуговуванні';
AppPjax::begin();
?>
<div class="main-template-employ main-template-address">
    <div class="flex-title-block">
        <div class="h3 main-template__header"><?php echo $this->title; ?></div>
        <div class="top-buttons-group">
            <a href="<?php echo Url::toRoute('update'); ?>" class="btn-add-white">
                <div class="icon">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.33333 0C6.96514 0 6.66667 0.298477 6.66667 0.666667V6.66667H0.666667C0.298477 6.66667 0 6.96514 0 7.33333V8.66667C0 9.03486 0.298477 9.33333 0.666667 9.33333H6.66667V15.3333C6.66667 15.7015 6.96514 16 7.33333 16H8.66667C9.03486 16 9.33333 15.7015 9.33333 15.3333V9.33333H15.3333C15.7015 9.33333 16 9.03486 16 8.66667V7.33333C16 6.96514 15.7015 6.66667 15.3333 6.66667H9.33333V0.666667C9.33333 0.298477 9.03486 0 8.66667 0H7.33333Z" fill="#918DA6" /></svg>
                </div>
                <span>Додати адресу</span>
            </a>
            <a href="<?php echo Url::toRoute('/client/abonents/import'); ?>" class="btn-add-white">
                <div class="icon">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.33333 0C6.96514 0 6.66667 0.298477 6.66667 0.666667V6.66667H0.666667C0.298477 6.66667 0 6.96514 0 7.33333V8.66667C0 9.03486 0.298477 9.33333 0.666667 9.33333H6.66667V15.3333C6.66667 15.7015 6.96514 16 7.33333 16H8.66667C9.03486 16 9.33333 15.7015 9.33333 15.3333V9.33333H15.3333C15.7015 9.33333 16 9.03486 16 8.66667V7.33333C16 6.96514 15.7015 6.66667 15.3333 6.66667H9.33333V0.666667C9.33333 0.298477 9.03486 0 8.66667 0H7.33333Z" fill="#918DA6" /></svg>
                </div>
                <span>Імпорт CSV</span>
            </a>
        </div>
    </div>
    <?php
    echo \app\components\FrontendGrid::widget([
        'cols' => ['17%', '17%', '24%', '100px', '', '80px'],
        'filterModel' => $model,
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => 'Область/район',
                'attribute' => 'region_id',
                'format' => 'raw',
                'contentOptions' => ['class' => 'nowrap'],
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => 'Область/район'],
                'value' => function ($data)use($services) {
                    $url = count($services) > 0 ? Url::toRoute(['details', 'id' => $data->id, 'aid' => current($services)]) : 'javascript:void(0);';
                    return Html::a('<span class="mob-title">Область/район: </span><span class="mob-descr">' . $data->getRegionTxt() . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
            [
                'attribute' => 'search_city',
                'format' => 'raw',
                'contentOptions' => ['class' => 'nowrap'],
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => $model->getAttributeLabel('search_city')],
                'value' => function ($data)use($services) {
                    $url = count($services) > 0 ? Url::toRoute(['details', 'id' => $data->id, 'aid' => current($services)]) : 'javascript:void(0);';
                    return Html::a('<span class="mob-title">' . $data->getAttributeLabel('search_city') . ': </span><span class="mob-descr">' . $data->getSearchCity() . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
            [
                'attribute' => 'search_street',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => $model->getAttributeLabel('search_street')],
                'value' => function ($data)use($services) {
                    $url = count($services) > 0 ? Url::toRoute(['details', 'id' => $data->id, 'aid' => current($services)]) : 'javascript:void(0);';
                    return Html::a('<span class="mob-title">' . $data->getAttributeLabel('search_street') . ': </span><span class="mob-descr">' . $data->search_street . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
            [
                'label' => 'Будинок',
                'attribute' => 'no',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => 'Будинок'],
                'value' => function ($data)use($services) {
                    $url = count($services) > 0 ? Url::toRoute(['details', 'id' => $data->id, 'aid' => current($services)]) : 'javascript:void(0);';
                    return Html::a('<span class="mob-title">Будинок: </span><span class="mob-descr">' . $data->no . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
            [
                'attribute' => 'clientNote',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => $model->getAttributeLabel('clientNote')],
                'value' => function ($data) use($services) {
                    $url = count($services) > 0 ? Url::toRoute(['details', 'id' => $data->id, 'aid' => current($services)]) : 'javascript:void(0);';
                    return Html::a('<span class="mob-title">' . $data->getAttributeLabel('clientNote') . ': </span><span class="mob-descr">' . $data->getClientNote() . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
            [
                'class' => '\yii\grid\ActionColumn',
                'filterOptions' => ['class' => 'empty'],
                'template' => '{update} {delete}',
                'contentOptions' => ['class' => 'actions'],
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a(Icons::$pencil, $url, ['title' => 'Редагувати', 'data-pjax' => 0]);
                    },
                    'delete' => function($url, $model, $key) {
                        return Html::a(Icons::$trash, $url, ['title' => 'Видалити', 'data-pjax' => 0, 'data-confirm-message' => Html::encode('Ви підтверджуєте видалення адреси ' . $model->getTitle() . '?')]);
                    }
                ],
            ],
        ],
    ]);
    ?>
</div>
<?php
AppPjax::end();
