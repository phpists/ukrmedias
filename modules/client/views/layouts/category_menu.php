<?php foreach ($level as $data): ?>
    <div class="menu_catalog transY0" id="category_<?php echo $data['model']->id; ?>">
        <div class="head">
            <?php if ($data['model']->level == 2): ?>
                Каталог товарів
            <?php else: ?>
                <span class="js_back pointer" data-menu="#category_<?php echo $parent->id; ?> .item"><?php echo $parent->getSiteTitle(); ?></span>
            <?php endif; ?>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="js_close">
                <path d="M15.7071 1.70711C16.0976 1.31658 16.0976 0.683417 15.7071 0.292893C15.3166 -0.0976311 14.6834 -0.0976311 14.2929 0.292893L8 6.58579L1.70711 0.292893C1.31658 -0.0976311 0.683417 -0.0976311 0.292893 0.292893C-0.0976311 0.683417 -0.0976311 1.31658 0.292893 1.70711L6.58579 8L0.292893 14.2929C-0.0976311 14.6834 -0.0976311 15.3166 0.292893 15.7071C0.683417 16.0976 1.31658 16.0976 1.70711 15.7071L8 9.41421L14.2929 15.7071C14.6834 16.0976 15.3166 16.0976 15.7071 15.7071C16.0976 15.3166 16.0976 14.6834 15.7071 14.2929L9.41421 8L15.7071 1.70711Z" fill=""/>
            </svg>
        </div>
        <div class="nav">
            <?php foreach ($level as $data2): ?>
                <?php if (count($data2['children']) > 0): ?>
                    <div class="item <?php echo $data['model']->id === $data2['model']->id ? 'col_bdr' : 'col_blc'; ?>" data-submenu="#category_<?php echo $data['model']->id; ?>_submenu_<?php echo $data2['model']->id; ?>">
                        <picture><source srcset="<?php echo $data2['model']->getModelFiles('cover_1')->getSrc('small'); ?>" type="image/webp"/><img src="<?php echo $data2['model']->getModelFiles('cover_1')->getSrc('small'); ?>" alt=""></picture>
                        <?php echo $data2['model']->getSiteTitle(); ?>
                        <picture><source srcset="img/menu-catalog/arrow-right.svg" type="image/webp"/><img src="img/menu-catalog/arrow-right.svg" alt=""></picture>
                    </div>
                <?php else: ?>
                    <a class="item" href="<?php echo $data2['model']->getUrl(); ?>">
                        <picture class="small">
                            <source srcset="<?php echo $data2['model']->getModelFiles('cover_1')->getSrc('small'); ?>" type="image/webp"/><img src="<?php echo $data2['model']->getModelFiles('cover_1')->getSrc('small'); ?>" alt="">
                        </picture>
                        <p><?php echo $data2['model']->getSiteTitle(); ?></p>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php foreach ($level as $data2): ?>
            <div class="references <?php echo $data['model']->id === $data2['model']->id ? 'db' : 'dn'; ?>" id="category_<?php echo $data['model']->id; ?>_submenu_<?php echo $data2['model']->id; ?>">
                <?php foreach ($data2['children'] as $data3): ?>
                    <?php if (count($data3['children']) > 0): ?>
                        <div class="item-menu" data-menu="#category_<?php echo $data3['model']->id; ?>">
                            <picture><source srcset="<?php echo $data3['model']->getModelFiles('cover_1')->getSrc('small'); ?>" type="image/webp"/><img src="<?php echo $data3['model']->getModelFiles('cover_1')->getSrc('small'); ?>" alt=""></picture>
                            <?php echo $data3['model']->getSiteTitle(); ?>
                        </div>
                    <?php else: ?>
                        <a class="item" href="<?php echo $data3['model']->getUrl(); ?>">
                            <picture>
                                <source srcset="<?php echo $data3['model']->getModelFiles('cover_1')->getSrc('small'); ?>" type="image/webp"/><img src="<?php echo $data3['model']->getModelFiles('cover_1')->getSrc('small'); ?>" alt="">
                            </picture>
                            <p><?php echo $data3['model']->getSiteTitle(); ?></p>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (count($data['children']) > 0): ?>
        <?php echo $this->context->renderFile('@module/views/layouts/category_menu.php', ['level' => $data['children'], 'parent' => $data['model']]); ?>
    <?php endif; ?>
<?php endforeach; ?>
