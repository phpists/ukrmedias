<?php

use yii\helpers\Url;
use yii\helpers\Html;
?><div class="person">
    <div><?php echo $model->getName(); ?></div>
    <div>
        <?php if ($model->isActive()): ?>
            <picture><source srcset="img/orders/check-green.svg" type="image/webp"/><img src="img/orders/check-green.svg" alt=""></picture>
        <?php else: ?>
            <picture><source srcset="img/orders/check-red.svg" type="image/webp"/><img src="img/orders/check-red.svg" alt=""></picture>
        <?php endif; ?>
        <?php echo $model->getStatus(); ?>
    </div>
    <div class="btns">
        <a href="<?php echo Url::to(['update', 'id' => $model->id]); ?>">
            <picture><source srcset="img/orders/edit.svg" type="image/webp"/><img src="img/orders/edit.svg" alt=""></picture>
            Редагувати
        </a>
        <a href="<?php echo Url::to(['delete', 'id' => $model->id]); ?>" data-confirm-click="Ви підтверджуєте видалення <?php echo Html::encode($model->getName()); ?> ?">
            <picture><source srcset="img/orders/delete.svg" type="image/webp"/><img src="img/orders/delete.svg" alt=""></picture>
            Видалити
        </a>
    </div>
</div>