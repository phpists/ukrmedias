<?php

use yii\helpers\Url;
use app\components\assets\ClientAssets;
use app\components\Misc;
use app\models\Settings;
use app\models\DataHelper;
use app\models\Feedback;
use app\models\AccessLogic;
use app\models\Firms;
use app\models\Goods;

$this->beginPage();

ClientAssets::register($this);
$this->registerCsrfMetaTags();
$route = $this->context->getRoute();
$firm = Yii::$app->user->isGuest ? new Firms() : Yii::$app->user->identity->getFirm();
?>


<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language ?>">
    <head>
        <base href="<?php echo Yii::$app->request->getBaseUrl(true); ?>/files/frontend/"/>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <title><?php echo $this->title; ?></title>
        <?php $this->head(); ?>
    </head>
    <body>
        <?php
        $this->beginBody();
        echo Misc::TestVersionHeader();
        $this->params['current_cat_ids'] = $this->context->categoryModel instanceof \app\models\Category ? $this->context->categoryModel->getIdsUp(true) : [];
        ?>
        <!----- Хедер 1 ----->
        <?php echo $this->context->renderFile('@module/views/layouts/header_small.php', ['firm' => $firm]); ?>
        <!----- orders ----->

        <div class="orders" id="orders">
            <div class="block">
                <?php echo app\components\Misc::getFlashMessages(); ?>
                <p class="locality"><?php echo $firm->getTitle(); ?></p>
                <div class="cont">
                    <div class="nav">
                        <div class="links">
                            <a href="<?php echo Url::to(['/client/preorders/index']); ?>" class="<?php if (preg_match('@^client/preorders/@', $route)): ?>Active<?php else: ?>notActive<?php endif; ?>">Мої заявки</a>
                            <a href="<?php echo Url::to(['/client/orders/index']); ?>" class="<?php if (preg_match('@^client/orders/@', $route)): ?>Active<?php else: ?>notActive<?php endif; ?>">Мої замовлення</a>
                            <a href="<?php echo Url::to(['/client/reports/index']); ?>" class="<?php if (preg_match('@^client/reports/@', $route)): ?>Active<?php else: ?>notActive<?php endif; ?>">Звіти</a>
                            <a href="<?php echo Url::to(['/client/downloads/index']); ?>" class="<?php if (preg_match('@^client/downloads/@', $route)): ?>Active<?php else: ?>notActive<?php endif; ?>">Завантаження</a>
                            <a href="<?php echo Url::to(['/client/feedback/index']); ?>" class="separator <?php if (preg_match('@^client/feedback/@', $route)): ?>Active<?php else: ?>notActive<?php endif; ?>">Зворотній зв'язок</a>
                            <a href="<?php echo Url::to(['/client/profile/index']); ?>" class="<?php if (preg_match('@^client/profile/@', $route)): ?>Active<?php else: ?>notActive<?php endif; ?>">Профіль</a>
                            <a href="<?php echo Url::to(['/client/managers/index']); ?>" class="<?php if (preg_match('@^client/managers/@', $route)): ?>Active<?php else: ?>notActive<?php endif; ?>">Мої співробітники</a>
                            <a href="<?php echo Url::to(['/client/addresses/index']); ?>" class="<?php if (preg_match('@^client/addresses/@', $route)): ?>Active<?php else: ?>notActive<?php endif; ?>">Адреси доставки</a>
                            <a class="exit" href="<?php echo Url::to(['/frontend/site/logout']); ?>" class="notActive">Вихід</a>
                        </div>
                        <div class="manager">
                            <?php if ($firm->manager_name <> ''): ?>
                                <p>Ваш менеджер:</p>
                                <p><?php echo $firm->manager_name; ?></p>
                                <?php if ($firm->manager_phone <> ''): ?>
                                    <a href="tel:<?php echo Misc::phoneFilter($firm->manager_phone); ?>">
                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 1.49951C0 0.671352 0.671573 0 1.5 0H3.48345C4.21671 0 4.84249 0.529945 4.96304 1.25299L5.23996 2.91395C5.35404 3.59822 4.95319 4.26376 4.29487 4.48313L3.93333 4.6036C3.78228 4.65394 3.69688 4.81346 3.73877 4.96702L4.11501 6.34609C4.32253 7.10677 4.89662 7.71344 5.64487 7.96278L6.56011 8.26776C6.68845 8.31052 6.82955 8.26213 6.90459 8.1496L7.34197 7.49374C7.71719 6.93111 8.42267 6.68913 9.06439 6.90296L10.9743 7.53941C11.5869 7.74351 12 8.31653 12 8.96196V10.5005C12 11.3286 11.3284 12 10.5 12H9.3C4.16375 12 0 7.83762 0 2.70306V1.49951Z" fill=""/>
                                        </svg>
                                        <?php echo $firm->manager_phone; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php echo $content; ?>
                </div>
            </div>
        </div>

        <a href="#header" class="btn_up dn" id="btn_up">
            <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M6 0C6.26522 0 6.51957 0.105357 6.70711 0.292893L11.7071 5.29289C12.0976 5.68342 12.0976 6.31658 11.7071 6.70711C11.3166 7.09763 10.6834 7.09763 10.2929 6.70711L6 2.41421L1.70711 6.70711C1.31658 7.09763 0.683417 7.09763 0.292893 6.70711C-0.0976311 6.31658 -0.0976311 5.68342 0.292893 5.29289L5.29289 0.292893C5.48043 0.105357 5.73478 0 6 0Z" fill=""/>
            </svg>
        </a>
        <div class="dark_fond opac0" id="delete-confirm">
            <div class="staff_delete">
                <h3>Видалення</h3>
                <div class="text"></div>
                <br/>
                <div class="buttons">
                    <div class="close">Відмінити</div>
                    <div class="confirm">Підтвердити</div>
                </div>
            </div>
        </div>
        <?php $this->endBody(); ?>
    </body>
</html>
<?php $this->endPage(); ?>