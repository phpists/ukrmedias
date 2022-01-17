<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\Misc;
use app\components\Icons;
use app\components\AppPjax;
use app\models\Services;
use app\models\DataHelper;

$this->title = 'Рахунки за споживання';
?>
<div class="main-template-receipts main-template-address">
    <div class="h3 main-template__header"><?php echo $this->title; ?></div>
    <div class="columns is-multiline">
        <div class="column is-4-desktop is-12-mobile">
            <form class="js_reload_form" action="<?php echo Url::to(['index', 'id' => $service_id]); ?>">
                <div class="columns is-flex-center">
                    <div class="column is-7">
                        <?php echo Html::dropDownList('month', $month, Misc::$months, ['class' => 'js-dropdown-select form-default__select']); ?>
                    </div>
                    <div class="column is-5">
                        <?php echo Html::dropDownList('year', $year, Misc::years(), ['class' => 'js-dropdown-select form-default__select']); ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="w100 tabs-type__wrapper">
        <?php
        foreach (Services::$labels as $sid => $label):
            if (in_array($sid, $services)):
                ?>
                <a href="<?php echo Url::toRoute(['index', 'id' => $sid, 'year' => $year, 'month' => $month]); ?>" class="tabs__head-item <?php if ($sid == $service_id): ?>is-active service<?php echo $sid; ?><?php endif; ?>" data-pjax="0">
                    <icon class="tabs__head-item--icon  "><?php echo Services::getIcon($sid); ?>
                    </icon>
                    <span class="tabs__head-item--text"><?php echo $label; ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php
    AppPjax::begin();
    echo \app\components\FrontendGrid::widget([
        'id' => 'main_page_grid',
        'cols' => ['130px', '110px', '', '60px'],
        'filterModel' => $model,
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'number',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => $model->getAttributeLabel('number')],
                'value' => function ($data) {
                    $url = Url::toRoute(['details', 'id' => $data->house_id, 'aid' => $data->flat, 'sid' => $data->service_id, 'from' => $data->from, 'to' => $data->to]);
                    return Html::a('<span class="mob-title">' . $data->getAttributeLabel('number') . ': </span><span class="mob-descr">' . $data->getNumber() . '</span>', $url, ['title' => 'Деталі', 'data-pjax' => 0, 'class' => 'cell-link', 'target' => '_blank']);
                },
            ],
            [
                'attribute' => 'amount',
                'format' => 'raw',
                'filterOptions' => ['class' => 'empty'],
                'value' => function ($data) {
                    $url = Url::toRoute(['details', 'id' => $data->house_id, 'aid' => $data->flat, 'sid' => $data->service_id, 'from' => $data->from, 'to' => $data->to]);
                    return Html::a('<span class="mob-title">' . $data->getAttributeLabel('amount') . ': </span><span class="mob-descr">' . $data->getTotalAmount() . '</span>', $url, ['title' => 'Деталі', 'data-pjax' => 0, 'class' => 'cell-link', 'target' => '_blank']);
                },
            ],
            [
                'attribute' => 'search_address',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-default__input', 'placeholder' => $model->getAttributeLabel('search_address')],
                'value' => function ($data) {
                    $url = Url::toRoute(['details', 'id' => $data->house_id, 'aid' => $data->flat, 'sid' => $data->service_id, 'from' => $data->from, 'to' => $data->to]);
                    return Html::a('<span class="mob-title">' . $data->getAttributeLabel('search_address') . ': </span><span class="mob-descr">' . $data->search_address . '</span>', $url, ['title' => 'Деталі', 'data-pjax' => 0, 'class' => 'cell-link', 'target' => '_blank']);
                },
            ],
            [
                'class' => '\yii\grid\ActionColumn',
                'filterOptions' => ['class' => 'empty'],
                'template' => '{print} {preview}',
                'contentOptions' => ['class' => 'actions'],
                'buttons' => [
                    'print' => function($url, $model, $key) {
                        $url = Url::toRoute(['download', 'id' => $model->house_id, 'aid' => $model->flat, 'sid' => $model->service_id, 'from' => $model->from, 'to' => $model->to]);
                        $linkOptions = ['title' => 'Скачати', 'class' => 'hover-orange', 'data-pjax' => 0, 'style' => 'min-width:50px;'];
                        if ($model->flat == 0) {
                            $linkOptions['target'] = '_house_invoices';
                            return Html::a('ZIP', $url, $linkOptions);
                        } else {
                            return Html::a('PDF', $url, $linkOptions);
                        }
                    },
                    'preview' => function($url, $model, $key) {
                        $data = Yii::$app->security->hashData(json_encode([
                            'from' => $model->from,
                            'to' => $model->to,
                            'house_id' => $model->house_id,
                            'flat' => $model->flat,
                            'service_id' => $model->service_id,
                            'client_id' => Yii::$app->user->identity->client_id,
                                ]), DataHelper::HASH_KEY);
                        $url = Url::toRoute(['screen', 'data' => base64_encode($data)]);
                        return Html::a(Icons::$empty, $url, ['title' => 'to screen', 'data-pjax' => 0, 'target' => '_preview']);
                    }
                ],
                'visibleButtons' => [
                    'preview' => false,
                ],
            ],
        ],
    ]);
    AppPjax::end();
    ?>
</div>
<?php
