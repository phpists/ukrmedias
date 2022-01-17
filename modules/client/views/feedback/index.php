<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\AppActiveForm;

$this->title = 'Зворотній зв`язок';
?>
<div class="table">
    <div class="profile">
        <?php $form = AppActiveForm::begin(); ?>
        <div class="form-row">
            <h3>Тема</h3>
            <?php echo $form->field($model, 'subject_id')->label(false)->dropDownList($model::$subjectLabels, ['prompt' => '']); ?>
            <h3>Повідомлення</h3>
            <?php echo $form->field($model, 'mess')->label(false)->textarea(['class' => '']); ?>
        </div>
        <div class="form-row">
            <button class="btn save" name="action" value="save">Відправити</button>
        </div>
        <?php AppActiveForm::end(); ?>
    </div>
</div>