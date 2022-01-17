<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppActiveForm;
use app\models\Goods;
use app\models\Addresses;
use app\models\DeliveryVariants;
use app\models\novaposhta\NP_Areas;
use app\models\novaposhta\NP_CitiesNp;
use app\models\novaposhta\NP_Offices;

$form = AppActiveForm::begin([
            'validateOnSubmit' => true,
            'encodeErrorSummary' => false,
//            'fieldConfig' => [
//                'template' => '{label}{input}',
//            ]
        ]);
$goodsQty = 0;
?>
<section class="ordering" id="ordering">
    <div class="block">
        <?php echo app\components\Misc::getFlashMessages(); ?>
        <p class="locality"><?php echo $this->title = 'Оформлення замовлення'; ?></p>
        <?php echo $form->errorSummary($model, ['showAllErrors' => true]); ?>
        <div class="warehouse">
            <h4>Склад замовлення</h4>
            <div class="head">
                <div class="foto">
                    Фото
                </div>
                <div class="name">
                    Назва
                </div>
                <div class="brand">
                    Бренд
                </div>
                <div class="vendor_code">
                    Артикул / Код
                </div>
                <div class="in_stock">
                    На складі
                </div>
                <div class="box_piece js_general">
                    <label class="on">Уп.<input type="radio" name="general_qty_type" value="<?php echo Goods::INC_TYPE_PACK; ?>" checked/></label>
                    <label>Шт.<input type="radio" name="general_qty_type" value="<?php echo Goods::INC_TYPE_QTY; ?>"/></label>
                </div>
                <div class="price">
                    Ціна, ₴
                </div>
                <div class="sum">
                    Сума, ₴
                </div>
                <div class="close">
                    <svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.70711 1.70711C8.09763 1.31658 8.09763 0.683417 7.70711 0.292893C7.31658 -0.0976311 6.68342 -0.0976311 6.29289 0.292893L4 2.58579L1.70711 0.292893C1.31658 -0.0976311 0.683417 -0.0976311 0.292893 0.292893C-0.0976311 0.683417 -0.0976311 1.31658 0.292893 1.70711L2.58579 4L0.292893 6.29289C-0.0976311 6.68342 -0.0976311 7.31658 0.292893 7.70711C0.683417 8.09763 1.31658 8.09763 1.70711 7.70711L4 5.41421L6.29289 7.70711C6.68342 8.09763 7.31658 8.09763 7.70711 7.70711C8.09763 7.31658 8.09763 6.68342 7.70711 6.29289L5.41421 4L7.70711 1.70711Z" fill="white"/>
                    </svg>
                </div>
            </div>
            <?php
            foreach ($details as $row => $dataModel):
                $goodsQty += $dataModel->qty;
                ?>
                <div class="item js_goods <?php echo array_key_exists($dataModel->variant_id, $runtimeData) ? 'error' : ''; ?>" data-weight="<?php echo $dataModel->getGoods()->weight; ?>" data-volume="<?php echo $dataModel->getGoods()->volume; ?>">
                    <div class="foto">
                        <?php
                        $photoModel = $dataModel->getGoods()->getMainPhoto();
                        if ($photoModel === null) {
                            $src = '/files/images/system/empty.png';
                        } else {
                            $src = $photoModel->getSrc('small');
                        }
                        ?>
                        <img src="<?php echo $src; ?>" alt="">
                    </div>
                    <div class="name">
                        <?php echo $dataModel->getAdminTitle(); ?><br/>
                    </div>
                    <div class="brand">
                        <?php echo $dataModel->brand; ?>
                    </div>
                    <div class="vendor_code">
                        <?php echo $dataModel->article; ?> / <?php echo $dataModel->getVariant()->barCode; ?>
                    </div>
                    <div class="in_stock">
                        <?php echo $dataModel->getVariant()->getStockQty(); ?>
                    </div>
                    <div class="box_piece js_item">
                        <label class="on">Уп.<input type="radio" name="qty_type" value="<?php echo Goods::INC_TYPE_PACK; ?>" checked/></label>
                        <label>Шт.<input type="radio" name="qty_type" value="<?php echo Goods::INC_TYPE_QTY; ?>"/></label>
                    </div>
                    <div class="box_piece">
                        <div class="counter">
                            <div class="minus js_btn" data-qty="-1">
                                <svg width="14" height="2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0 1a1 1 0 011-1h12a1 1 0 110 2H1a1 1 0 01-1-1z" fill=""/>
                                </svg>
                            </div>
                            <input name="Goods[<?php echo $row; ?>][qty]" data-qty_pack="<?php echo $dataModel->getGoods()->qty_pack; ?>" data-qty_max="<?php echo $dataModel->getVariant()->getStockQty(); ?>"  class="number" value="<?php echo $dataModel->qty; ?>" placeholder="0"/>
                            <?php echo Html::hiddenInput("Goods[{$row}][goods_id]", $dataModel->goods_id); ?>
                            <?php echo Html::hiddenInput("Goods[{$row}][variant_id]", $dataModel->variant_id); ?>
                            <div class="plus js_btn" data-qty="1">
                                <svg width="14" height="14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0 7a1 1 0 011-1h12a1 1 0 110 2H1a1 1 0 01-1-1z" fill=""/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7 14a1 1 0 01-1-1V1a1 1 0 112 0v12a1 1 0 01-1 1z" fill=""/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="price">
                        <label>Ціна:</label><span class="js_price"><?php echo $dataModel->price; ?></span>
                    </div>
                    <div class="sum">
                        <label>Сума:</label><span class="js_amount"><?php echo $dataModel->getAmount(); ?></span>
                    </div>
                    <a class="close" href="<?php echo Url::to(['del', 'id' => $dataModel->doc_id, 'goods_id' => $dataModel->goods_id, 'variant_id' => $dataModel->variant_id]); ?>" data-confirm-click="Ви підтверджуєте видалення товару <?php echo Html::encode($dataModel->getAdminTitle(' / ')); ?>?">
                        <svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.70711 1.70711C8.09763 1.31658 8.09763 0.683417 7.70711 0.292893C7.31658 -0.0976311 6.68342 -0.0976311 6.29289 0.292893L4 2.58579L1.70711 0.292893C1.31658 -0.0976311 0.683417 -0.0976311 0.292893 0.292893C-0.0976311 0.683417 -0.0976311 1.31658 0.292893 1.70711L2.58579 4L0.292893 6.29289C-0.0976311 6.68342 -0.0976311 7.31658 0.292893 7.70711C0.683417 8.09763 1.31658 8.09763 1.70711 7.70711L4 5.41421L6.29289 7.70711C6.68342 8.09763 7.31658 8.09763 7.70711 7.70711C8.09763 7.31658 8.09763 6.68342 7.70711 6.29289L5.41421 4L7.70711 1.70711Z" fill=""/>
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
            <div class="total">
                <p>Разом:</p>
                <div id="js_total" data-minamount="<?php echo $minAmount; ?>"><?php echo $model->amount; ?></div>
                <p>До мінімальної суми замовлення залишилось:</p>
                <div id="js_to_minimum"><?php echo $toMin; ?></div>
            </div>
        </div>
    </div>
