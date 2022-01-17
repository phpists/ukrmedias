<?php

use app\models\Services;
use app\models\Devices;
use app\models\AccessLogic;
?><div class="">
    <!-- BEGIN .card -->
    <div class="card card--auto">
        <a href="javascript:void(0);" data-action="collapse" class="card__header card__header-bg__green card__header--gap card__header--radius">
            <span class="card__header-icon icon-blue icon-clock-18 icon-bg-grey-24">
                <svg class="icon icon-clock-24"><use xlink:href="img/sprite.svg#icon-clock-24"></use></svg>
            </span>
            <div class="card__header-text card__header-text__white">
                <?php if ($hwModel->device->type_id == Devices::TYPE_DISTRIBUTOR): ?>
                    <span class="text-exsmall">Розподілювач: </span> <span class="text-medium text-small"><?php echo $hwModel->device->snumber; ?></span>
                <?php else: ?>
                    <span class="text-exsmall">Номер лічильника: </span> <span class="text-medium text-small"><?php echo $hwModel->counter->number; ?></span>
                <?php endif; ?>
            </div>
            <span class="card__chevron card__chevron-show icon-grey">
                <svg class="icon icon-icon-chevron-24"><use xlink:href="img/sprite.svg#icon-icon-chevron-24"></use></svg>
            </span>
        </a>
        <div class="card__body card__body--gap  is-active is-collapsible" data-allow-multiple="true">
            <!-- BEGIN .accouting__wrapper -->
            <div class="accouting__wrapper  columns is-multiline">
                <div class="column is-4 is-6-mobile ">
                    <?php if ($hwModel->device->type_id != Devices::TYPE_DISTRIBUTOR): ?>
                        <div class="accouting__item">
                            <div class="text-exsmall text-lgrey">Модель:</div>
                            <div class="text-small text-normal text-dark"><?php echo $hwModel->counter->model; ?></div>
                        </div>
                        <div class="accouting__item">
                            <div class="text-exsmall text-lgrey">Дата повірки:</div>
                            <div class="text-small text-normal text-dark"><?php echo $hwModel->counter->getDate(); ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="accouting__item">
                        <div class="text-exsmall text-lgrey">Показання (<?php echo $hwModel->getUnit(); ?>):</div>
                        <?php if (is_numeric($hwModel->lastValue) && AccessLogic::isLoginAs()): ?>
                            <div class="text-small text-normal text-dark js_sync_device" data-sync="<?php echo $hwModel->lastAttributes; ?>" style="cursor:pointer;"><?php echo $hwModel->lastValue; ?></div>
                        <?php else: ?>
                            <div class="text-small text-normal text-dark"><?php echo $hwModel->lastValue; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="accouting__item">
                        <div class="text-exsmall text-lgrey">Оновлення:</div>
                        <div class="text-small text-normal text-dark"><?php echo $hwModel->lastDate; ?></div>
                    </div>
                </div>
                <div class="column is-8 is-6-mobile ">
                    <div class="columns is-multiline">
                        <div class="column is-6 is-12-mobile is-12-tablet ">
                            <div class="accouting__item">
                                <div class="text-exsmall text-lgrey">Призначення:</div>
                                <div class="text-small text-normal text-dark"><?php echo $hwModel->device->getDataType(); ?></div>
                            </div>
                            <div class="accouting__item">
                                <div class="text-exsmall text-lgrey">Модуль:</div>
                                <div class="text-small text-normal text-dark"><?php echo $hwModel->device->getType(); ?></div>
                            </div>
                            <div class="accouting__item">
                                <div class="text-exsmall text-lgrey">Серійний номер:</div>
                                <div class="text-small text-normal text-dark"><?php echo $hwModel->device->snumber; ?></div>
                            </div>
                            <div class="accouting__item">
                                <div class="text-exsmall text-lgrey">Місце:</div>
                                <div class="text-small text-normal text-dark"><?php echo $hwModel->device->getPlace(); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END .accouting__wrapper -->
        </div>
    </div>
    <!-- END .card -->
</div>