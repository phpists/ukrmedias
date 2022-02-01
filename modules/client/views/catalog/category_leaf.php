<?php

use yii\helpers\Html;
use app\models\GoodsFilter;
?>
<?php $filter->beginForm(); ?>
<section class="category" id="category">
    <div class="block">
        <div class="nav">
            <div class="locality">
                <?php foreach ($model->findParents() as $i => $dataModel): ?>
                    <?php if ($i > 0): ?>•<?php endif; ?> <a href="<?php echo $brandCpu === null ? $dataModel->getUrl() : $dataModel->getUrl(['brandCpu' => $brandCpu]); ?>"><?php echo $dataModel->getTitle() ?></a>
                <?php endforeach; ?>
                <span>• <?php echo $model->getTitle() ?></span>
            </div>
            <div class="show">
                Показувати:
                <div class="quantity">
                    <span><?php echo $filter->get(GoodsFilter::$pageSize); ?></span>
                    <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""/>
                    </svg>
                </div>
                <div class="transY0" id="quantity_choise">
                    <?php foreach (GoodsFilter::$pageSizeLabels as $value => $label): ?>
                        <label>
                            <?php echo Html::radio(GoodsFilter::$pageSize, $filter->isChecked(GoodsFilter::$pageSize, $value), ['value' => $value]); ?>
                            <?php echo $label; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div id="sort" class="sort">
                <span class="sort-title"><?php echo GoodsFilter::$sortLabels[$filter->get(GoodsFilter::$sortOrder)]; ?></span>
                <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""/>
                </svg>
                <div class="transY0">
                    <?php foreach (GoodsFilter::$sortLabels as $value => $label): ?>
                        <label>
                            <?php echo Html::radio(GoodsFilter::$sortOrder, $filter->isChecked(GoodsFilter::$sortOrder, $value), ['value' => $value]); ?>
                            <?php echo $label; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <label class="formation" id="formation1">
                <?php echo Html::radio(GoodsFilter::$css, $filter->isChecked(GoodsFilter::$css, ''), ['value' => '']); ?>
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1 0C0.447715 0 0 0.447715 0 1V7C0 7.55229 0.447715 8 1 8H7C7.55229 8 8 7.55229 8 7V1C8 0.447715 7.55229 0 7 0H1ZM2.5 2C2.22386 2 2 2.22386 2 2.5V5.5C2 5.77614 2.22386 6 2.5 6H5.5C5.77614 6 6 5.77614 6 5.5V2.5C6 2.22386 5.77614 2 5.5 2H2.5Z" fill=""/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1 10C0.447715 10 0 10.4477 0 11V17C0 17.5523 0.447715 18 1 18H7C7.55229 18 8 17.5523 8 17V11C8 10.4477 7.55229 10 7 10H1ZM2.5 12C2.22386 12 2 12.2239 2 12.5V15.5C2 15.7761 2.22386 16 2.5 16H5.5C5.77614 16 6 15.7761 6 15.5V12.5C6 12.2239 5.77614 12 5.5 12H2.5Z" fill=""/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11 0C10.4477 0 10 0.447715 10 1V7C10 7.55229 10.4477 8 11 8H17C17.5523 8 18 7.55229 18 7V1C18 0.447715 17.5523 0 17 0H11ZM12.5 2C12.2239 2 12 2.22386 12 2.5V5.5C12 5.77614 12.2239 6 12.5 6H15.5C15.7761 6 16 5.77614 16 5.5V2.5C16 2.22386 15.7761 2 15.5 2H12.5Z" fill=""/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11 10C10.4477 10 10 10.4477 10 11V17C10 17.5523 10.4477 18 11 18H17C17.5523 18 18 17.5523 18 17V11C18 10.4477 17.5523 10 17 10H11ZM12.5 12C12.2239 12 12 12.2239 12 12.5V15.5C12 15.7761 12.2239 16 12.5 16H15.5C15.7761 16 16 15.7761 16 15.5V12.5C16 12.2239 15.7761 12 15.5 12H12.5Z" fill=""/>
                </svg>
            </label>
            <label class="formation" id="formation2">
                <?php echo Html::radio(GoodsFilter::$css, $filter->isChecked(GoodsFilter::$css, 'view-lines'), ['value' => 'view-lines']); ?>
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1 0C0.447715 0 0 0.447715 0 1V7C0 7.55229 0.447715 8 1 8H17C17.5523 8 18 7.55229 18 7V1C18 0.447715 17.5523 0 17 0H1ZM2.5 2C2.22386 2 2 2.22386 2 2.5V5.5C2 5.77614 2.22386 6 2.5 6H15.5C15.7761 6 16 5.77614 16 5.5V2.5C16 2.22386 15.7761 2 15.5 2H2.5Z" fill=""/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1 10C0.447715 10 0 10.4477 0 11V17C0 17.5523 0.447715 18 1 18H17C17.5523 18 18 17.5523 18 17V11C18 10.4477 17.5523 10 17 10H1ZM2.5 12C2.22386 12 2 12.2239 2 12.5V15.5C2 15.7761 2.22386 16 2.5 16H15.5C15.7761 16 16 15.7761 16 15.5V12.5C16 12.2239 15.7761 12 15.5 12H2.5Z" fill=""/>
                </svg>
            </label>
            <div class="options"></div>
            <div class="show_params" id="show_params">
                <p class="rolledUp">показати параметри</p>
                <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""></path>
                </svg>
            </div>
            <div class="filters">
                <?php if (count($brands) > 0): ?>
                    <div class="item">
                        <p class="js_param_title js_self_value">Бренди</p>
                        <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""/>
                        </svg>
                        <div class="drop">
                            <h6>Пошук</h6>
                            <div class="checkbox_cont">
                                <?php foreach ($brands as $id => $title): ?>
                                    <label class="checkbox">
                                        <?php echo $filter->checkbox(GoodsFilter::$brands, $id); ?>
                                        <span><?php echo $title; ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php foreach ($cats as $data): ?>
                    <div class="item">
                        <p class="js_param_title"><?php echo $data['title']; ?></p>
                        <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""/>
                        </svg>
                        <div class="drop">
                            <h6>Пошук</h6>
                            <div class="checkbox_cont">
                                <?php foreach ($data['children'] as $id => $title): ?>
                                    <label class="checkbox">
                                        <?php echo $filter->checkbox(GoodsFilter::$cats, $id); ?>
                                        <span><?php echo $title; ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php foreach ($params as $param_id => $data) { ?>
                    <div class="item">
                        <p class="js_param_title"><?php echo $data['title']; ?></p>
                        <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""/>
                        </svg>
                        <div class="drop">
                            <h6>Пошук</h6>
                            <div class="checkbox_cont" id="param_<?= $param_id ?>">
                                <?php foreach ($data['values'] as $value => $title){

                                    $f = $facets[$param_id] ?? [];
                                    if (isset($facets[$param_id])){
                                        $values = array_column($facets[$param_id], 'id');
                                        $is = in_array($value, $values);

                                        if (!$is){
                                            continue;
                                        }
                                    }

                                    ?>
                                    <label class="checkbox">
                                        <?php echo $filter->checkbox(GoodsFilter::$params, [$param_id => $value]); ?>
                                        <span><?php echo $title; ?></span>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php

        $filter->beginResult();
        echo $this->context->renderPartial('_filter_result', ['model' => $model, 'filter' => $filter]);
        ?>
        <?php $filter->endResult(); ?>
    </div>
</section>
<?php $filter->endForm(); ?>
<section class="category">
    <div class="block">
        <?php echo $this->context->renderPartial('_download_btn', ['cat_id' => $model->id, 'category_level' => $category_level ?? 1]); ?>
    </div>
</section>
<div id="js_filter_options_item_tpl" style="display:none;">
    <div>
        <span class="js_p_title"></span><span class="js_p_values"></span>
    </div>
</div>