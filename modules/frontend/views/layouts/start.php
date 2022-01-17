<?php

use app\components\assets\FrontendAssets;
use app\components\Misc;

$this->beginPage();

FrontendAssets::register($this);
$this->registerCsrfMetaTags();
?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language; ?>">
    <head>
        <base href="<?php echo Yii::$app->request->getBaseUrl(true); ?>/files/frontend/"/>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Commissioner:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <title><?php echo $this->title; ?></title>
        <?php $this->head(); ?>
    </head>
    <body>
        <?php
        $this->beginBody();
        echo Misc::TestVersionHeader();
        ?>
        <div class="login">
            <div>
                <picture><source srcset="img/login/ukrmedias.svg" type="image/webp"><img src="img/login/ukrmedias.svg" alt=""></picture>
            </div>
            <div>
                <?php //echo app\components\Misc::getFlashMessages(); ?>
                <?php //echo $content; ?>
                <form action="">
                    <h6>Вхід</h6>
                    <p class="p1">Для роботи з порталом Ukrmedias необхідна авторизація.</p>
                    <p class="p2">Введіть номер телефону</p>
                    <input type="text" placeholder="+38 (_ _ _) _ _ _ _ _ _ _">
                    <p class="p3">Пароль для входу буде відправлено на вказаний номер телефону.</p>
                    <input type="submit" value="Отримати пароль" class="bgg">
                    <div>
                        Не зареєстровані?
                        <a href="#">Створіть обіковий запис</a>
                    </div>
                </form>
            </div>
        </div>

        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="js/script.min.js"></script>
        <?php $this->endBody(); ?>
    </body>
</html>
<?php
$this->endPage();
