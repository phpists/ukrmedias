<div class="<?php echo $filter->getCss(); ?>">
    <?php

    $modelGoods = $filter->getQueryModelItems(Yii::$app->user->identity->getFirm()->price_type_id)->all();
    Yii::$app->cache->set("_download_model_goods", $modelGoods, 0);

    echo \yii\widgets\ListView::widget([
        'dataProvider' => $filter->getDataProvider(Yii::$app->user->identity->getFirm()->price_type_id),
        'layout' => '<div class="cont">{items}</div>{pager}',
        'options' => ['class' => 'goods'],
        'itemOptions' => [
            'tag' => false,
        ],
        'itemView' => '_goods_item',
        'pager' => [
            'options' => ['class' => 'page_choise'],
            'linkContainerOptions' => ['class' => 'pagination_link'],
            'activePageCssClass' => 'col_bdr',
            'linkOptions' => ['class' => ''],
            'disabledListItemSubTagOptions' => ['class' => ''],
            'disableCurrentPageButton' => true,
            'disabledPageCssClass' => 'disb',
            'nextPageCssClass' => 'arrow',
            'prevPageCssClass' => 'arrow',
            'prevPageLabel' => '<img src="img/orders/arrow-left.svg" alt="">',
            'nextPageLabel' => '<img src="img/orders/arrow-right.svg" alt="">',
        ],
    ]);
    ?>
</div>
