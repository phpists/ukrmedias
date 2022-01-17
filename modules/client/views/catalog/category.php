<section class="directory">
    <div class="block">
        <div class="choise">
            <?php foreach ($cats as $dataModel): ?>
                <div>
                    <a href="<?php echo $dataModel->getUrl(['brandCpu' => $brandCpu]); ?>"><img src="<?php echo $dataModel->getModelFiles('cover_1')->getSrc('medium'); ?>" alt=""><p><?php echo $dataModel->getTitle(); ?></p></a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="category">
    <div class="block">
        <?php echo $this->context->renderPartial('_download_btn', ['cat_id' => $model->id]); ?>
    </div>
</section>


