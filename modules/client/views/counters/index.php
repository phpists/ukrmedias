<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Services;
use app\components\AppPjax;
use app\components\Icons;

$this->title = 'Прилади обліку';
AppPjax::begin();
?>
<div class="main-template-receipts main-template-meters">
    <div class="flex-title-block is-column-tablet">
        <div class="h3 main-template__header"><?php echo $this->title; ?></div>
        <div class="top-buttons-group">
            <a href="<?php echo Url::toRoute('/client/counters/update'); ?>" class="btn-add-white" data-pjax="0">
                <div class="icon">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.33333 0C6.96514 0 6.66667 0.298477 6.66667 0.666667V6.66667H0.666667C0.298477 6.66667 0 6.96514 0 7.33333V8.66667C0 9.03486 0.298477 9.33333 0.666667 9.33333H6.66667V15.3333C6.66667 15.7015 6.96514 16 7.33333 16H8.66667C9.03486 16 9.33333 15.7015 9.33333 15.3333V9.33333H15.3333C15.7015 9.33333 16 9.03486 16 8.66667V7.33333C16 6.96514 15.7015 6.66667 15.3333 6.66667H9.33333V0.666667C9.33333 0.298477 9.03486 0 8.66667 0H7.33333Z" fill="#918DA6"></path></svg>
                </div>
                <span>Додати лічильник</span>
            </a>
            <a href="<?php echo Url::toRoute('/client/counters/import'); ?>" class="btn-add-white" data-pjax="0">
                <div class="icon">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.33333 0C6.96514 0 6.66667 0.298477 6.66667 0.666667V6.66667H0.666667C0.298477 6.66667 0 6.96514 0 7.33333V8.66667C0 9.03486 0.298477 9.33333 0.666667 9.33333H6.66667V15.3333C6.66667 15.7015 6.96514 16 7.33333 16H8.66667C9.03486 16 9.33333 15.7015 9.33333 15.3333V9.33333H15.3333C15.7015 9.33333 16 9.03486 16 8.66667V7.33333C16 6.96514 15.7015 6.66667 15.3333 6.66667H9.33333V0.666667C9.33333 0.298477 9.03486 0 8.66667 0H7.33333Z" fill="#918DA6" /></svg>
                </div>
                <span>Імпорт CSV</span>
            </a>
        </div>
    </div>
    <div class="w100 tabs-type__wrapper">
        <?php
        foreach (Services::$labels as $sid => $label):
            if (in_array($sid, $services)):
                ?>
                <a href="<?php echo Url::toRoute(['index', 'id' => $sid]); ?>" class="tabs__head-item <?php if ($sid == $service_id): ?>is-active service<?php echo $sid; ?><?php endif; ?>" data-pjax="0">
                    <icon class="tabs__head-item--icon">
                        <?php echo Services::getIcon($sid); ?>
                    </icon>
                    <span class="tabs__head-item--text"><?php echo $label; ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php
    echo \app\components\FrontendGrid::widget([
        'cols' => ['120px', '180px', '', '', '80px'],
        'filterModel' => $model,
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => 'Номер',
                'attribute' => 'counter_number',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => $model->getAttributeLabel('counter_number')],
                'value' => function ($data) use($service_id) {
                    $url = $data->house_id > 0 ? $data->getAbonentUrl($service_id) : 'javascript:void(0);';
                    return Html::a('<span class="mob-title">' . $data->getAttributeLabel('counter_number') . ': </span><span class="mob-descr">' . ($data->counter_number ? $data->counter_number : '-') . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
            [
                'attribute' => 'counter_model',
                'format' => 'raw',
                'filterOptions' => ['class' => 'empty'],
                'value' => function ($data) use($service_id) {
                    $url = $data->house_id > 0 ? $data->getAbonentUrl($service_id) : 'javascript:void(0);';
                    return Html::a('<span class="mob-title">' . $data->getAttributeLabel('counter_model') . ': </span><span class="mob-descr">' . ($data->counter_model ? $data->counter_model : '-') . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
            [
                'label' => 'Модуль',
                'attribute' => 'device_snumber',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => 'Номер модулю'],
                'value' => function ($data) use($service_id) {
                    $url = $data->house_id > 0 ? $data->getAbonentUrl($service_id) : 'javascript:void(0);';
                    $text = $data->device_type !== '' ? "{$data->device_type} ({$data->device_snumber})" : '';
                    return Html::a('<span class="mob-title">Модуль: </span><span class="mob-descr">' . $text . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
            [
                'attribute' => 'address',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => 'Адреса'],
                'value' => function ($data) use($service_id) {
                    $url = $data->house_id > 0 ? $data->getAbonentUrl($service_id) : 'javascript:void(0);';
                    return Html::a('<span class="mob-title">' . $data->getAttributeLabel('address') . ': </span><span class="mob-descr">' . $data->address . '</span>', $url, ['data-pjax' => 0, 'class' => 'cell-link']);
                },
            ],
            [
                'class' => '\yii\grid\ActionColumn',
                'filterOptions' => ['class' => 'empty'],
                'template' => '{update} {delete}',
                'contentOptions' => ['class' => 'actions'],
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        $url = $model->isAllowUpdate() ? ['update', 'id' => $model->counter_id] : 'javascript:void(0);';
                        return Html::a(Icons::$pencil, $url, ['title' => 'Редагувати', 'data-pjax' => 0]);
                    },
                    'delete' => function($url, $model, $key) {
                        $url = $model->isAllowDelete() ? ['delete', 'id' => $model->counter_id] : 'javascript:void(0);';
                        return Html::a(Icons::$trash, $url, ['title' => 'Видалити', 'data-pjax' => 0, 'data-confirm-message' => Html::encode('Ви підтверджуєте видалення лічильника ' . $model->counter_number . '?')]);
                    }
                ],
                'visibleButtons' => [
                    'update' => function ($model) {
                        return $model->isAllowUpdate();
                    },
                    'delete' => function ($model) {
                        return $model->isAllowDelete();
                    },
                ],
            ],
        ],
    ]);
    ?>
</div>
<?php
AppPjax::end();
