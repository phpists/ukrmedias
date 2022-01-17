<?php

use yii\helpers\Url;

$clientFlats = $house->getClientsFlats();
$collapse = count($clientFlats) === 0;
?>
<div class="column-narrow">
    <div class="card card-fix-height <?php if ($collapse): ?>collapsed<?php endif; ?>" id="accordion_card_left" style="width:<?php if ($collapse): ?>50<?php else: ?>300<?php endif; ?>px;">
        <div class="js-collapsed-block-alt">
            <div class="h card__header-bg__grey">
                <a id="js-chevron" class="card__chevron icon-grey js-collapse" href="javascript:void(0);"><svg class="icon icon-icon-chevron-24"><use xlink:href="img/sprite.svg#icon-icon-chevron-24"></use></svg></a>
            </div>
            <div class="js-collapsed-block-label"><?php echo $house->getTitle(); ?></div>
        </div>
        <div class="js-collapsed-block">
            <?php if ($flat_id > 0): ?>
                <div class="card__header card__header-bg__grey">
                    <a href="<?php echo Url::toRoute(['/client/houses/details', 'id' => $house->id, 'aid' => $service_id]); ?>">
                        <span class="card__header-text__small  card__header-text__white"><?php echo $house->getTitle(); ?></span>
                    </a>
                    <a class="card__chevron icon-grey js-collapse" href="javascript:void(0);"><svg class="icon icon-icon-chevron-24"><use xlink:href="img/sprite.svg#icon-icon-chevron-24"></use></svg></a>
                </div>
            <?php else: ?>
                <div class="card__header card__header-bg__green">
                    <span class="card__header-text card__header-text__small card__header-text__white js-active-text"><?php echo $house->getTitle(); ?></span>
                    <a class="card__chevron icon-white js-collapse" href="javascript:void(0);"><svg class="icon icon-icon-chevron-24"><use xlink:href="img/sprite.svg#icon-icon-chevron-24"></use></svg></a>
                </div>
            <?php endif; ?>
            <?php
            if (count($client->houseServices) > 0):
                $abonents = $house->getAbonentsByFlats();
                ?>
                <div class="card__body is-collapsible">
                    <div class="card__body card__body__gaped">
                        <?php foreach ($clientFlats as $flatModel): ?>
                            <div class="card__body-item <?php if ($flatModel->id === $flat_id): ?>card__header-bg__green js-active-text<?php endif; ?>">
                                <a href="<?php echo Url::toRoute(['/client/flats/details', 'id' => $flatModel->id, 'aid' => $service_id]); ?>" class="card__body-link  ">
                                    <span class="card__body-number"> <?php echo $flatModel->no; ?> </span>
                                    <span class="card__body-text">
                                        <?php if (isset($abonents[$flatModel->id])) : ?>
                                            <?php echo $abonents[$flatModel->id]->getNameCombined(); ?>
                                        <?php endif; ?>
                                    </span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="buttons-group">
                        <a href="<?php echo Url::toRoute('/client/houses/update'); ?>" class="btn-add-white mb24">
                            <div class="icon">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.33333 0C6.96514 0 6.66667 0.298477 6.66667 0.666667V6.66667H0.666667C0.298477 6.66667 0 6.96514 0 7.33333V8.66667C0 9.03486 0.298477 9.33333 0.666667 9.33333H6.66667V15.3333C6.66667 15.7015 6.96514 16 7.33333 16H8.66667C9.03486 16 9.33333 15.7015 9.33333 15.3333V9.33333H15.3333C15.7015 9.33333 16 9.03486 16 8.66667V7.33333C16 6.96514 15.7015 6.66667 15.3333 6.66667H9.33333V0.666667C9.33333 0.298477 9.03486 0 8.66667 0H7.33333Z" fill="#918DA6" /></svg>
                            </div>
                            <span>Додати адресу</span>
                        </a>
                        <?php if ($house->flats_qty > 0): ?>
                            <a class="btn-add-white mb24" href="<?php echo Url::toRoute(['/client/houses/add', 'id' => $house->id, 'aid' => $service_id]); ?>">
                                <div class="icon">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.33333 0C6.96514 0 6.66667 0.298477 6.66667 0.666667V6.66667H0.666667C0.298477 6.66667 0 6.96514 0 7.33333V8.66667C0 9.03486 0.298477 9.33333 0.666667 9.33333H6.66667V15.3333C6.66667 15.7015 6.96514 16 7.33333 16H8.66667C9.03486 16 9.33333 15.7015 9.33333 15.3333V9.33333H15.3333C15.7015 9.33333 16 9.03486 16 8.66667V7.33333C16 6.96514 15.7015 6.66667 15.3333 6.66667H9.33333V0.666667C9.33333 0.298477 9.03486 0 8.66667 0H7.33333Z" fill="#918DA6" /></svg>
                                </div>
                                <span>Додати приміщення</span>
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo Url::toRoute('/client/abonents/import'); ?>" class="btn-add-white mb24">
                            <div class="icon">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.33333 0C6.96514 0 6.66667 0.298477 6.66667 0.666667V6.66667H0.666667C0.298477 6.66667 0 6.96514 0 7.33333V8.66667C0 9.03486 0.298477 9.33333 0.666667 9.33333H6.66667V15.3333C6.66667 15.7015 6.96514 16 7.33333 16H8.66667C9.03486 16 9.33333 15.7015 9.33333 15.3333V9.33333H15.3333C15.7015 9.33333 16 9.03486 16 8.66667V7.33333C16 6.96514 15.7015 6.66667 15.3333 6.66667H9.33333V0.666667C9.33333 0.298477 9.03486 0 8.66667 0H7.33333Z" fill="#918DA6" /></svg>
                            </div>
                            <span>Імпорт адрес</span>
                        </a>
                        <a href="<?php echo Url::toRoute(['/client/counters/add', 'id' => $house->id, 'aid' => $service_id, 'fid' => $flat_id]); ?>" class="btn-add-white mb24">
                            <div class="icon">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.33333 0C6.96514 0 6.66667 0.298477 6.66667 0.666667V6.66667H0.666667C0.298477 6.66667 0 6.96514 0 7.33333V8.66667C0 9.03486 0.298477 9.33333 0.666667 9.33333H6.66667V15.3333C6.66667 15.7015 6.96514 16 7.33333 16H8.66667C9.03486 16 9.33333 15.7015 9.33333 15.3333V9.33333H15.3333C15.7015 9.33333 16 9.03486 16 8.66667V7.33333C16 6.96514 15.7015 6.66667 15.3333 6.66667H9.33333V0.666667C9.33333 0.298477 9.03486 0 8.66667 0H7.33333Z" fill="#918DA6" /></svg>
                            </div>
                            <span>Додати прилад обліку</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
