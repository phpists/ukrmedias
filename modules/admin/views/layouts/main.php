<?php
$this->beginPage();

use app\components\assets\AdminAssets;
use app\components\Misc;
use yii\helpers\Url;
use app\models\AccessLogic;

AdminAssets::register($this);
$this->registerCsrfMetaTags();
$route = $this->context->getRoute();
?><!DOCTYPE html>
<html lang="<?php echo Yii::$app->language ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?php echo $this->title; ?></title>
        <?php $this->head() ?>
    </head>
    <body class="d-flex flex-column min-vh-100">
        <?php
        $this->beginBody();
        echo Misc::TestVersionHeader();
        ?>
        <nav class="navbar navbar-expand navbar-dark bg-primary">
            <?php if (!Yii::$app->user->isGuest): ?>
                <span id="menu-toggle" class="navbar-brand"><span class="navbar-toggler-icon"></span></span>
                <div class="collapse navbar-collapse justify-content-end">
                    <ul class="navbar-nav">
                        <?php if (AccessLogic::isLoginAs()): ?>
                            <li class="nav-item"><a class="nav-link" href="<?php echo Url::toRoute('/frontend/site/logout') ?>"><?php echo AccessLogic::get('name'); ?> &rarr;</a></li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo Url::toRoute('/admin/profile/update') ?>">
                                <i class="fa fa-user"></i> <?php echo Yii::$app->user->identity->getName(); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </nav>
        <main class="d-flex flex-grow-1">
            <div id="wrapper" class="d-flex opened w-100">
                <!-- Sidebar -->
                <div id="sidebar-wrapper">
                    <div class="sidebar-nav">
                        <?php if (Yii::$app->user->can(\app\components\Auth::ROLE_MANAGER)): ?>
                            <?php
                            if (preg_match('@^admin/(category|goods|brands|params|prices)@', $route)) {
                                $parentClass = '';
                                $childClass = 'show';
                                $aria = 'true';
                            } else {
                                $parentClass = 'collapsed';
                                $childClass = '';
                                $aria = 'false';
                            }
                            ?>
                            <div class="title <?php echo $parentClass; ?>" data-toggle="collapse" data-target="#menu-catalog" aria-expanded="<?php echo $aria; ?>">Каталог <i class="fa fa-chevron-right float-right"></i></div>
                            <div id="menu-catalog" class="items collapse <?php echo $childClass; ?>">
                                <div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/brands/index'); ?>">
                                            <?php if (strpos($route, 'admin/brands/index') === false): ?>
                                                Торгові марки
                                            <?php else: ?>
                                                <span>Торгові марки</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/category/index'); ?>">
                                            <?php if (strpos($route, 'admin/category/') === false): ?>
                                                Категорії
                                            <?php else: ?>
                                                <span>Категорії</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/goods/index'); ?>">
                                            <?php if (strpos($route, 'admin/goods/index') === false): ?>
                                                Товари
                                            <?php else: ?>
                                                <span>Товари</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/params/index'); ?>">
                                            <?php if (strpos($route, 'admin/params/index') === false): ?>
                                                Параметри
                                            <?php else: ?>
                                                <span>Параметри</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/prices/index'); ?>">
                                            <?php if (strpos($route, 'admin/prices/') === false): ?>
                                                Цінові групи
                                            <?php else: ?>
                                                <span>Цінові групи</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if (preg_match('@^admin/(managers|users)@', $route)) {
                                $parentClass = '';
                                $childClass = 'show';
                                $aria = 'true';
                            } else {
                                $parentClass = 'collapsed';
                                $childClass = '';
                                $aria = 'false';
                            }
                            ?>
                            <div class="title <?php echo $parentClass; ?>" data-toggle="collapse" data-target="#menu-users" aria-expanded="<?php echo $aria; ?>">Користувачі <i class="fa fa-chevron-right float-right"></i></div>
                            <div id="menu-users" class="items collapse <?php echo $childClass; ?>">
                                <div>
                                    <?php if (Yii::$app->user->can(\app\components\Auth::ROLE_ADMIN)): ?>
                                        <div class="item">
                                            <a href="<?php echo Url::toRoute('/admin/managers/index'); ?>">
                                                <?php if (strpos($route, 'admin/managers/') === false): ?>
                                                    Співробітники
                                                <?php else: ?>
                                                    <span>Співробітники</span>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/users/index'); ?>">
                                            <?php if (strpos($route, 'admin/users/index') === false): ?>
                                                Користувачі клієнтів
                                            <?php else: ?>
                                                <span>Користувачі клієнтів</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if (preg_match('@^admin/(firms|preorders|orders|feedback|settings)@', $route)) {
                                $parentClass = '';
                                $childClass = 'show';
                                $aria = 'true';
                            } else {
                                $parentClass = 'collapsed';
                                $childClass = '';
                                $aria = 'false';
                            }
                            ?>
                            <div class="title <?php echo $parentClass; ?>" data-toggle="collapse" data-target="#menu-devices" aria-expanded="<?php echo $aria; ?>">Система заказов <i class="fa fa-chevron-right float-right"></i></div>
                            <div id="menu-devices" class="items collapse <?php echo $childClass; ?>">
                                <div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/preorders/index'); ?>">
                                            <?php if (strpos($route, 'admin/preorders/') === false): ?>
                                                Заявки
                                            <?php else: ?>
                                                <span>Заявки</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/orders/index'); ?>">
                                            <?php if (strpos($route, 'admin/orders/') === false): ?>
                                                Замовлення
                                            <?php else: ?>
                                                <span>Замовлення</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/feedback/index'); ?>">
                                            <?php if (strpos($route, 'admin/feedback/') === false): ?>
                                                Звернення
                                            <?php else: ?>
                                                <span>Звернення</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/firms/index'); ?>">
                                            <?php if (strpos($route, 'admin/firms/') === false): ?>
                                                Клієнти
                                            <?php else: ?>
                                                <span>Клієнти</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </div>

                                <div class="item">
                                    <a href="<?php echo Url::toRoute('/admin/settings/index'); ?>">
                                        <?php if (strpos($route, 'admin/settings/') === false): ?>
                                            Налаштування
                                        <?php else: ?>
                                            <span>Налаштування</span>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            </div>
                            <?php
                            if (preg_match('@^admin/(promo|currency|delivery-variants|payment-variants|sliders|downloads)@', $route)) {
                                $parentClass = '';
                                $childClass = 'show';
                                $aria = 'true';
                            } else {
                                $parentClass = 'collapsed';
                                $childClass = '';
                                $aria = 'false';
                            }
                            ?>
                            <div class="title <?php echo $parentClass; ?>" data-toggle="collapse" data-target="#menu-system" aria-expanded="<?php echo $aria; ?>">Різне <i class="fa fa-chevron-right float-right"></i></div>
                            <div id="menu-system" class="items collapse <?php echo $childClass; ?>">
                                <div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/promo/index'); ?>">
                                            <?php if (strpos($route, 'admin/promo/') === false): ?>
                                                Акції
                                            <?php else: ?>
                                                <span>Акції</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/currency/index'); ?>">
                                            <?php if (strpos($route, 'admin/currency/') === false): ?>
                                                Курси валют
                                            <?php else: ?>
                                                <span>Курси валют</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/delivery-variants/index'); ?>">
                                            <?php if (strpos($route, 'admin/delivery-variants/') === false): ?>
                                                Варіанти доставки
                                            <?php else: ?>
                                                <span>Варіанти доставки</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/payment-variants/index'); ?>">
                                            <?php if (strpos($route, 'admin/payment-variants/') === false): ?>
                                                Варіанти оплати
                                            <?php else: ?>
                                                <span>Варіанти оплати</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/sliders/index'); ?>">
                                            <?php if (strpos($route, 'admin/sliders/') === false): ?>
                                                Слайдери
                                            <?php else: ?>
                                                <span>Слайдери</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a href="<?php echo Url::toRoute('/admin/downloads/index'); ?>">
                                            <?php if (strpos($route, 'admin/downloads/') === false): ?>
                                                Завантаження
                                            <?php else: ?>
                                                <span>Завантаження</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!Yii::$app->user->isGuest): ?>
                            <div class="title-item">
                                <a href="<?php echo Url::toRoute('/frontend/site/logout'); ?>">Вихід</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div> <!-- /#sidebar-wrapper -->
                <!-- Page Content -->
                <div id="page-content-wrapper" class="">
                    <div class="container">
                        <?php
                        echo yii\widgets\Breadcrumbs::widget([
                            'itemTemplate' => '<li class="breadcrumb-item">{link}</li>',
                            'activeItemTemplate' => '<li class="breadcrumb-item active">{link}</li>',
                            'options' => ['class' => 'breadcrumb p-1'],
                            'homeLink' => false,
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        ]);
                        ?>
                        <?php echo app\components\Misc::getFlashMessages(); ?>
                        <?php echo $content; ?>
                    </div>
                    <hr/>
                </div> <!-- /#page-content-wrapper -->
            </div> <!-- /#wrapper -->
        </main>
        <footer class="navbar navbar-dark bg-secondary">
            <span class="navbar-text p-0">&copy; <?php echo date('Y'); ?> <?php echo Yii::$app->name; ?></span>
        </footer>
        <div id="delete-confirm" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Увага!</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Ви підтверджуєте видалення цього запису?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger confirm" data-dismiss="modal">Так</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Ні</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="alert-dialog" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">До відому:</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>