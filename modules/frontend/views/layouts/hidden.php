<?php

use app\components\assets\FrontendAssets;

$this->beginPage();

FrontendAssets::register($this);
$this->registerCsrfMetaTags();
?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language; ?>">
    <head>
        <base href="<?php echo Yii::$app->request->getBaseUrl(true); ?>/files/frontend/"/>
        <?php $this->head(); ?>
    </head>
    <body>
        <?php $this->beginBody(); ?>
        <?php echo $content; ?>
        <?php $this->endBody(); ?>
    </body>
</html>
<?php
$this->endPage();
