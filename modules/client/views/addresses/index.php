<?php

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Адреси доставки';
$firm = Yii::$app->user->identity->getFirm();
?>
<div class="table">
    <div class="add_employee">
        <a href="<?php echo Url::to(['update']); ?>">
            <svg width="17" height="20" viewBox="0 0 17 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M14 3.42152e-09C14.5523 3.42152e-09 15 0.447715 15 1V2H16C16.5523 2 17 2.44772 17 3C17 3.55228 16.5523 4 16 4H15V5C15 5.55228 14.5523 6 14 6C13.4477 6 13 5.55228 13 5V4H12C11.4477 4 11 3.55228 11 3C11 2.44772 11.4477 2 12 2H13V1C13 0.447715 13.4477 3.42152e-09 14 3.42152e-09Z" fill="#"/>
                <path d="M8.65079 1.75927C9.07012 1.39985 9.11868 0.768553 8.75926 0.349227C8.39983 -0.0700991 7.76853 -0.118661 7.34921 0.240762L1.04763 5.64212C0.382688 6.21206 0 7.04411 0 7.91989V17C0 18.6569 1.34315 20 3 20H13C14.6569 20 16 18.6569 16 17V9.00002C16 8.44773 15.5523 8.00002 15 8.00002C14.4477 8.00002 14 8.44773 14 9.00002V17C14 17.5523 13.5523 18 13 18H10V15C10 14.4477 9.55229 14 9 14H7C6.44771 14 6 14.4477 6 15V18L3 18C2.44772 18 2 17.5523 2 17V7.91989C2 7.62796 2.12756 7.35061 2.34921 7.16063L8.65079 1.75927Z" fill="#"/>
            </svg>
            Додати адресу
        </a>
    </div>
    <div class="addresses">
        <?php foreach ($list as $dataModel): ?>
            <div class="address">
                <div>
                    <?php if ($firm->default_addr_id === $dataModel->id): ?>
                        <picture><source srcset="img/orders/check-red.svg" type="image/webp"/><img src="img/orders/check-red.svg" alt=""></picture>
                    <?php endif; ?>
                    <?php echo $dataModel->getSummary(); ?>
                </div>
                <div class="btns">
                    <a href="<?php echo Url::to(['update', 'id' => $dataModel->id]); ?>">
                        <picture><source srcset="img/orders/edit.svg" type="image/webp"/><img src="img/orders/edit.svg" alt=""></picture>
                        Редагувати
                    </a>
                    <a href="<?php echo Url::to(['delete', 'id' => $dataModel->id]); ?>" data-confirm-click="Ви підтверджуєте видалення адреси<br/><?php echo Html::encode($dataModel->getSummary()); ?> ?">
                        <picture><source srcset="img/orders/delete-address.svg" type="image/webp"/><img src="img/orders/delete-address.svg" alt=""></picture>
                        Видалити
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>