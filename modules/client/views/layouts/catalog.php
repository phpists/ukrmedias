<?php

use yii\helpers\Url;
use app\components\assets\CatalogAssets;
use app\components\Misc;
use app\models\Goods;

$this->beginPage();

CatalogAssets::register($this);
$this->registerCsrfMetaTags();
$route = $this->context->getRoute();
$firm = Yii::$app->user->identity->getFirm();
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
        <?php echo $this->context->renderFile('@module/views/layouts/header_small.php', ['firm' => $firm]); ?>
        <?php echo $content; ?>
        <a href="#header" class="btn_up dn" id="btn_up">
            <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M6 0C6.26522 0 6.51957 0.105357 6.70711 0.292893L11.7071 5.29289C12.0976 5.68342 12.0976 6.31658 11.7071 6.70711C11.3166 7.09763 10.6834 7.09763 10.2929 6.70711L6 2.41421L1.70711 6.70711C1.31658 7.09763 0.683417 7.09763 0.292893 6.70711C-0.0976311 6.31658 -0.0976311 5.68342 0.292893 5.29289L5.29289 0.292893C5.48043 0.105357 5.73478 0 6 0Z" fill=""/>
            </svg>
        </a>
        <?php $this->endBody(); ?>
    </body>
</html>
<?php $this->endPage(); ?>