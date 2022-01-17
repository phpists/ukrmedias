<?php
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = "Помилка {$code}";
?>
<div class="table">
    <div class="profile">
        <h1><?php echo $this->title; ?></h1>
        <div class="alert-error">
            <?php echo nl2br(Html::encode($code === 404 ? 'Сторінка не знайдена.' : $message)); ?>
        </div>
    </div>
</div>
