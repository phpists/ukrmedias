<?php
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = "Помилка {$code}";
?>
<div class="">
    <br/>
    <h4><?php echo $this->title; ?></h4>
    <div class="">
        <?php echo $code === 404 ? 'Сторінка не знайдена.' : nl2br($message); ?>
    </div>
</div>