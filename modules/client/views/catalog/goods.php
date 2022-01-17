<?php

use app\models\Goods;
use app\models\Params;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<section class="product" id="product">
    <div class="block">
        <p class="locality">
            <?php foreach ($category->findParents() as $i => $dataModel): ?>
                <?php if ($i > 0): ?>
                    &nbsp;&nbsp;•&nbsp;&nbsp;
                <?php endif; ?>
                <a href="<?php echo $dataModel->getUrl(); ?>"><?php echo $dataModel->getTitle(); ?></a>
            <?php endforeach; ?>
            &nbsp;&nbsp;•&nbsp;&nbsp;<a
                    href="<?php echo $category->getUrl(); ?>"><?php echo $category->getTitle(); ?></a>
            <span>•&nbsp;&nbsp;<?php echo $model->getTitle(); ?></span></p>
        <div class="content">
            <div class="picture_block">
                <div class="big">
                    <?php
                    $photoModel = $model->getMainPhoto();
                    if ($photoModel === null) {
                        $src = '/files/images/system/empty.png';
                    } else {
                        $src = $photoModel->getSrc('big');
                    }
                    ?>
                    <img src="<?php echo $src; ?>" alt="">
                </div>
                <br/>
                <div class="small">
                    <?php foreach ($model->getPhotos() as $photoModel): ?>
                        <div id="preview_photo_<?php echo $photoModel->id; ?>" class="small_pic">
                            <img src="<?php echo $photoModel->getSrc('small'); ?>" alt=""
                                 data-big="<?php echo $photoModel->getSrc('big'); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="info">
                <h2><?php echo $model->getTitle(); ?></h2>
                <span>Артикул:  <?php echo $model->article; ?></span><span>Код:  <?php echo $model->code; ?></span>
                <div>
                    <?php foreach ($model->getParams(Params::TYPE_GOODS_ONLY) as $dataModel): ?>
                        <span><?php echo $dataModel->getTitle(); ?>: <?php echo $dataModel->getValueTxt(); ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="description">
                    Детальний опис
                    <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg"
                         class="rotate0">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M0.292893 0.292893C0.683418 -0.0976311 1.31658 -0.0976311 1.70711 0.292893L6 4.58579L10.2929 0.292894C10.6834 -0.0976306 11.3166 -0.0976306 11.7071 0.292894C12.0976 0.683418 12.0976 1.31658 11.7071 1.70711L6.70711 6.70711C6.51957 6.89464 6.26522 7 6 7C5.73478 7 5.48043 6.89464 5.29289 6.70711L0.292893 1.70711C-0.0976311 1.31658 -0.0976311 0.683417 0.292893 0.292893Z"
                              fill="#2F4858"/>
                    </svg>
                    <div class="text">
                        <?php echo $model->getDescr(); ?>
                    </div>
                </div>
            </div>
            <div class="order">
                <div class="title">
                    <h4>Замовлення</h4>
                    <div class="Availability">
                        <div class="square bco"></div>
                        <div class="square bco"></div>
                        <div class="square bco"></div>
                        Закінчується
                    </div>
                    <div class="Availability">
                        <div class="square bcgn"></div>
                        <div class="square bcgn"></div>
                        <div class="square bcgn"></div>
                        В наявності
                    </div>
                    <div class="Availability">
                        <div class="square bcbl"></div>
                        <div class="square bcbl"></div>
                        <div class="square bcbl"></div>
                        Під замовлення
                    </div>
                </div>
                <form id="order_block" class="order_block" action="<?php echo Url::to(['/client/cart/add']); ?>">
                    <?php echo Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()); ?>
                    <div class="item header">
                        <div class="head">
                            <div class="dimensions">Розміри</div>
                            <div class="product_code">Штрих-код</div>
                            <div class="nds">₴ з НДС</div>
                            <div class="in_the_box">В упаковці</div>
                            <div class="presence">Наявність</div>
                            <div class="box_piece js_general">
                                <label class="on">Уп.<input type="radio" name="general_qty_type"
                                                            value="<?php echo Goods::INC_TYPE_PACK; ?>"
                                                            checked/></label>
                                <label>Шт.<input type="radio" name="general_qty_type"
                                                 value="<?php echo Goods::INC_TYPE_QTY; ?>"/></label>
                            </div>
                        </div>
                    </div>
                    <?php
                    $row = 0;
                    foreach ($variantsGrouped as $size => $variants):
                        ?>
                        <h6><?php echo $size; ?></h6>
                        <?php
                        foreach ($variants as $i => $dataModel):
                            if ($dataModel->isNone()) {
                                continue;
                            }
                            $photoModelId = $dataModel->photoModel === null ? null : $dataModel->photoModel->id;
                            ?>
                            <div class="item js_goods" data-weight="<?php echo $dataModel->goodsModel->weight; ?>"
                                 data-volume="<?php echo $dataModel->goodsModel->volume; ?>">
                                <div class="item_hover">
                                    <div class="dimensions"
                                         onClick="GoodsPage_photoVariantClick('#preview_photo_<?php echo $photoModelId; ?>')">
                                        <?php if ($dataModel->color_code <> ''): ?>
                                            <div class="color">
                                                <div style="background-color:<?php echo $dataModel->color_code; ?>"></div>
                                            </div>
                                        <?php elseif ($dataModel->photoModel !== null): ?>
                                            <div class="color">
                                                <img src="<?php echo $dataModel->photoModel->getSrc('color'); ?>"
                                                     alt=""/>
                                            </div>
                                        <?php endif; ?>
                                        <?php
                                        $color = explode('/', $dataModel->color);
                                        echo isset($color[0]) ? $color[0] : $dataModel->color;
                                        ?>
                                    </div>
                                    <div class="product_code">
                                        <?php echo $dataModel->barCode; ?>
                                    </div>
                                    <div class="nds js_price"><?php echo $dataModel->goodsModel->getPrice(); ?></div>
                                    <div class="in_the_box"><?php echo $dataModel->goodsModel->qty_pack; ?></div>
                                    <div class="presence">
                                        <div class="Availability">
                                            <?php if ($dataModel->isInStock()): ?>
                                                <div class="square bcgn"></div>
                                                <div class="square bcgn"></div>
                                                <div class="square bcgn"></div>
                                            <?php elseif ($dataModel->isMinimum()): ?>
                                                <div class="square bco"></div>
                                                <div class="square bco"></div>
                                                <div class="square bco"></div>
                                            <?php elseif ($dataModel->isWait()): ?>
                                                <div class="square bcbl"></div>
                                                <div class="square bcbl"></div>
                                                <div class="square bcbl"></div>
                                            <?php else: ?>
                                                <div class="square bcy"></div>
                                                <div class="square bcy"></div>
                                                <div class="square bcy"></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="box_piece js_item">
                                        <label class="on">Уп.<input type="radio" name="qty_type<?php echo $i; ?>"
                                                                    value="<?php echo Goods::INC_TYPE_PACK; ?>"
                                                                    checked/></label>
                                        <label>Шт.<input type="radio" name="qty_type<?php echo $i; ?>"
                                                         value="<?php echo Goods::INC_TYPE_QTY; ?>"/></label>
                                    </div>
                                    <div class="box_piece">
                                        <div class="counter">
                                            <div class="minus js_btn" data-qty="-1">
                                                <svg width="14" height="2" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M0 1a1 1 0 011-1h12a1 1 0 110 2H1a1 1 0 01-1-1z" fill=""/>
                                                </svg>
                                            </div>
                                            <input name="Goods[<?php echo $row; ?>][qty]"
                                                   data-qty_pack="<?php echo $dataModel->goodsModel->qty_pack; ?>"
                                                   data-qty_max="<?php echo $dataModel->getStockQty(); ?>"
                                                   class="number" value="0" placeholder="0"/>
                                            <?php echo Html::hiddenInput("Goods[{$row}][goods_id]", $dataModel->goodsModel->id); ?>
                                            <?php echo Html::hiddenInput("Goods[{$row}][variant_id]", $dataModel->id); ?>
                                            <div class="plus js_btn" data-qty="1">
                                                <svg width="14" height="14" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M0 7a1 1 0 011-1h12a1 1 0 110 2H1a1 1 0 01-1-1z" fill=""/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M7 14a1 1 0 01-1-1V1a1 1 0 112 0v12a1 1 0 01-1 1z"
                                                          fill=""/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $row++;
                        endforeach;
                        ?>
                    <?php
                    endforeach;
                    ?>
                    <div class="total">
                        <?php if (count($variantsGrouped) > 0): ?>
                            <div>
                                Обрано позицій цієї моделі:
                                <span id="js_total_items">0</span>
                            </div>
                            <div>
                                На суму, ₴:
                                <span id="js_total">0</span>
                            </div>
                            <div>
                                Вага, кг:
                                <span id="js_total_weight">0</span>
                            </div>
                            <div>
                                Об`єм, куб.м:
                                <span id="js_total_volume">0</span>
                            </div>
                        <?php endif; ?>
                        <?php if (getenv('HTTP_REFERER') <> ''): ?>
                            <a href="javascript:history.back();" class="total_btn continue_shopping">Продовжити
                                покупки</a>
                        <?php else: ?>
                            <a href="<?php echo $this->context->categoryModel->getUrl(); ?>"
                               class="total_btn continue_shopping">Продовжити покупки</a>
                        <?php endif; ?>
                        <?php if (count($variantsGrouped) > 0): ?>
                            <a href="<?php echo Url::toRoute(['/client/cart/index']); ?>"
                               class="total_btn to_order <?php echo $goodsQty > 0 ? 'active' : ''; ?>">Оформити
                                заявку</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
