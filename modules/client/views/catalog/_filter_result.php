<div class="<?php echo $filter->getCss(); ?>">
    <?php
    #$url = $filter->getDirectUrl();
    #$urlBtn = $url === false ? '' : '<div class="direct-url-control"><input type="text" value="' . $url . '" readonly/><span id="copy" class="direct-url-btn">Копіювати</span></div>';
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
            'linkContainerOptions' => ['class' => ''],
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
