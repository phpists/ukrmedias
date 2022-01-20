<?php

use yii\helpers\Url;
use app\models\Orders;

$this->title = "Заявк № {$model->getNumber()} від {$model->getDate()}";
$this->params['breadcrumbs'] = [
    ['label' => 'Заявки', 'url' => ['index']],
];
?>
<?php if (Yii::$app->session->hasFlash('order-success')): ?>
    <div class="dark_fond opac1" id="confirm_window">
        <div class="confirm_window">
            <div class="basket">
                <picture><source srcset="img/delivery/basket-green.svg" type="image/webp"><img src="img/delivery/basket-green.svg" alt=""></picture>
            </div>
            <h3>Заявка прийнята</h3>
            <p>Менеджер зв’яжеться з вами найближчим часом.</p>
            <a href="<?php echo Url::to(['/client/profile/dashboard']); ?>">Перейти до каталогу</a>
            <a href="<?php echo Url::to(['/client/preorders/index']); ?>">Мої замовлення</a>
            <a href="<?php echo Url::to(['/client/orders/index']); ?>">Мої накладні</a>
        </div>
    </div>
<?php endif; ?>

<div class="table">
    <div class="order_number">
        <p>Заявка <?php echo $model->getNumber(); ?></p>
        <div>
            <?php echo $model->getStatusIcon(); ?>
            <?php echo $model->getStatus(); ?>
        </div>
    </div>
    <?php foreach ($model->getDetails() as $dataModel): ?>
        <div class="info_item">
            <div class="img">
                <?php
                $goodsModel = $dataModel->getGoods();
                $photoModel = $goodsModel ? $goodsModel->getMainPhoto() : null;
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
        </div>
    </div>
    <div class="add_order">
        <h2>Повторити заявку</h2>
        <div>
            <h6>Доповніть кошик товарами з цієї заявки.</h6>
            <p>До існуючих товарів у кошику додадуться артикули з заявки.</p>
            <a href="<?php echo Url::to(['repeate', 'id' => $model->id]); ?>">Доповнити</a>
        </div>
        <div>
            <h6>Заповніть кошик товарами з цієї заявки.</h6>
            <p>Кошик буде очищено. До кошику додадуться артикули з заявки.</p>
            <a href="<?php echo Url::to(['repeate', 'id' => $model->id, 'clean' => 1]); ?>">Заповнити</a>
        </div>
    </div>
</div>