</section>
<?php if (count($details) > 0): ?>
    <section class="payment" id="payment">
        <div class="block">
            <div class="cont">
                <div class="head">
                    <h4>Оплата</h4>
                </div>
                <div class="variants">
                    <?php foreach ($payments as $id => $title): ?>
                        <label class="radio">
                            <?php
                            echo Html::activeRadio($model, 'payment_id', [
                                'id' => uniqid('id'),
                                'value' => $id,
                                'label' => false,
                                'uncheck' => false,
                            ]);
                            ?>
                            <span><?php echo $title; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="description">
                    <div class="variant db">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!----- delivery  ----->

    <section class="payment" id="delivery">
        <div class="block">
            <div class="cont">
                <div class="head">
                    <h4>Доставка</h4>
                </div>
                <div class="variants">
                    <?php foreach ($delivery as $id => $title): ?>
                        <label class="radio">
                            <?php
                            echo Html::activeRadio($model, 'delivery_id', [
                                'id' => uniqid('id'),
                                'value' => $id,
                                'label' => false,
                                'uncheck' => false,
                            ]);
                            ?>
                            <span><?php echo $title; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="description">
                    <div class="variant">
                        <?php echo $form->field($model, 'address_id')->dropDownList(Addresses::keyval(), ['prompt' => 'інша адреса', 'class' => 'default', 'onChange' => 'changeAddress(this)']); ?>
                    </div>
                    <div id="js_address" <?php if ($model->address_id <> ''): ?>style="display:none;"<?php endif; ?>>
                        <div class="js_delivery js_delivery_<?php echo DeliveryVariants::NOVA_POSHTA; ?>" <?php if ($model->delivery_id <> DeliveryVariants::NOVA_POSHTA): ?>style="display:none;"<?php endif; ?>>
                            <div class="variant">
                                <?php echo $form->autocomplete($model, 'np_area_ref', NP_Areas::keyval(), ['template' => '{input}', 'prompt' => '', 'onChange' => 'loadOptionsAlt(this,"/client/data/cities-options","#preorders-np_city_ref","#preorders-np_office_no")']); ?>
                            </div>
                            <div class="variant">
                                <?php
                                echo $form->autocomplete($model, 'np_city_ref', empty($model->np_area_ref) ? [] : NP_CitiesNp::keyval($model->np_area_ref), [
                                    'prompt' => '',
                                    'data-ref' => $model->np_area_ref,
                                    'onChange' => 'loadOptionsAlt(this,"/client/data/offices-options","#preorders-np_office_no")',
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
                            </div>
                            <div class="variant">
                                <?php echo $form->autocomplete($model, 'np_office_no', empty($model->np_city_ref) ? [] : NP_Offices::keyval($model->np_city_ref), ['prompt' => '']); ?>
                            </div>
                        </div>
                        <div class="js_delivery js_delivery_all" <?php if ($model->delivery_id == DeliveryVariants::NOVA_POSHTA): ?>style="display:none;"<?php endif; ?>>
                            <div class="variant">
                                <?php echo $form->field($model, 'region')->input('text'); ?>
                            </div>
                            <div class="variant">
                                <?php echo $form->field($model, 'city')->input('text'); ?>
                            </div>
                            <div class="variant">
                                <?php echo $form->field($model, 'address')->input('text'); ?>
                            </div>
                        </div>
                        <div class="variant">
                            <?php echo $form->field($model, 'name')->input('text'); ?>
                        </div>
                        <div class="variant">
                            <?php echo $form->field($model, 'phone')->input('text', ['data-phone-mask' => '1']); ?>
                        </div>
                        <div class="variant">
                            <?php echo $form->field($model, 'consignee')->input('text'); ?>
                        </div>
                    </div>
                    <div class="variant">
                        <?php echo $form->field($model, 'client_note')->textarea(['rows' => '2']); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!----- confirm  ----->

    <section class="confirm">
        <div class="block">
            <div class="cont">
                <div class="total">
                    Обрано товарів:
                    <span id="js_goods_qty"><?php echo $goodsQty; ?></span>
                </div>
                <div class="total">
                    На суму, ₴:
                    <span id="js_total_alt"><?php echo $model->amount; ?></span>
                </div>
                <div class="total">
                    Вартість доставки, ₴:
                    <span>-</span>
                </div>
                <div class="total">
                    Разом, ₴:
                    <span id="js_grand_gotal"><?php echo $model->amount; ?></span>
                </div>
                <button class="btn" id="confirm_btn">Підтвердити замовлення</button>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php AppActiveForm::end(); ?>
<script>
    function changeAddress(input) {
        if (input.value === "") {
            $("#js_address").slideDown();
        } else {
            $("#js_address").slideUp();
        }
    }
</script>