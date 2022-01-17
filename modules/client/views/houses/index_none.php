<?php

use yii\helpers\Url;

$this->title = 'Немає доданих адрес';
?>
<div class="main-template-employ main-template-employ-null">
    <div class="flex-title-block">
        <div class="h3 main-template__header"><?php echo $this->title; ?></div>
        <a href="<?php echo Url::toRoute('update'); ?>" class="btn-add-white">
            <div class="icon">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.33333 0C6.96514 0 6.66667 0.298477 6.66667 0.666667V6.66667H0.666667C0.298477 6.66667 0 6.96514 0 7.33333V8.66667C0 9.03486 0.298477 9.33333 0.666667 9.33333H6.66667V15.3333C6.66667 15.7015 6.96514 16 7.33333 16H8.66667C9.03486 16 9.33333 15.7015 9.33333 15.3333V9.33333H15.3333C15.7015 9.33333 16 9.03486 16 8.66667V7.33333C16 6.96514 15.7015 6.66667 15.3333 6.66667H9.33333V0.666667C9.33333 0.298477 9.03486 0 8.66667 0H7.33333Z" fill="#918DA6" /></svg>
            </div>
            <span>Додати адресу</span>
        </a>
    </div>
    <div class="img-not-info">
        <div class="img"><img src="img/img-addresses.png" alt=""></div>
    </div>
</div>