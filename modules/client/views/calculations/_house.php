<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Services;
use app\models\DataHelper;
?>
<div class="inform-table-list">
    <div class="inform-table__item">
        <div class="address-wrap">
            <div class="name-point"><?php echo $model->search_city; ?></div>
            <div class="name-street"><?php echo $model->search_street; ?></div>
            <div class="home-number"><?php echo $model->no; ?></div>
        </div>
        <div class="action-wrap">
            <div class="comment"><?php echo $model->getClientNote(); ?></div>
            <div class="actions">
                <?php if (count($services) > 0): ?>
                    <a href="<?php echo Url::toRoute(['details', 'id' => $date, 'aid' => $model->id]); ?>" class="btn btn-m-size" data-pjax="0">Параметри</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
