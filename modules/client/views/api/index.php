<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;
use app\models\Api;
use app\models\Services;
use app\models\AddrFlats;
use app\models\DataHelper;
use app\models\Devices;

$this->title = 'API';
?>
<div class="h3 main-template__header settings-page"><?php echo $this->title; ?></div>
<div class="">
    <?php $form = AppActiveForm::begin(['errorSummaryModels' => $model]); ?>
    <div class="columns is-multiline">
        <div class="column is-12-mobile is-6-tablet is-6-desktop">
            <div class="form-default__row form-default__row-blue">
                <label class="form-default__label">Ідентифікатор користувача:</label>
                <?php echo Html::textInput(null, $model->getApiId(), ['class' => 'w100 form-default__input', 'readonly' => true]); ?>
                <button id="copy-id" class="copy-api"><img src="img/icon-copy.svg" alt=""></button>
            </div>
        </div>
        <div class="column is-12-mobile is-6-tablet is-6-desktop">
            <div class="form-default__row form-default__row-blue">
                <label class="form-default__label">Приватний ключ:</label>
                <?php echo Html::activeTextInput($model, 'api_key', ['class' => 'w100 form-default__input', 'readonly' => true]); ?>
                <button id="copy-api" class="copy-api"><img src="img/icon-copy.svg" alt=""></button>
                <button id="create-key" class="create-key" data-url="<?php echo Url::toRoute('api-key'); ?>">новий ключ</button>
            </div>
        </div>
    </div>
    <div class="calc-buttons-group">
        <div class="">
            <button class="btn btn-profile btn-blue" aria-haspopup="true" name="action" value="save"><svg class="icon icon-check"><use xlink:href="img/sprite.svg#icon-check"></use></svg> Зберегти</button>
        </div>
    </div>
    <?php AppActiveForm::end(); ?>
    <?php echo $this->render('doc'); ?>
</div>
