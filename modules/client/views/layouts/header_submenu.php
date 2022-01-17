<?php if ($this->context->categoryModel instanceof \app\models\Category): ?>
    <header>
        <div class="line_2">
            <?php foreach ($this->context->categoryModel->findLevelData($this->context->cats) as $data): ?>
                <div class="unit <?php echo in_array($data['model']->id, $this->params['current_cat_ids']) ? 'active' : ''; ?>">
                    <?php if (count($data['children']) > 0): ?>
                        <?php echo $data['model']->getSiteTitle(); ?>
                        <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""/>
                        </svg>
                        <div>
                            <p><?php echo $data['model']->getSiteTitle(); ?></p>
                            <?php foreach ($data['children'] as $levelData): ?>
                                <a href="<?php echo $levelData['model']->getUrl(); ?>" class="<?php echo in_array($levelData['model']->id, $this->params['current_cat_ids']) ? 'active' : ''; ?>"><?php echo $levelData['model']->getSiteTitle(); ?></a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo $data['model']->getUrl(); ?>"><?php echo $data['model']->getTitle(); ?></a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </header>
<?php endif; ?>