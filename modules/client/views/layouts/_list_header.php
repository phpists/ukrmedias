<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
?>


<div class="staff_sort">
    <?php foreach ($widget->headerAttrs as $attr) : ?>
        <?php
        if ($widget->isSortable($attr)):
            $link = $widget->getSortLink($attr);
            ?>
            <a href="<?php echo $link['url']; ?>">
                <div class="unit <?php if ($link['direction'] === SORT_ASC || $link['direction'] === SORT_DESC): ?>selected<?php endif; ?>">
                    <span><?php echo $filterModel->getAttributeLabel($attr); ?></span>
                    <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg" class="<?php if ($link['direction'] === SORT_ASC): ?>selected<?php endif; ?>">
                        <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""/>
                    </svg>
                    <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg" class="<?php if ($link['direction'] === SORT_DESC): ?>selected<?php endif; ?>">
                        <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""/>
                    </svg>
                </div>
            </a>
        <?php else: ?>
            <div class="unit">
                <span><?php echo $filterModel->getAttributeLabel($attr); ?></span>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php if (count($widget->filter) > 0): ?>
    <form data-pjax>
        <div class="inform-table-sort">
            <?php foreach ($widget->headerAttrs as $attr): ?>
                <div class="unit">
                    <?php
                    if (isset($widget->filter[$attr])):
                        $type = &$widget->filter[$attr];
                        if (is_array($type)) :
                            echo Html::activeDropDownList($filterModel, $attr, $type, ['class' => 'w100']);
                        elseif ($type):
                            echo Html::activeInput('text', $filterModel, $attr, ['class' => 'w100', 'placeholder' => $filterModel->getAttributeLabel($attr)]);
                        endif;
                    endif;
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </form>
<?php endif; ?>