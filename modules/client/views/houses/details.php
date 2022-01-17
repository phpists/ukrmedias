<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\Icons;
use app\models\Services;
use app\models\DataHelper;
use app\models\Users;

app\components\assets\ReportAssets::register($this);
$this->params['breadcrumbs'] = [
    ['label' => 'Адреси', 'url' => 'index'],
];
$this->title = $house->getTitle();
?>
<div style="margin-bottom:24px;"></div>
<div class="adaptive-column">
    <?php echo $this->context->renderPartial('_flats', ['house' => $house, 'service_id' => $service_id, 'flat_id' => null, 'client' => $client]); ?>
    <div class="column-wide">
        <div class="columns is-multiline columns-botoom">
            <div class="column is-flex-center">
                <div class="my-address w100">
                    <div class="h3">Адреса:</div>
                    <div class="text-dark">
                        <?php echo $house->getTitle(); ?>
                        <?php echo Html::a(Icons::$pencil, ['update', 'id' => $house->id], ['title' => 'Редагувати', 'class' => 'btn btn-inline']); ?>
                    </div>
                    <div class="text-info"><?php echo $house->clientNote; ?></div>
                </div>
            </div>
        </div>
        <?php if (count($abonents) > 0): ?>
            <div class="card card--white card--gap card--auto">
                <?php foreach ($abonents as $dataModel): ?>
                    <div class="card--info">
                        <div class="h4 text-grey2">Абонент</div>
                        <?php if ($dataModel->type_id == Users::TYPE_LEGAL_PERSON): ?>
                            <div class="text-normal-size text-dark"><?php echo $dataModel->getTitle(); ?></div>
                        <?php endif; ?>
                        <div class="text-normal-size text-dark"><?php echo $dataModel->getName(); ?></div>
                        <?php if ($dataModel->email <> ''): ?>
                            <div class="text-normal-size text-dark">
                                <a href="mailto:<?php echo $dataModel->email; ?>"><?php echo $dataModel->email; ?></a>
                            </div>
                        <?php endif; ?>
                        <?php if ($dataModel->phone <> ''): ?>
                            <div class="text-normal-size text-dark">
                                <a href="tel:<?php echo $dataModel->phone; ?>"><?php echo $dataModel->phone; ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php echo $this->context->renderPartial('_service', ['hardware' => $hardware, 'hardwareHouse' => $hardwareHouse, 'service_id' => $service_id, 'entity' => $house, 'client' => $client]); ?>
    </div>
</div>
