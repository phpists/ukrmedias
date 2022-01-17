<?php

use yii\helpers\Url;
use app\components\AppListView;
use app\models\Services;

$this->title = 'Розрахункові періоди';
$this->params['breadcrumbs'] = [
    ['label' => 'Адреси', 'url' => 'index'],
    $this->title,
];
?>
<div class="main-template-employ main-template-address">
    <div class="h3 main-template__header"><?php echo $house->getTitle(); ?></div>
    <div class="w100 tabs-type__wrapper">
        <?php
        foreach (Services::$labels as $sid => $label):
            if (in_array($sid, $client->houseServices) || in_array($sid, $client->services) && $client->isSupervisor()):
                ?>
                <a href="<?php echo Url::toRoute(['period', 'id' => $house->id, 'aid' => $sid]); ?>" class="tabs__head-item <?php if ($sid == $service_id): ?>is-active service<?php echo $sid; ?><?php endif; ?>">
                    <icon class="tabs__head-item--icon  "><?php echo Services::getIcon($sid); ?>
                    </icon>
                    <span class="tabs__head-item--text"><?php echo $label; ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <div class="main-template-receipts calculations-period">
        <?php
        echo AppListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_item',
            'searchModel' => $model,
            'headerAttrs' => [],
            #'filter' => ['second_name' => 'text', 'active' => Users::$statusLabels],
            'viewParams' => ['house' => $house, 'service_id' => $service_id],
        ]);
        ?>
    </div>
</div>

