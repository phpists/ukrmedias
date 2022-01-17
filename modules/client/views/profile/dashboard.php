<?php

use yii\helpers\Url;
use app\components\SlickSlider;

app\components\assets\CatalogAssets::register($this);
?>
<section class="bazzers">
    <div class="block">
        <?php
        SlickSlider::begin([
            'id' => 'promo_list',
            'qty' => 1,
            'tagOptions' => ['class' => 'centered'],
            'options' => [
                'dots' => true,
                'arrows' => false,
                'slidesToShow' => 1,
                'autoplay' => true,
                'autoplaySpeed' => 3000,
            ],
        ]);
        ?>
        <?php foreach ($promo as $dataModel): ?>
            <?php if ($dataModel->getModelFiles('cover')->isExists()): ?>
                <div>
                    <a href="<?php echo $dataModel->getUrl(); ?>"><img class="" src="<?php echo $dataModel->getModelFiles('cover')->getSrc('big'); ?>" alt=""></a>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php SlickSlider::end(); ?>
    </div>
    <div>
        <?php foreach ($data as $dataModel): ?>
            <a href="<?php echo $dataModel->getUrl(); ?>">
                <img src="<?php echo $dataModel->getModelFiles('photo')->getSrc(); ?>" alt="">
            </a>
        <?php endforeach; ?>
    </div>
</section>
<section class="bazzers">
    <div class="block">
        <div>
            <?php foreach ($data as $dataModel): ?>
                <a href="<?php echo $dataModel->getUrl(); ?>">
                    <img src="<?php echo $dataModel->getModelFiles('photo')->getSrc(); ?>" alt="">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>