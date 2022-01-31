<?php

use app\models\Cpu;
$goodCpu = Cpu::find()->where(['id' => $model->id])->one()->toArray();

if ($goodCpu['visible']) {
    ?>
    <a href="<?php echo $model->getUrl(); ?>" class="goods_item" data-id="<?php echo $model->id; ?>">
        <div>

            <div class="advert">
                <?php if ($model->isHasPromo()): ?>
                    <div class="action">Акція</div>
                <?php endif; ?>
                <?php if ($model->isHasNew()): ?>
                    <div class="latest">Новинка</div>
                <?php endif; ?>
                <?php if ($model->hasOldPrice()): ?>
                    <div class="discount">-<?php echo $model->getRuntimeDiscount(); ?>%</div>
                <?php endif; ?>
            </div>
            <?php
            $photoModel = $model->getMainPhoto();
            if ($photoModel === null) {
                $src = '/files/images/system/empty.png';
            } else {
                $src = $photoModel->getSrc('medium');
            }
            ?>
            <div class="photo-div-fixed">
                <img src="<?php echo $src; ?>" alt="">
            </div>
            <div class="goods_info">
                <div>
                    <p><?php echo $model->getBrand()->title; ?></p>
                    <p><?php echo $model->getTitle(); ?></p>
                    <p><?php echo $model->article; ?></p>
                </div>
            </div>
        </div>
        <div class="goods_price">
            <?php if ($model->hasOldPrice()): ?>
                <div class="col_bdr">
                    <span id="js_price_info_<?php echo $model->id; ?>"><?php echo $model->getPriceRange(); ?></span> ₴
                </div>
                <div class="col_grey"><?php echo $model->getOldPrice(); ?> ₴</div>
            <?php else: ?>
                <div class="col_black"><span
                            id="js_price_info_<?php echo $model->id; ?>"><?php echo $model->getPriceRange(); ?></span> ₴
                </div>
            <?php endif; ?>
        </div>
    </a>

<? } ?>