<?php

use app\components\assets\FrontendAssets;
use app\components\Misc;
use yii\helpers\Url;

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
        <title><?php echo $this->title; ?></title>
        <?php $this->head(); ?>
    </head>
    <body>
        <?php
        $this->beginBody();
        echo Misc::TestVersionHeader();
        ?>
        <?php echo app\components\Misc::getFlashMessages(); ?>
        <div class="login">
            <div>
                <a class="picture" href="<?php echo Url::to(['/frontend/site/index']); ?>"><picture><source srcset="img/login/ukrmedias.svg" type="image/webp"><img src="img/login/ukrmedias.svg" alt=""></picture></a>
            </div>
            <div>
                <?php echo $content; ?>
            </div>
        </div>
        <?php $this->endBody(); ?>
    </body>
</html>
<?php
$this->endPage();
