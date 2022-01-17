<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActiveForm;
use app\components\AdminActionColumn;
use app\models\PaymentVariants;
use app\models\DeliveryVariants;
use app\models\novaposhta\NP_Areas;
use app\models\novaposhta\NP_CitiesNp;
use app\models\novaposhta\NP_Offices;

$this->params['breadcrumbs'] = [
    ['label' => 'Заявки', 'url' => 'index'],
    $this->title = "Заявка № {$order->getNumber()}",
];
?>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#main" role="tab" aria-controls="home" aria-selected="true">Основні дані</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#goods" role="tab" aria-controls="home" aria-selected="true">Підбір товару</a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="main" role="tabpanel" aria-labelledby="home-tab">
        <?php
        echo app\components\AdminGrid::widget([
            'id' => 'order-details',
            'dataProvider' => $detailsDataProvider,
            'emptyText' => 'немає товарів',
            'showFooter' => true,
            'placeFooterAfterBody' => true,
            'columns' => [
                [
                    'attribute' => 'brand',
                    'format' => 'raw',
                    'footer' => 'Всього:',
                    'footerOptions' => ['style' => 'text-align:right;', 'colspan' => 5],
                ],
                [
                    'attribute' => 'title',
                    'format' => 'raw',
                    'footerOptions' => ['style' => 'display:none;'],
                    'value' => function ($model) {
                        return $model->getAdminTitle();
                    }
                ],
                [
                    'header' => 'Артикул / Код',
                    'format' => 'raw',
                    'footerOptions' => ['style' => 'display:none;'],
                    'value' => function ($model) {
                        return $model->getAdminCode();
                    }
                ],
                [
                    'attribute' => 'qty',
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'input-cell'],
                    'footerOptions' => ['style' => 'display:none;'],
                    'value' => function ($model) {
                        return Html::input('text', null, $model->qty, [
                            'style' => 'width:100%;text-align:center;',
                            'class' => 'qty',
                            'data-url' => Url::to(['add-goods', 'id' => $model->doc_id, 'goods_id' => $model->goods_id, 'variant_id' => $model->variant_id]),
                            'onChange' => 'changeQty(this,event)'
                        ]);
                    }
                ],
                [
                    'attribute' => 'price',
                    'format' => 'raw',
                    'filter' => false,
                    'contentOptions' => ['class' => 'price righted'],
                    'footerOptions' => ['style' => 'display:none;'],
                    'value' => function ($model) {
                        return $model->getPrice();
                    },
                ],
                [
                    'header' => 'Сума',
                    'format' => 'raw',
                    'filter' => false,
                    'contentOptions' => ['class' => 'amount righted'],
                    'value' => function ($model) {
                        return $model->getAmount();
                    },
                    'footer' => $order->amount,
                    'footerOptions' => ['id' => 'order-amount', 'style' => 'font-weight:bold;', 'class' => 'righted'],
                ],
                [
                    'class' => AdminActionColumn::className(),
                    'template' => '{delete}',
                    'contentOptions' => ['class' => 'actions pr-0'],
                    'buttons' => [
                        'delete' => function ($url, $model, $key) {
                            $url = Url::to(['del-goods', 'id' => $model->doc_id, 'goods_id' => $model->goods_id, 'variant_id' => $model->variant_id]);
                            return Html::a('<i class="fa fa-trash"></i>', $url, [
                                'title' => 'видалити',
                                'data-confirm-message' => yii\helpers\Html::encode('Ви підтверджуєте видалення ' . $model->title . '?'),
                            ]);
                        },
                    ],
                ],
            ],
        ]);
        ?>

        <?php
        $form = AdminActiveForm::begin(['errorSummaryModels' => $order]);
        ?>
        <div class="row">
            <div class="col-sm-12 col-md-8">
                <?php echo $form->field($order, 'firm_id')->dropDownList([$order->firm_id => $order->getFirmTitle()], ['disabled' => true]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <?php echo $form->field($order, 'payment_id')->dropDownList(PaymentVariants::keyval()); ?>
            </div>
            <div class="col-sm-12 col-md-4">
                <?php echo $form->field($order, 'delivery_id')->dropDownList(DeliveryVariants::keyvalClient(), ['onChange' => 'changeDelivery(this)']); ?>
            </div>
        </div>
        <div class="js_delivery js_delivery_<?php echo DeliveryVariants::NOVA_POSHTA; ?>" style="<?php if ($order->delivery_id <> DeliveryVariants::NOVA_POSHTA): ?>display:none;<?php endif; ?>">
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <?php echo $form->field($order, 'np_area_ref')->dropDownList(NP_Areas::keyval(), ['prompt' => '', 'onChange' => 'loadOptionsAlt(this,"/admin/data/cities-options","#preorders-np_city_ref","#preorders-np_office_no")']); ?>
                </div>
                <div class="col-sm-12 col-md-4">
                    <?php
                    echo $form->autocomplete($order, 'np_city_ref', empty($order->np_area_ref) ? [] : NP_CitiesNp::keyval($order->np_area_ref), [
                        'prompt' => '',
                        'data-ref' => $order->np_area_ref,
                        'onChange' => 'loadOptionsAlt(this,"/admin/data/offices-options","#preorders-np_office_no")',
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
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-8">
                    <?php echo $form->autocomplete($order, 'np_office_no', empty($order->np_city_ref) ? [] : NP_Offices::keyval($order->np_city_ref), ['prompt' => '']); ?>
                </div>
            </div>
        </div>
        <div class="js_delivery js_delivery_all" style="<?php if (in_array($order->delivery_id, [DeliveryVariants::NOVA_POSHTA, DeliveryVariants::SELF])): ?>display:none;<?php endif; ?>">
            <div class="row">
                <div class="col-sm-12 col-md-3">
                    <?php echo $form->field($order, 'region')->input('text'); ?>
                </div>
                <div class="col-sm-12 col-md-4">
                    <?php echo $form->field($order, 'city')->input('text'); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <?php echo $form->field($order, 'address')->input('text'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <?php echo $form->field($order, 'consignee')->input('text'); ?>
            </div>
            <div class="col-sm-12 col-md-4">
                <?php echo $form->field($order, 'name')->input('text'); ?>
            </div>
            <div class="col-sm-12 col-md-4">
                <?php echo $form->field($order, 'phone')->input('text', ['placeholder' => '+38']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-8">
                <?php echo $form->field($order, 'client_note')->textarea(['rows' => '2']); ?>
            </div>
        </div>
        <div class="fixed-column">
            <div class="form-group mt-5 mb-5">
                <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">зберегти як чернетку</button>
            </div>
            <div class="form-group mb-5">
                <button class="btn btn-success btn-sm" type="submit" name="action" value="exit">підтвердити</button>
            </div>
            <div class="form-group mb-5">
                <a class="btn btn-info btn-sm" href="<?php echo yii\helpers\Url::toRoute('index') ?>">повернутись</a>
            </div>
        </div>
        <?php AdminActiveForm::end(); ?>
    </div>
    <div class="tab-pane fade show" id="goods" role="tabpanel" aria-labelledby="home-tab">
        <?php
        echo app\components\AdminGrid::widget([
            'filterModel' => $goodsModel,
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'title',
                    'format' => 'raw',
                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $goodsModel->getAttributeLabel('title')],
                ],
                [
                    'attribute' => 'article',
                    'format' => 'raw',
                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $goodsModel->getAttributeLabel('article')],
                ],
                [
                    'attribute' => 'code',
                    'format' => 'raw',
                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $goodsModel->getAttributeLabel('code')],
                ],
                [
                    'attribute' => 'size',
                    'format' => 'raw',
                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $goodsModel->getAttributeLabel('size')],
                ],
                [
                    'attribute' => 'color',
                    'format' => 'raw',
                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $goodsModel->getAttributeLabel('color')],
                ],
                [
                    'header' => 'Ціна',
                    'format' => 'raw',
                    'filter' => false,
                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $goodsModel->getAttributeLabel('price')],
                    'value' => function ($model) {
                        return $model->getPrice();
                    }
                ],
                [
                    'class' => AdminActionColumn::className(),
                    'template' => '{to_cart}',
                    'contentOptions' => ['class' => 'actions pr-0'],
                    'buttons' => [
                        'to_cart' => function ($url, $model, $key) use ($order) {
                            $label = '<div><span class=""><i class="fa fa-shopping-cart"></i></span><input/></div>';
                            $url = Url::to(['add-goods', 'id' => $order->id, 'goods_id' => $model->id]);
                            return Html::a($label, $url, ['class' => 'cart_qty_control', 'title' => 'додати товар в заявку', 'data-pjax' => 0, 'onClick' => 'addGoods(this,event);']);
                        },
                    ],
                ],
            ],
        ]);
        ?>
    </div>
