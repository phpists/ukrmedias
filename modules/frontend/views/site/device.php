<?php

use app\components\assets\LoginAssets;
use app\components\AppActiveForm;
use yii\web\View;
LoginAssets::register($this);
$form = AppActiveForm::begin(['options' => ['class' => 'js_autosubmit']]);
?>
<input type="submit" style="display:none;"/>
<?php AppActiveForm::end(); ?>