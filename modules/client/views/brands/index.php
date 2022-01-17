<?php
app\components\assets\CatalogAssets::register($this);
?><section class="brands">
    <div class="block">
        <h5><?php echo $this->title = 'Бренди'; ?></h5>
        <div class="cont">
            <?php foreach ($data as $dataModel): ?>
                <a href="<?php echo $dataModel->getUrl(); ?>">
                    <img src="<?php echo $dataModel->getModelFiles('logo')->getSrc('medium'); ?>" alt="" class="brand">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>