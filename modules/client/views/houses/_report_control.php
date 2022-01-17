<?php

use yii\helpers\Html;
use app\models\DataHelper;
?>
<div class="report-dates <?php if (count($hardware) > 1): ?>long<?php else: ?>short<?php endif; ?>">
    <div class="calendar-wrapper calendar-no-column">
        <?php if (isset($title)): ?>
            <div class="title"><?php echo $title; ?></div>
        <?php endif; ?>
        <div id="calendar-head" class="calendar-head">
            <?php foreach ($filter as $group => $label): ?>
                <div class="calendar-head__item js-calendar-tabs" data-id="<?php echo $group; ?>"><?php echo $label; ?></div>
            <?php endforeach; ?>
        </div>
        <div class="calendar-body">
            <input id="input_alt_<?php echo DataHelper::GROUP_HOURS; ?>" type="hidden" value="<?php echo date('Y-m-d', strtotime('-1 day')); ?> - <?php echo date('Y-m-d'); ?>"/>
            <input id="input_alt_<?php echo DataHelper::GROUP_DAY; ?>" type="hidden" value="<?php echo date('Y-m-d'); ?>"/>
            <input id="input_alt_<?php echo DataHelper::GROUP_DAYS; ?>" type="hidden" value="<?php echo date('Y-m-d'); ?>"/>
            <input id="input_alt_<?php echo DataHelper::GROUP_MONTH; ?>" type="hidden" value="<?php echo date('Y-m-d'); ?>"/>
            <input id="input_alt_<?php echo DataHelper::GROUP_RAW; ?>" type="hidden" value="<?php echo date('Y-m-01'); ?> - <?php echo date('Y-m-t'); ?>"/>
            <div id="calendar-body-<?php echo DataHelper::GROUP_HOURS; ?>" class="calendar-body__item js-calendar-tab-body js-range-calendar">
                <span class="calendar__calend icon-grey icon-bg-white">
                    <svg class="icon icon-calendar"><use xlink:href="img/sprite.svg#icon-calendar"></use></svg>
                </span>
                <input id="input_<?php echo DataHelper::GROUP_HOURS; ?>" type="text" class="calendar-body__input calendar-body__input-show " data-default-time="<?php echo date('Y-m-d', strtotime('-1 day')); ?> - <?php echo date('Y-m-d'); ?>" readonly/>
                <span class="calendar-body__date  " data-toggle></span>
            </div>
            <div id="calendar-body-<?php echo DataHelper::GROUP_DAY; ?>" class="calendar-body__item js-calendar-tab-body js-day-calendar">
                <span class="calendar__arrow icon-grey prev-btn icon-bg-white">
                    <svg class="icon icon-arrow-left"><use xlink:href="img/sprite.svg#icon-arrow-left"></use></svg>
                </span>
                <span class="calendar__arrow-chev icon-grey  prev-btn">
                    <svg class="icon icon-icon-chevron-32-left"><use xlink:href="img/sprite.svg#icon-icon-chevron-32-left"></use></svg>
                </span>
                <input id="input_<?php echo DataHelper::GROUP_DAY; ?>" type="text" class="calendar-body__input calendar-body__input-show" data-default-time="<?php echo date('Y-m-d'); ?>" readonly/>
                <span class="calendar-body__date  " data-toggle></span>
                <span class="calendar__arrow icon-grey icon-bg-white  next-btn">
                    <svg class="icon icon-arrow-right"><use xlink:href="img/sprite.svg#icon-arrow-right"></use></svg>
                </span>
                <span class="calendar__arrow-chev icon-grey  next-btn">
                    <svg class="icon icon-icon-chevron-32-right"><use xlink:href="img/sprite.svg#icon-icon-chevron-32-right"></use></svg>
                </span>
            </div>
            <div id="calendar-body-<?php echo DataHelper::GROUP_DAYS; ?>" class="calendar-body__item js-calendar-tab-body js-day-calendar">
                <span class="calendar__arrow icon-grey prev-btn icon-bg-white">
                    <svg class="icon icon-arrow-left"><use xlink:href="img/sprite.svg#icon-arrow-left"></use></svg>
                </span>
                <span class="calendar__arrow-chev icon-grey  prev-btn">
                    <svg class="icon icon-icon-chevron-32-left"><use xlink:href="img/sprite.svg#icon-icon-chevron-32-left"></use></svg>
                </span>
                <input id="input_<?php echo DataHelper::GROUP_DAYS; ?>" type="text" class="calendar-body__input calendar-body__input-show" data-default-time="<?php echo date('Y-m-d'); ?>" readonly/>
                <span class="calendar-body__date  " data-toggle></span>
                <span class="calendar__arrow icon-grey icon-bg-white  next-btn">
                    <svg class="icon icon-arrow-right"><use xlink:href="img/sprite.svg#icon-arrow-right"></use></svg>
                </span>
                <span class="calendar__arrow-chev icon-grey  next-btn">
                    <svg class="icon icon-icon-chevron-32-right"><use xlink:href="img/sprite.svg#icon-icon-chevron-32-right"></use></svg>
                </span>
            </div>
            <div id="calendar-body-<?php echo DataHelper::GROUP_MONTH; ?>" class="calendar-body__item js-calendar-tab-body js-month-calendar">
                <span class="calendar__arrow icon-grey prev-btn icon-bg-white">
                    <svg class="icon icon-arrow-left"><use xlink:href="img/sprite.svg#icon-arrow-left"></use></svg>
                </span>
                <span class="calendar__arrow-chev icon-grey  prev-btn">
                    <svg class="icon icon-icon-chevron-32-left"><use xlink:href="img/sprite.svg#icon-icon-chevron-32-left"></use></svg>
                </span>
                <input id="input_<?php echo DataHelper::GROUP_MONTH; ?>" type="text" class="calendar-body__input calendar-body__input-show" data-default-time="<?php echo date('Y-m-d'); ?>" readonly/>
                <span class="calendar-body__date  " data-toggle></span>
                <span class="calendar__arrow icon-grey icon-bg-white  next-btn">
                    <svg class="icon icon-arrow-right"><use xlink:href="img/sprite.svg#icon-arrow-right"></use></svg>
                </span>
                <span class="calendar__arrow-chev icon-grey  next-btn">
                    <svg class="icon icon-icon-chevron-32-right"><use xlink:href="img/sprite.svg#icon-icon-chevron-32-right"></use></svg>
                </span>
            </div>
            <div id="calendar-body-<?php echo DataHelper::GROUP_RAW; ?>" class="calendar-body__item js-calendar-tab-body js-range-calendar">
                <span class="calendar__calend icon-grey icon-bg-white">
                    <svg class="icon icon-calendar"><use xlink:href="img/sprite.svg#icon-calendar"></use></svg>
                </span>
                <input id="input_<?php echo DataHelper::GROUP_RAW; ?>" type="text" class="calendar-body__input calendar-body__input-show " data-default-time="<?php echo date('Y-m-01'); ?> - <?php echo date('Y-m-t'); ?>" readonly/>
                <span class="calendar-body__date  " data-toggle></span>
            </div>
        </div>
        <?php if ($excelOnly === true): ?>
            <a href="<?php echo $downloadUrl; ?>" class="biginfo--header-item icon-grey js-download_report pr-5 hover-orange text-dark download-btn">
                <svg class="icon icon-xls-32"><use xlink:href="img/sprite.svg#icon-xls-32"></use></svg>
                <span class="tooltiptext">Отримати&nbsp;зведений&nbsp;звіт</span>
            </a>
        <?php endif; ?>
    </div>
    <div class="report-variants">
        <div id="biginfo--header-nav" class="biginfo--header-nav js-big-tabs w100">
            <!-- value="" -->
            <?php if (count($hardware) > 1): ?>
                <?php
                echo '<script>APP_SETTINGS.reportCounter="' . key($hardware) . '"</script>';
                echo Html::dropDownList(null, key($hardware), $hardware, ['class' => 'js-dropdown-select form-default__select js-set_counter']);
                ?>
            <?php endif; ?>
            <?php if ($excelOnly === false): ?>
                <a data-id="<?php echo DataHelper::REPORT_VIEW_LIST; ?>" href="javascript:void(0);" class="biginfo--header-item js_report_view">
                    <div class="tooltip">
                        <span class="tooltiptext">Список</span>
                        <svg class="icon icon-tabs-list-32"><use xlink:href="img/sprite.svg#icon-tabs-list-32"></use></svg>
                    </div>
                </a>
                <a data-id="<?php echo DataHelper::REPORT_VIEW_TABLE; ?>"href="javascript:void(0);" class="biginfo--header-item hide-mob js_report_view">
                    <div class="tooltip">
                        <span class="tooltiptext">Таблиця</span>
                        <svg class="icon icon-tabs-table-32"><use xlink:href="img/sprite.svg#icon-tabs-table-32"></use></svg>
                    </div>
                </a>
                <a data-id="<?php echo DataHelper::REPORT_VIEW_CHART; ?>"href="javascript:void(0);" class="biginfo--header-item js_report_view">
                    <div class="tooltip">
                        <span class="tooltiptext">График</span>
                        <svg class="icon icon-tabls-chart-32"><use xlink:href="img/sprite.svg#icon-tabls-chart-32"></use></svg>
                    </div>
                </a>
                <a href="<?php echo $downloadUrl; ?>" class="biginfo--header-item icon-grey js-download_report">
                    <div class="tooltip">
                        <span class="tooltiptext">Отримати&nbsp;звіт&nbsp;в&nbsp;Excel</span>
                        <svg class="icon icon-xls-32"><use xlink:href="img/sprite.svg#icon-xls-32"></use></svg>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if ($htmlReport === true): ?>
    <div class="biginfo--body">
        <div class="biginfo--item is-active js-biginfo-item" id="bigtabs" data-url="<?php echo $reportUrl; ?>"></div>
    </div>
<?php endif; ?>
