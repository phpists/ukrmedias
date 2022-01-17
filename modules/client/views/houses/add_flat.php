<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;
use app\models\AddrFlats;
use app\models\Users;

$this->params['breadcrumbs'] = [
    ['label' => 'Адреси', 'url' => '/client/houses/index'],
    ['label' => "Будинок {$house->no}", 'url' => ['/client/houses/details', 'id' => $house->id, 'aid' => $service_id]],
];
$this->title = 'Нове приміщення';
?>

<?php $form = AppActiveForm::begin(['errorSummaryModels' => $house]); ?>
<div class="main-template-employ">
    <div class="h3 main-template__header">Приміщення за адресою:</div>
    <div class="m24"><?php echo $house->getTitle(); ?></div>
    <div class="feedback__wrap ">
        <div class="form-default__row">
            <?php echo $form->autocomplete($house, 'flat_id', $flats, ['class' => 'js-dropdown-select2 form-default__select']); ?>
        </div>
        <div class="calc-buttons-group">
            <div class="">
                <a href="<?php echo Url::toRoute(['/client/houses/details', 'id' => $house->id, 'aid' => $service_id]); ?>" class="close btn btn-profile bg-white">&larr; Назад</a>
            </div>
            <div class="">
                <button class="btn btn-profile btn-blue" aria-haspopup="true" name="action" value="save"><svg class="icon icon-check"><use xlink:href="img/sprite.svg#icon-check"></use></svg> Зберегти</button>
            </div>
        </div>
    </div>
</div>
<?php AppActiveForm::end(); ?>
