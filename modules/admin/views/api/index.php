<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    $this->title = 'Документація по API',
];
?>
<a class="btn btn-primary btn-sm float-right" href="<?php echo Url::to(['download']); ?>">скачать</a>
<br/>
<br/>
<div class="">
    <style><?php echo file_get_contents(__DIR__ . '/doc.css'); ?></style>
    <?php
    echo $this->renderFile("@app/modules/client/views/api/doc.php", [
    ]);
    ?>
</div>
<br/>
<a class="btn btn-primary btn-sm float-right" href="<?php echo Url::to(['download']); ?>">скачать</a>
<br/>
<br/>
