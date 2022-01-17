<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;
use app\models\DeliveryVariants;
use app\models\novaposhta\NP_Areas;
use app\models\novaposhta\NP_CitiesNp;
use app\models\novaposhta\NP_Offices;

$this->params['breadcrumbs'] = [
    ['label' => 'Співробітники', 'url' => 'index'],
];
$this->title = 'Адреси';
?>
<?php $form = AppActiveForm::begin(); ?>
<div class="table">
    <div class="profile">
        <div class="item">
            <?php if ($model->isNewRecord): ?>
                <h2>Нова адреса</h2>
            <?php endif; ?>
            <div class="col">
                <h5>Заповніть поля</h5>
                <p>Доставка</p>
                <?php echo $form->field($model, 'delivery_id')->label(false)->dropDownList(DeliveryVariants::keyvalClient(), ['prompt' => 'будь-яка', 'onChange' => 'changeDelivery(this)']); ?>
                <div class="js_delivery js_delivery_<?php echo DeliveryVariants::NOVA_POSHTA; ?>" <?php if ($model->delivery_id <> DeliveryVariants::NOVA_POSHTA): ?>style="display:none;"<?php endif; ?>>
                    <p>Область</p>
                    <?php echo $form->autocomplete($model, 'np_area_ref', NP_Areas::keyval(), ['prompt' => '', 'label' => false, 'onChange' => 'loadOptionsAlt(this,"/client/data/cities-options","#addresses-np_city_ref","#addresses-np_office_no")']); ?>
                    <p>Населений пункт</p>
                    <?php
                    echo $form->autocomplete($model, 'np_city_ref', empty($model->np_area_ref) ? [] : NP_CitiesNp::keyval($model->np_area_ref), [
                        'prompt' => '',
                        'label' => false,
                        'data-ref' => $model->np_area_ref,
                        'onChange' => 'loadOptionsAlt(this,"/client/data/offices-options","#addresses-np_office_no")',
                        'pluginOptions' => [
                            'matcher' => new yii\web\JsExpression('function(params, data) {
                                if ($.trim(params.term) === "") {
                                    return data;
                                }
                                if (typeof data.text === "undefined") {
                                    return null;
                                }
                                var text=data.text.replace(/^([^\(]+).*/,"$1").toLowerCase();
                                if (text.indexOf(params.term.toLowerCase()) > -1) {
                                    return data;
                                }
                                return null;
                              }'),
                        ],
                    ]);
                    ?>
                    <p>Відділення</p>
                    <?php echo $form->autocomplete($model, 'np_office_no', empty($model->np_city_ref) ? [] : NP_Offices::keyval($model->np_city_ref), ['prompt' => '', 'label' => false]); ?>
                </div>
                <div class="js_delivery js_delivery_all" <?php if ($model->delivery_id == DeliveryVariants::NOVA_POSHTA): ?>style="display:none;"<?php endif; ?>>
                    <p>Область</p>
                    <?php echo $form->field($model, 'region')->label(false)->input('text', ['class' => '']); ?>
                    <p>Населений пункт</p>
                    <?php echo $form->field($model, 'city')->label(false)->input('text', ['class' => '']); ?>
                    <p>Адреса</p>
                    <?php echo $form->field($model, 'address')->label(false)->input('text', ['class' => '']); ?>
                </div>
                <p>Найменування вантажоотримувача</p>
                <?php echo $form->field($model, 'consignee')->label(false)->input('text', ['class' => '']); ?>
                <p>Контактна особа</p>
                <?php echo $form->field($model, 'name')->label(false)->input('text', ['class' => '']); ?>
                <p>Номер телефону</p>
                <?php echo $form->field($model, 'phone')->label(false)->input('text', ['class' => '', 'data-phone-mask' => '1']); ?>
            </div>
        </div>
        <div class="item">
            <h2>Статус</h2>
            <label class="checkbox">
                <?php
                echo Html::activeCheckbox($model, 'asDefault', [
                    'checked' => $firm->default_addr_id > 0 && $firm->default_addr_id == $model->id,
                    'label' => false,
                ]);
                ?>
                <span>Встановити адресою за замовчуванням</span>
            </label>
        </div>
        <div class="item">
            <div class="buttons">
                <a class="btn cancel" href="<?php echo Url::to(['index']); ?>">Повернутись</a>
                <button class="btn save" name="action" value="save">Зберегти</button>
            </div>
        </div>
    </div>
</div>
<?php AppActiveForm::end(); ?>
<script>
    function changeDelivery(input) {
        var $form = $(input).closest("form");
        var val = $(input).val();
        var $div = $form.find("div.js_delivery_".concat(val)).slideDown();
        var $items = $form.find("div.js_delivery");
        if ($div.length === 0) {
            $div = $form.find("div.js_delivery_all").slideDown();
        }
        $items.not($div).slideUp();
    }
</script>