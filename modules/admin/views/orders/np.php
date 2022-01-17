<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActiveForm;
use app\models\novaposhta\NP_ExpressDoc;
use app\models\PaymentVariants;
use app\models\DeliveryVariants;

$this->params['breadcrumbs'] = [
    ['label' => 'Замовлення', 'url' => 'index'],
    $this->title = "Замовлення № {$order->getNumber()}",
];
?>

<?php
$form = AdminActiveForm::begin([
            'fieldConfig' => [
                'template' => '{input}',
            ]
        ]);
echo $form->errorSummary($model, ['showAllErrors' => true]);
echo $form->field($model, 'Ref')->hiddenInput();
?>
<div class="row">
    <div class="col-md-12 col-lg-6" style="border-right:1px solid lightgray;padding-right:15px;">
        <h5>Інформація про відправника</h5>
        <div class="row">
            <div class="col-md-12 col-lg-4">Відправник</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'Sender')->dropDownList($senders, ['onChange' => 'senderContacts(this,"#np_expressdoc-contactsender")']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Місто відправника</div>
            <div class="col-md-12 col-lg-8">
                <?php echo Html::textInput('', $citySenderTitle, ['class' => 'form-control', 'disabled' => true]); ?>
                <?php echo $form->field($model, 'CitySender')->hiddenInput(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Відділення</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'SenderOffice')->dropDownList($offices); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Контакт</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'ContactSender')->dropDownList($contacts, ['onChange' => 'senderPhone(this,"#np_expressdoc-sendersphone")']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Телефон</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'SendersPhone')->input('text', ['readonly' => true]); ?>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-6">
        <h5>Інформація про отримувача</h5>
        <div class="row">
            <div class="col-md-12 col-lg-4">Отримувач</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'RecipientType')->dropDownList($types, ['disabled' => true]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Місто отримувача</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->autocomplete($model, 'CityRecipient', $cities, ['disabled' => true]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Відділення</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->autocomplete($model, 'RecipientAddressName', $officesR, ['disabled' => true]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Контакт</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'RecipientName')->input('text'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Телефон</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'RecipientsPhone')->input('text'); ?>
            </div>
        </div>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-12 col-lg-6" style="border-right:1px solid lightgray;padding-right:15px;">
        <h5>Загальна інформація</h5>
        <div class="row">
            <div class="col-md-12 col-lg-4">Дата накладної</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->date($model, 'DateTime', ['class' => 'form-control'])->label('&nbsp;'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Форма оплати</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'PaymentMethod')->dropDownList($paymentTypes); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Тип платника</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'PayerType')->dropDownList($payerTypes); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Тип доставки</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'ServiceType')->dropDownList($deliveryTypes); ?>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-6">
        <h5>Інформація про відправлення</h5>
        <div class="row">
            <div class="col-md-12 col-lg-4">Тип вантажу</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'CargoType')->dropDownList($cargoTypes); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Вага</div>
            <div class="col-md-12 col-lg-2">
                <?php echo $form->field($model, 'Weight')->input('text'); ?>
            </div>
            <div class="col-md-12 col-lg-3">Об'ємна вага</div>
            <div class="col-md-12 col-lg-3">
                <?php echo $form->field($model, 'VolumeGeneral')->input('text'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Габарити, мм</div>
            <div class="col-md-12 col-lg-2">
                <?php echo $form->field($model, 'volumetricLength')->input('text', ['placeholder' => 'Довжина']); ?>
            </div>
            <div class="col-md-12 col-lg-2">
                <?php echo $form->field($model, 'volumetricWidth')->input('text', ['placeholder' => 'Ширина']); ?>
            </div>
            <div class="col-md-12 col-lg-2">
                <?php echo $form->field($model, 'volumetricHeight')->input('text', ['placeholder' => 'Висота']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Кількість місць</div>
            <div class="col-md-12 col-lg-2">
                <?php echo $form->field($model, 'SeatsAmount')->input('text'); ?>
            </div>
            <div class="col-md-12 col-lg-3">Оцін. вартість</div>
            <div class="col-md-12 col-lg-3">
                <?php echo $form->field($model, 'Cost')->input('text'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Без коробки</div>
            <div class="col-md-12 col-lg-8">
                <?php
                echo $form->field($model, 'specialCargo')->checkbox([
                    'checked' => false,
                    'label' => '<span class="badge border badge-pill col"><i class="fa fa-check"></i></span>',
                ]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4">Опис вантажу</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'Description')->dropDownList($cargoDescr); ?>
            </div>
        </div>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-12 col-lg-6" style="border-right:1px solid lightgray;padding-right:15px;">
        <h5>Додаткові послуги</h5>
        <div class="row">
            <div class="col-md-12 col-lg-4">Супровідні документи</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'AccompanyingDocuments')->input('text'); ?>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-6">
        <h5>Зворотня доставка</h5>
        <div class="row">
            <div class="col-md-12 col-lg-4">Вид</div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'BackwardDeliveryData[' . NP_ExpressDoc::BACKWARD_DELIVERY_MONEY . '][CargoType]')->dropDownList(['' => '-', 'Money' => 'Гроши'], ['onChange' => 'updateBackwardDeliveryData(this,"Money")']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4"><span class="js_NP_BD_Money">Платник</span></div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'BackwardDeliveryData[' . NP_ExpressDoc::BACKWARD_DELIVERY_MONEY . '][PayerType]')->dropDownList(['Recipient' => 'Получатель'], ['class' => 'form-control js_NP_BD_Money']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-4"><span class="js_NP_BD_Money">Сума</span></div>
            <div class="col-md-12 col-lg-8">
                <?php echo $form->field($model, 'BackwardDeliveryData[' . NP_ExpressDoc::BACKWARD_DELIVERY_MONEY . '][RedeliveryString]')->input('text', ['class' => 'form-control js_NP_BD_Money']); ?>
            </div>
        </div>
    </div>
</div>

<br/>
<div class="fixed-column">
    <div class="form-group mt-5 mb-5">
        <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">зберегти</button>
    </div>
    <div class="form-group mb-5">
        <button class="btn btn-success btn-sm" type="submit" name="action" value="exit">зберегти і повернутись</button>
    </div>
    <div class="form-group mb-5">
        <a class="btn btn-info btn-sm" href="<?php echo Url::to(['index']) ?>">повернутись</a>
    </div>
</div>

<script>
    function updateBackwardDeliveryData(input, type) {
        console.log("bxod");
        if (input.value === "") {
            $(".js_NP_BD_" + type).prop('disabled', true).slideUp();
        } else {
            $(".js_NP_BD_" + type).prop('disabled', false).slideDown();
        }
    }
    function officesOptions(input, selector) {
        $(selector).load("<?php echo Url::to(['offices-options']); ?>", {city_ref: $(input).val()});
    }
    function senderContacts(input, selector) {
        $.get("<?php echo Url::to(['sender-contacts']); ?>", {sender_ref: $(input).val()}, function (resp) {
            $(selector).html(resp).trigger("change");
        });
    }
    function senderPhone(input, selector) {
        $.get("<?php echo Url::to(['sender-phone']); ?>", {contact_ref: $(input).val()}, function (resp) {
            $(selector).val(resp);
        });
    }
</script>
<?php AdminActiveForm::end(); ?>
