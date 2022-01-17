<?php

use yii\helpers\Url;
use app\models\Services;
use app\models\DataHelper;
use app\models\AddrHouses;
use app\models\AddrFlats;

if ($service_id == Services::ID_HEAT && $entity instanceof AddrFlats):
    $hwTitle = 'Вузол розподілу';
else:
    $hwTitle = 'Вузол обліку';
endif;
?>
<div class="w100 tabs-type__wrapper">
    <?php
    foreach (Services::$labels as $sid => $label):
        if (in_array($sid, $client->houseServices) || in_array($sid, $client->services) && $client->isSupervisor()):
            ?>
            <a href="<?php echo Url::toRoute(['details', 'id' => $entity->id, 'aid' => $sid]); ?>" class="tabs__head-item <?php if ($sid == $service_id): ?>is-active service<?php echo $sid; ?><?php endif; ?>">
                <icon class="tabs__head-item--icon  "><?php echo Services::getIcon($sid); ?>
                </icon>
                <span class="tabs__head-item--text"><?php echo $label; ?></span>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<div class="hardware-cards" id="accordion_card_accounting">
    <div class="h4 title-middle"><?php echo $hwTitle; ?></div>
    <?php
    $counters = [];
    foreach ($hardwareHouse as $hwKey => $dataModel):
        if (!$dataModel->device->isNewRecord) {
            $counters[$hwKey] = $dataModel->getLabel();
        }
        echo $this->context->renderPartial('/houses/_hardware', ['hwModel' => $dataModel, 'service_id' => $service_id]);
    endforeach;
    foreach ($hardware as $hwKey => $dataModel):
        if (!$dataModel->device->isNewRecord) {
            $counters[$hwKey] = $dataModel->getLabel();
        }
        echo $this->context->renderPartial('/houses/_hardware', ['hwModel' => $dataModel, 'service_id' => $service_id]);
    endforeach;
    ?>
</div>
<?php
if (count($counters) > 0):
    echo $this->context->renderPartial('/houses/_report_control', [
        'filter' => [
            DataHelper::GROUP_HOURS => 'Журнал',
            DataHelper::GROUP_DAYS => 'Дні',
            DataHelper::GROUP_MONTH => 'Місяці',
            DataHelper::GROUP_RAW => 'Період',
        ],
        'downloadUrl' => Url::toRoute(['download', 'id' => $entity->id, 'aid' => $service_id]),
        'reportUrl' => Url::toRoute(['report', 'id' => $entity->id, 'aid' => $service_id]),
        'excelOnly' => false,
        'htmlReport' => true,
        'hardware' => $counters,
    ]);
else:
    echo "<div>{$hwTitle} відсутній</div>";
endif;