</div>
<style>
    .cart_qty_control input{
        display:none;
    }
    .cart_qty_control.active input{
        display:block;
    }
    .cart_qty_control.active span{
        display:none;
    }
</style>
<script>
    function changeDelivery(input) {
        var $form = $(input).closest("form");
        var val = $(input).val();
        var $div = $form.find("div.js_delivery_".concat(val)).slideDown();
        $form.find("div.js_delivery").not($div).slideUp();
        if ($div.length === 0) {
            $form.find("div.js_delivery_all").slideDown();
        }
    }
    function changeQty(input, e) {
        e.preventDefault();
        var $input = $(input);
        if (!/^\d+$/.test(input.value)) {
            return;
        }
        calculate();
        $.ajax({
            url: $input.data("url"),
            type: 'post',
            data: {qty: Math.round(input.value)},
            error: function (xhr, status, error) {
                alert('Помилка ajax-запиту: ' + xhr.responseText);
            }
        });
    }
    function addGoods(a, e) {
        e.preventDefault();
        var $a = $(a).addClass("active");
        var pjaxContainer = $("#order-details").closest('[data-pjax-container]').attr("id");
        $a.find("input").unbind().focus().one("change", function () {
            var input = this;
            $.ajax({
                url: $a.attr("href"),
                type: 'post',
                data: {qty: Number(input.value)},
                error: function (xhr, status, error) {
                    alert('Помилка ajax-запиту: ' + xhr.responseText);
                }
            }).done(function (data) {
                $.pjax.reload('#' + $.trim(pjaxContainer), {timeout: 3000});
                $a.removeClass("active");
            });
        });
    }
    function calculate() {
        var total = 0;
        $("#order-details").find("tbody tr").each(function (i, tr) {
            var $tr = $(tr);
            var amount = Math.round($tr.find(".qty").val()) * $tr.find(".price").text();
            $tr.find(".amount").text(amount.toFixed(2));
            total += amount;
        });
        $("#order-amount").text(total.toFixed(2));
    }
</script>