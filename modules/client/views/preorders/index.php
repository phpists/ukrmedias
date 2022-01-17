<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\Misc;
use app\components\AppPjax;
use app\components\FrontendGrid;
use app\components\Icons;
use app\models\Orders;

$this->title = 'Мої заявки';
?>
<div class="dark_fond opac0" id="reorder">
    <div class="reorder">
        <h2>Повторити заявку</h2>
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.7071 1.70711C16.0976 1.31658 16.0976 0.683417 15.7071 0.292893C15.3166 -0.0976311 14.6834 -0.0976311 14.2929 0.292893L8 6.58579L1.70711 0.292893C1.31658 -0.0976311 0.683417 -0.0976311 0.292893 0.292893C-0.0976311 0.683417 -0.0976311 1.31658 0.292893 1.70711L6.58579 8L0.292893 14.2929C-0.0976311 14.6834 -0.0976311 15.3166 0.292893 15.7071C0.683417 16.0976 1.31658 16.0976 1.70711 15.7071L8 9.41421L14.2929 15.7071C14.6834 16.0976 15.3166 16.0976 15.7071 15.7071C16.0976 15.3166 16.0976 14.6834 15.7071 14.2929L9.41421 8L15.7071 1.70711Z" fill=""/>
        </svg>
        <div class="add">
            <h6>Доповніть кошик товарами з цієї заявки.</h6>
            <p>До існуючих товарів у кошику додадуться артикули з заявки.</p>
            <a class="js-add" href="<?php echo Url::to(['repeate', 'id' => $model->id]); ?>">Доповнити</a>
        </div>
        <div class="fill">
            <h6>Заповніть кошик товарами з цієї заявки.</h6>
            <p>Кошик буде очищено. До кошику додадуться артикули з заявки.</p>
            <a class="js-replace" href="<?php echo Url::to(['repeate', 'id' => $model->id, 'clean' => 1]); ?>">Заповнити</a>
        </div>
    </div>
</div>
<div class="table">
    <?php echo $this->context->renderPartial('/cart/_upload_button'); ?>
    <?php
    AppPjax::begin();

    echo FrontendGrid::widget([
        'id' => 'orders',
        'cols' => ['80px', '80px', '100px', '100px', '50px'],
        'filterModel' => $model,
        'dataProvider' => $dataProvider,
//        'rowOptions' => function ($model, $index, $widget, $grid) {
//            return ['class' => $model->isNew() ? '' : 'processed'];
//        },
        'columns' => [
            [
                'attribute' => 'id',
                'format' => 'raw',
                'filterInputOptions' => ['placeholder' => $model->getAttributeLabel('number')],
                'value' => function ($data) {
                    $url = Url::to(['view', 'id' => $data->id]);
                    return '<span class="mob-title">' . $data->getAttributeLabel('number') . ': </span><span class="mob-descr"><a data-pjax="0" class="cell-link" href="' . $url . '">' . $data->getNumber() . '</a></span>';
                },
            ],
            [
                'attribute' => 'date',
                'format' => 'raw',
                'filter' => false,
                'filterOptions' => ['class' => 'empty'],
                'value' => function ($data) {
                    $url = Url::to(['view', 'id' => $data->id]);
                    return '<span class="mob-title">' . $data->getAttributeLabel('date') . ': </span><span class="mob-descr"><a data-pjax="0" class="cell-link" href="' . $url . '">' . $data->getDate() . '</a></span>';
                },
            ],
            [
                'attribute' => 'status_id',
                'format' => 'raw',
                'filter' => $model::$statusLabels,
                'filterInputOptions' => ['class' => '', 'prompt' => $model->getAttributeLabel('всі статуси')],
                'value' => function ($data) {
                    $url = Url::to(['view', 'id' => $data->id]);
                    return '<span class="mob-title">' . $data->getAttributeLabel('status_id') . ': </span><span class="mob-descr"><a data-pjax="0" class="cell-link" href="' . $url . '">' . $data->getStatusIcon() . $data->getStatus() . '</a></span>';
                },
            ],
            [
                'attribute' => 'amount',
                'format' => 'raw',
                'filterOptions' => ['class' => 'empty'],
                'value' => function ($data) {
                    $url = Url::to(['view', 'id' => $data->id]);
                    return '<span class="mob-title">' . $data->getAttributeLabel('amount') . ': </span><span class="mob-descr"><a data-pjax="0" class="cell-link" href="' . $url . '">' . $data->amount . '</a></span>';
                },
            ],
            [
                'class' => '\yii\grid\ActionColumn',
                'filterOptions' => ['class' => 'empty'],
                'template' => '{delete} {repeate}',
                'contentOptions' => ['class' => 'actions'],
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        return Html::a('видалити', $url, ['class' => 'btn', 'title' => 'Видалити', 'data-pjax' => 0, 'data-confirm-click' => Html::encode('Ви підтверджуєте видалення заявки № ' . $model->getNumber() . ' від ' . $model->getDate() . '?')]);
                    },
                    'repeate' => function ($url, $model, $key) {
                        return '<div class="btn repeat" data-url="' . Url::to(['repeate', 'id' => $model->id]) . '">Повторити замовлення</div>';
                    }
                ],
                'visibleButtons' => [
                    'delete' => function ($model) {
                        return $model->isAllowDelete();
                    },
                ],
            ],
        ],
    ]);
    AppPjax::end();
    ?>
</div>