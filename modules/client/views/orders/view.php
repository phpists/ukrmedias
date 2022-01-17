<?php

use yii\helpers\Url;
use app\models\Orders;

$this->title = "Замовлення № {$model->getNumber()} від {$model->getDate()}";
$this->params['breadcrumbs'] = [
    ['label' => 'Замовлення', 'url' => ['index']],
];
?>
<div class="table">
    <div class="order_number">
        <p>Замовлення <?php echo $model->getNumber(); ?></p>
        <div>
            <?php echo $model->getStatusIcon(); ?>
            <?php echo $model->getStatus(); ?>
        </div>
    </div>
    <?php foreach ($model->getDetails() as $dataModel): ?>
        <div class="info_item">
            <div class="img">
                <?php
                $photoModel = $dataModel->getGoods() !== null ? $dataModel->getGoods()->getMainPhoto() : null;
                if ($photoModel === null):
                    ?>
                    <img src="/files/images/system/empty.png" alt="">
                    <?php
                else:
                    $src = $photoModel->getSrc('small');
                    ?>
                    <picture><source srcset="<?php echo $src; ?>" type="image/webp"><img src="<?php echo $src; ?>" alt=""></picture>
                <?php endif; ?>
            </div>
            <div class="text">
                <div><?php echo $dataModel->getAdminTitle(); ?></div>
                <p><?php echo $dataModel->article; ?> / <?php echo $dataModel->code; ?></p>
            </div>
            <div class="quantity">
                <p>Кількість</p>
                <p><?php echo $dataModel->qty; ?></p>
            </div>
            <div class="price">
                <p>Ціна</p>
                <p><?php echo $dataModel->price; ?></p>
            </div>
            <div class="sum">
                <p>Сума</p>
                <p><?php echo $dataModel->getAmount(); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="waybill_total">
        <div class="col">
            <div class="row">
                <p>Разом</p>
                <p><?php echo $model->amount; ?> ₴</p>
            </div>
            <div class="row">
                <p>Оплата</p>
                <p><?php echo $model->payment_title; ?></p>
            </div>
            <div class="row">
                <p>Доставка</p>
                <p><?php echo $model->delivery_title; ?></p>
            </div>
        </div>
        <div class="col">
            <div class="row">
                <p>Область</p>
                <p><?php echo $model->region; ?></p>
            </div>
            <div class="row">
                <p>Населений пункт</p>
                <p><?php echo $model->city; ?></p>
            </div>
            <div class="row">
                <p>Адреса</p>
                <p><?php echo $model->address; ?></p>
            </div>
        </div>
        <div class="col">
            <div class="row">
                <p>Вантажоодержувач</p>
                <p><?php echo $model->consignee; ?></p>
            </div>
            <div class="row">
                <p>Контактна особа</p>
                <p><?php echo $model->name; ?></p>
            </div>
            <div class="row">
                <p>Телефон</p>
                <p><?php echo $model->phone; ?></p>
            </div>
            <?php if ($model->client_note <> ''): ?>
                <div class="row">
                    <p>Коментар замовника</p>
                    <p><?php echo $model->getClientNote(); ?></p>
                </div>
            <?php endif; ?>
            <?php if ($model->manager_note <> ''): ?>
                <div class="row">
                    <p>Коментар постачальника</p>
                    <p><?php echo $model->getManagerNote(); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($model->isAllowDownload()): ?>
            <a href="<?php echo Url::to(['xls', 'id' => $model->id]); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 2C0 0.89543 0.895431 0 2 0H22C23.1046 0 24 0.895431 24 2V22C24 23.1046 23.1046 24 22 24H2C0.89543 24 0 23.1046 0 22V2Z" fill="white"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 17C11.7348 17 11.4804 16.8946 11.2929 16.7071L8.29289 13.7071C7.90237 13.3166 7.90237 12.6834 8.29289 12.2929C8.68342 11.9024 9.31658 11.9024 9.70711 12.2929L11 13.5858L11 10C11 9.44771 11.4477 9 12 9C12.5523 9 13 9.44771 13 10L13 13.5858L14.2929 12.2929C14.6834 11.9024 15.3166 11.9024 15.7071 12.2929C16.0976 12.6834 16.0976 13.3166 15.7071 13.7071L12.7071 16.7071C12.5196 16.8946 12.2652 17 12 17Z" fill=""/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6 3C5.44772 3 5 3.44772 5 4V20C5 20.5523 5.44772 21 6 21H18C18.5523 21 19 20.5523 19 20V8.41421C19 8.149 18.8946 7.89464 18.7071 7.70711L14.2929 3.29289C14.1054 3.10536 13.851 3 13.5858 3H6ZM7.5 5C7.22386 5 7 5.22386 7 5.5V18.5C7 18.7761 7.22386 19 7.5 19H16.5C16.7761 19 17 18.7761 17 18.5V9.20711C17 9.0745 16.9473 8.94732 16.8536 8.85355L13.1464 5.14645C13.0527 5.05268 12.9255 5 12.7929 5H7.5Z" fill=""/>
                </svg>
                Отримати Excel файл
            </a>
        <?php endif; ?>
    </div>
    <div class="add_order">
        <h2>Повторити замовлення</h2>
        <div>
            <h6>Доповніть кошик товарами з цього замовлення.</h6>
            <p>До існуючих товарів у кошику додадуться артикули з замовлення.</p>
            <a href="<?php echo Url::to(['repeate', 'id' => $model->id]); ?>">Доповнити</a>
        </div>
        <div>
            <h6>Заповніть кошик товарами з цього замовлення.</h6>
            <p>Кошик буде очищено. До кошику додадуться артикули з замовлення.</p>
            <a href="<?php echo Url::to(['repeate', 'id' => $model->id, 'clean' => 1]); ?>">Заповнити</a>
        </div>
    </div>
</div>
