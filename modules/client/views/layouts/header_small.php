<?php

use yii\helpers\Url;
use app\components\Misc;
use app\models\Category;
use app\models\DataHelper;
use app\models\PreOrders;

$firm = Yii::$app->user->identity->getFirm();
$cartSummary = PreOrders::getCartSummary();
$menuLevelData = $this->context->categoryModel instanceof \app\models\Category ? $this->context->categoryModel->findLevelData($this->context->cats) : [];
$mobileMenu = $this->context->cats;
?>
<header id="header">
    <div class="line_1">
        <div id="main-logo"><a href="/"><img src="img/profile-start/um.svg"/></a></div>
        <div class="links">
            <?php foreach ($this->context->cats as $level_2): ?>
                <a href="<?php echo $level_2['model']->getUrl(); ?>" class="<?php echo in_array($level_2['model']->id, $this->params['current_cat_ids']) ? 'active' : ''; ?>"><?php echo $level_2['model']->getSiteTitle(); ?></a>
            <?php endforeach; ?>
            <a href="<?php echo Url::to(['/client/catalog/novelty']); ?>">Новинки (<?php echo DataHelper::getNoveltyQty(); ?>)</a>
            <a href="<?php echo Url::to(['/client/catalog/promo']); ?>" class="red">Outlet</a>
            <a href="<?php echo Url::to(['/client/brands/index']); ?>">Бренди</a>
            <div id="catalog_menu_btn">
                <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0 7C0 6.44772 0.447715 6 1 6H17C17.5523 6 18 6.44772 18 7C18 7.55228 17.5523 8 17 8H1C0.447715 8 0 7.55228 0 7Z" fill=""/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0 13C0 12.4477 0.447715 12 1 12H17C17.5523 12 18 12.4477 18 13C18 13.5523 17.5523 14 17 14H1C0.447715 14 0 13.5523 0 13Z" fill=""/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0 1C0 0.447715 0.447715 0 1 0H17C17.5523 0 18 0.447715 18 1C18 1.55228 17.5523 2 17 2H1C0.447715 2 0 1.55228 0 1Z" fill=""/>
                </svg>
                Каталог
            </div>
        </div>
        <div class="capabilities">
            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg" id="search_btn">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.9056 14.3199C11.551 15.3729 9.84871 16 8 16C3.58172 16 0 12.4183 0 8C0 3.58172 3.58172 0 8 0C12.4183 0 16 3.58172 16 8C16 9.84871 15.3729 11.551 14.3199 12.9056L16.7071 15.2929C17.0976 15.6834 17.0976 16.3166 16.7071 16.7071C16.3166 17.0976 15.6834 17.0976 15.2929 16.7071L12.9056 14.3199ZM14 8C14 11.3137 11.3137 14 8 14C4.68629 14 2 11.3137 2 8C2 4.68629 4.68629 2 8 2C11.3137 2 14 4.68629 14 8Z" fill=""/>
            </svg>
            <a id="cart_icon" href="<?php echo Url::to(['/client/cart/index']); ?>" class="<?php echo $cartSummary['qty'] > 0 ? 'active' : ''; ?>">
                <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.50835 9.59149C3.50729 9.58572 3.50632 9.57992 3.50545 9.57409L2.28534 2.2534C2.16479 1.53012 1.539 1 0.805746 1H0.5C0.223858 1 0 0.776142 0 0.5C0 0.223858 0.223858 0 0.5 0H0.805746C1.99756 0 3.01903 0.840292 3.25526 2H19.5C19.8322 2 20.072 2.31795 19.9808 2.63736L17.9808 9.63736C17.9194 9.85201 17.7232 10 17.5 10H4.59023L4.71466 10.7466C4.83521 11.4699 5.461 12 6.19425 12H17.5C17.7761 12 18 12.2239 18 12.5C18 12.7761 17.7761 13 17.5 13H6.19425C4.97216 13 3.92918 12.1165 3.72827 10.911L3.50835 9.59149ZM6 18C4.89543 18 4 17.1046 4 16C4 14.8954 4.89543 14 6 14C7.10457 14 8 14.8954 8 16C8 17.1046 7.10457 18 6 18ZM6 17C6.55228 17 7 16.5523 7 16C7 15.4477 6.55228 15 6 15C5.44772 15 5 15.4477 5 16C5 16.5523 5.44772 17 6 17ZM15 18C13.8954 18 13 17.1046 13 16C13 14.8954 13.8954 14 15 14C16.1046 14 17 14.8954 17 16C17 17.1046 16.1046 18 15 18ZM15 17C15.5523 17 16 16.5523 16 16C16 15.4477 15.5523 15 15 15C14.4477 15 14 15.4477 14 16C14 16.5523 14.4477 17 15 17Z" fill=""/>
                </svg>
                <span id="cart_summary_amount" class="amount"><?php echo $cartSummary['amount']; ?></span>
                <span>(</span><span id="cart_summary_info"><?php echo $cartSummary['info']; ?></span><span>)</span>
            </a>
            <div id="profile_btn" class="profile_btn">
                <?php echo Yii::$app->user->identity->getName(); ?>
                <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""/>
                </svg>
            </div>
        </div>
    </div>
    <div id="line_2" class="line_2">
        <?php
        foreach ($menuLevelData as $data):
            $active_2 = in_array($data['model']->id, $this->params['current_cat_ids']) ? 'active' : '';
            ?>
            <div class="level_data_2">
                <?php if (count($data['children']) > 0): ?>
                    <div class="cat_title <?php echo $active_2; ?>">
                        <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""/>
                        </svg>

                        <div class="cat_title_content">
                            <span><?php echo $data['model']->getSiteTitle(); ?></span>
                        </div>
                    </div>
                    <div class="children">
                        <?php
                        foreach ($data['children'] as $levelData3):
                            $active_3 = in_array($levelData3['model']->id, $this->params['current_cat_ids']) ? 'active' : '';
                            ?>
                            <div class="level_data_3">
                                <div class="cat_title <?php echo $active_3; ?>">
                                    <a href="<?php echo $levelData3['model']->getUrl(); ?>">
                                        <div class="cat_title_content">
                                            <span><?php echo $levelData3['model']->getSiteTitle(); ?></span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="cat_title <?php echo $active_2; ?>">
                        <a href="<?php echo $data['model']->getUrl(); ?>">
                            <div class="cat_title_content">
                                <span><?php echo $data['model']->getSiteTitle(); ?></span>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div id="line_2_mob" class="line_2_mob">
        <div class="to-back root">
            <div class="">
                <span>Каталог товаров</span>
            </div>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="catalog_menu_close">
                <path d="M15.7071 1.70711C16.0976 1.31658 16.0976 0.683417 15.7071 0.292893C15.3166 -0.0976311 14.6834 -0.0976311 14.2929 0.292893L8 6.58579L1.70711 0.292893C1.31658 -0.0976311 0.683417 -0.0976311 0.292893 0.292893C-0.0976311 0.683417 -0.0976311 1.31658 0.292893 1.70711L6.58579 8L0.292893 14.2929C-0.0976311 14.6834 -0.0976311 15.3166 0.292893 15.7071C0.683417 16.0976 1.31658 16.0976 1.70711 15.7071L8 9.41421L14.2929 15.7071C14.6834 16.0976 15.3166 16.0976 15.7071 15.7071C16.0976 15.3166 16.0976 14.6834 15.7071 14.2929L9.41421 8L15.7071 1.70711Z" fill=""></path>
            </svg>
        </div>
        <?php
        foreach ($mobileMenu as $data):
            $active_2 = in_array($data['model']->id, $this->params['current_cat_ids']) ? 'active' : '';
            ?>
            <div class="level_data_2 js_level_data">
                <?php if (count($data['children']) > 0): ?>
                    <div class="cat_title <?php echo $active_2; ?>">
                        <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""/>
                        </svg>
                        <div class="cat_title_content">
                            <span><img class="cat_icon responsive" src="<?php echo $data['model']->getModelFiles('cover_1')->getSrc('small'); ?>"/></span>
                            <span><?php echo $data['model']->getSiteTitle(); ?></span>
                        </div>
                    </div>
                    <div class="children">
                        <div class="to-back">
                            <div class="to-back-btn">
                                <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""></path>
                                </svg>
                                <span><?php echo $data['model']->getSiteTitle(); ?></span>
                            </div>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="catalog_menu_close">
                                <path d="M15.7071 1.70711C16.0976 1.31658 16.0976 0.683417 15.7071 0.292893C15.3166 -0.0976311 14.6834 -0.0976311 14.2929 0.292893L8 6.58579L1.70711 0.292893C1.31658 -0.0976311 0.683417 -0.0976311 0.292893 0.292893C-0.0976311 0.683417 -0.0976311 1.31658 0.292893 1.70711L6.58579 8L0.292893 14.2929C-0.0976311 14.6834 -0.0976311 15.3166 0.292893 15.7071C0.683417 16.0976 1.31658 16.0976 1.70711 15.7071L8 9.41421L14.2929 15.7071C14.6834 16.0976 15.3166 16.0976 15.7071 15.7071C16.0976 15.3166 16.0976 14.6834 15.7071 14.2929L9.41421 8L15.7071 1.70711Z" fill=""></path>
                            </svg>
                        </div>
                        <?php
                        foreach ($data['children'] as $levelData3):
                            $active_3 = in_array($levelData3['model']->id, $this->params['current_cat_ids']) ? 'active' : '';
                            ?>
                            <div class="level_data_3 js_level_data">
                                <div class="cat_title <?php echo $active_3; ?>">
                                    <?php if (count($levelData3['children']) > 0): ?>
                                        <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""/>
                                        </svg>
                                    <?php endif; ?>
                                    <a href="<?php echo $levelData3['model']->getUrl(); ?>">
                                        <div class="cat_title_content">
                                            <span><img class="cat_icon responsive" src="<?php echo $levelData3['model']->getModelFiles('cover_1')->getSrc('small'); ?>"/></span>
                                            <span><?php echo $levelData3['model']->getSiteTitle(); ?></span>
                                        </div>
                                    </a>
                                </div>
                                <?php if (count($levelData3['children']) > 0): ?>
                                    <div class="children">
                                        <div  class="to-back">
                                            <div class="to-back-btn">
                                                <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.500008 0C0.30382 0 0.12576 0.114736 0.0446916 0.293392C-0.0363767 0.472048 -0.0054713 0.681605 0.12372 0.829252L3.62372 4.82925C3.71866 4.93776 3.85583 5 4.00001 5C4.14419 5 4.28135 4.93776 4.3763 4.82925L7.8763 0.829252C8.00549 0.681605 8.03639 0.472048 7.95532 0.293392C7.87426 0.114736 7.6962 0 7.50001 0H0.500008Z" fill=""></path>
                                                </svg>
                                                <span><?php echo $levelData3['model']->getSiteTitle(); ?></span>
                                            </div>
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" id="catalog_menu_close4" class="catalog_menu_close">
                                                <path d="M15.7071 1.70711C16.0976 1.31658 16.0976 0.683417 15.7071 0.292893C15.3166 -0.0976311 14.6834 -0.0976311 14.2929 0.292893L8 6.58579L1.70711 0.292893C1.31658 -0.0976311 0.683417 -0.0976311 0.292893 0.292893C-0.0976311 0.683417 -0.0976311 1.31658 0.292893 1.70711L6.58579 8L0.292893 14.2929C-0.0976311 14.6834 -0.0976311 15.3166 0.292893 15.7071C0.683417 16.0976 1.31658 16.0976 1.70711 15.7071L8 9.41421L14.2929 15.7071C14.6834 16.0976 15.3166 16.0976 15.7071 15.7071C16.0976 15.3166 16.0976 14.6834 15.7071 14.2929L9.41421 8L15.7071 1.70711Z" fill=""></path>
                                            </svg>
                                        </div>
                                        <?php
                                        foreach ($levelData3['children'] as $levelData4):
                                            $active_4 = in_array($levelData4['model']->id, $this->params['current_cat_ids']) ? 'active' : '';
                                            ?>
                                            <div class="level_data_4">
                                                <a class="cat_title <?php echo $active_4; ?>" href="<?php echo $levelData4['model']->getUrl(); ?>">
                                                    <div class="cat_title_content">
                                                        <span><img class="cat_icon responsive" src="<?php echo $levelData4['model']->getModelFiles('cover_1')->getSrc('small'); ?>"/></span>
                                                        <span><?php echo $levelData4['model']->getSiteTitle(); ?></span>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="cat_title <?php echo $active_2; ?>">
                        <a href="<?php echo $data['model']->getUrl(); ?>">
                            <div class="cat_title_content">
                                <span><img class="cat_icon responsive" src="<?php echo $data['model']->getModelFiles('cover_1')->getSrc('small'); ?>"/></span>
                                <span><?php echo $data['model']->getSiteTitle(); ?></span>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <div class="level_data_2 js_level_data">
            <div class="cat_title">
                <a href="<?php echo Url::to(['/client/catalog/novelty']); ?>">
                    <span class="cat_icon responsive"></span>
                    Новинки (<?php echo DataHelper::getNoveltyQty(); ?>)
                </a>
            </div>
        </div>
        <div class="level_data_2 js_level_data">
            <div class="cat_title">
                <a href="<?php echo Url::to(['/client/catalog/promo']); ?>" class="red">
                    <span class="cat_icon responsive"></span>
                    Outlet
                </a>
            </div>
        </div>
        <div class="level_data_2 js_level_data">
            <div class="cat_title">
                <a href="<?php echo Url::to(['/client/brands/index']); ?>">
                    <span class="cat_icon responsive"></span>
                    Бренди
                </a>
            </div>
        </div>
    </div>

    <div class="dark_fond opac0" id="profile">
        <div class="profile_block">
            <?php echo $this->context->renderPartial('/cart/_upload_button', ['isMenu' => true]); ?>
            <a href="<?php echo Url::to(['/client/preorders/index']); ?>">Мої заявки</a>
            <a href="<?php echo Url::to(['/client/orders/index']); ?>">Мої замовлення</a>
            <a href="<?php echo Url::to(['/client/reports/index']); ?>">Звіти</a>
            <a href="<?php echo Url::to(['/client/downloads/index']); ?>">Завантаження</a>
            <a class="separator" href="<?php echo Url::to(['/client/feedback/index']); ?>">Зворотній зв'язок</a>
            <a href="<?php echo Url::to(['/client/profile/index']); ?>">Профіль</a>
            <a href="<?php echo Url::to(['/client/managers/index']); ?>">Мої співробітники</a>
            <a href="<?php echo Url::to(['/client/addresses/index']); ?>">Адреси доставки</a>
            <a class="exit" href="<?php echo Url::to(['/frontend/site/logout']); ?>">Вихід</a>
            <?php if ($firm->manager_name <> ''): ?>
                <p class="manager">Ваш менеджер:</p>
                <p><?php echo $firm->manager_name; ?></p>
                <?php if ($firm->manager_phone <> ''): ?>
                    <a class="phone" href="tel:<?php echo Misc::phoneFilter($firm->manager_phone); ?>">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 1.49951C0 0.671352 0.671573 0 1.5 0H3.48345C4.21671 0 4.84249 0.529945 4.96304 1.25299L5.23996 2.91395C5.35404 3.59822 4.95319 4.26376 4.29487 4.48313L3.93333 4.6036C3.78228 4.65394 3.69688 4.81346 3.73877 4.96702L4.11501 6.34609C4.32253 7.10677 4.89662 7.71344 5.64487 7.96278L6.56011 8.26776C6.68845 8.31052 6.82955 8.26213 6.90459 8.1496L7.34197 7.49374C7.71719 6.93111 8.42267 6.68913 9.06439 6.90296L10.9743 7.53941C11.5869 7.74351 12 8.31653 12 8.96196V10.5005C12 11.3286 11.3284 12 10.5 12H9.3C4.16375 12 0 7.83762 0 2.70306V1.49951Z" fill=""/>
                        </svg>
                        <?php echo $firm->manager_phone; ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="dark_fond opac0" id="search">
        <div class="search_block">
            <form action="<?php echo Url::to(['/client/catalog/search']); ?>">
                <input type="text" placeholder="Що будемо шукати?" name="q" autocomplete="off">
            </form>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" id="search_close">
                <path d="M15.7071 1.70711C16.0976 1.31658 16.0976 0.683417 15.7071 0.292893C15.3166 -0.0976311 14.6834 -0.0976311 14.2929 0.292893L8 6.58579L1.70711 0.292893C1.31658 -0.0976311 0.683417 -0.0976311 0.292893 0.292893C-0.0976311 0.683417 -0.0976311 1.31658 0.292893 1.70711L6.58579 8L0.292893 14.2929C-0.0976311 14.6834 -0.0976311 15.3166 0.292893 15.7071C0.683417 16.0976 1.31658 16.0976 1.70711 15.7071L8 9.41421L14.2929 15.7071C14.6834 16.0976 15.3166 16.0976 15.7071 15.7071C16.0976 15.3166 16.0976 14.6834 15.7071 14.2929L9.41421 8L15.7071 1.70711Z" fill=""/>
            </svg>
        </div>
    </div>
</header>