<?php

use app\components\AppActiveForm;
use app\models\XML_PriceUa;
use yii\helpers\Html;
use yii\helpers\Url;

?>
    <div id="js_download" class="btn_default" data-url="">Скачати прайс-лист</div>
    <div class="dark_fond opac0" id="modal-download">
        <?php $form = AppActiveForm::begin(); ?>
        <div class="modal" style="max-width:400px;">
            <h2>Скачати прайс-лист</h2>
            <svg class="close" width="16" height="16" viewBox="0 0 16 16" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M15.7071 1.70711C16.0976 1.31658 16.0976 0.683417 15.7071 0.292893C15.3166 -0.0976311 14.6834 -0.0976311 14.2929 0.292893L8 6.58579L1.70711 0.292893C1.31658 -0.0976311 0.683417 -0.0976311 0.292893 0.292893C-0.0976311 0.683417 -0.0976311 1.31658 0.292893 1.70711L6.58579 8L0.292893 14.2929C-0.0976311 14.6834 -0.0976311 15.3166 0.292893 15.7071C0.683417 16.0976 1.31658 16.0976 1.70711 15.7071L8 9.41421L14.2929 15.7071C14.6834 16.0976 15.3166 16.0976 15.7071 15.7071C16.0976 15.3166 16.0976 14.6834 15.7071 14.2929L9.41421 8L15.7071 1.70711Z"
                      fill=""/>
            </svg>
            <div class="body flex-list">
                <div>
                    <?php foreach (array_slice(XML_PriceUa::$attrs, 0, 6) as $id => $title): ?>
                        <label class="checkbox">
                            <?php echo Html::checkbox("XML_PriceUa[{$id}]", in_array($id, XML_PriceUa::$default)); ?>
                            <span><?php echo $title; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div>
                    <?php foreach (array_slice(XML_PriceUa::$attrs, 6) as $id => $title): ?>
                        <label class="checkbox">
                            <?php echo Html::checkbox("XML_PriceUa[{$id}]", in_array($id, XML_PriceUa::$default)); ?>
                            <span><?php echo $title; ?></span>
                        </label>
                    <?php endforeach; ?>
                    <?php echo Html::hiddenInput("XML_PriceUa[cat_id]", $cat_id); ?>
                </div>
            </div>
            <div class="footer">
                <div>
                    <a class="btn_default"
                       href="<?php echo Url::to(['/client/data/download-xml', 'id' => $cat_id, 'ref' => getenv('REQUEST_URI')]); ?>">Скачати
                        в XML</a>
                </div>
                <div>
                    <a class="btn_default"
                       href="<?php echo Url::to(['/client/data/download-excel', 'id' => $cat_id, 'ref' => getenv('REQUEST_URI')]); ?>">Скачати
                        в Excel</a>
                </div>
            </div>
            <span id="js_upload_indicator" class="dn"
                  style="padding:7px;"><?php echo \app\components\Icons::$loading; ?></span>
        </div>
        <?php AppActiveForm::end(); ?>
    </div>

<?php if (isset($category_level) && $category_level == 2) { ?>
    <div id="js_download_with_image" class="btn_default" data-url="">Скачати прайс-лист з фото</div>
    <div class="dark_fond opac0" id="modal-download-with-image">
        <?php $form = AppActiveForm::begin(); ?>
        <div class="modal" style="max-width:400px;">
            <h2>Скачати прайс-лист з фото</h2>
            <svg class="close" width="16" height="16" viewBox="0 0 16 16" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M15.7071 1.70711C16.0976 1.31658 16.0976 0.683417 15.7071 0.292893C15.3166 -0.0976311 14.6834 -0.0976311 14.2929 0.292893L8 6.58579L1.70711 0.292893C1.31658 -0.0976311 0.683417 -0.0976311 0.292893 0.292893C-0.0976311 0.683417 -0.0976311 1.31658 0.292893 1.70711L6.58579 8L0.292893 14.2929C-0.0976311 14.6834 -0.0976311 15.3166 0.292893 15.7071C0.683417 16.0976 1.31658 16.0976 1.70711 15.7071L8 9.41421L14.2929 15.7071C14.6834 16.0976 15.3166 16.0976 15.7071 15.7071C16.0976 15.3166 16.0976 14.6834 15.7071 14.2929L9.41421 8L15.7071 1.70711Z"
                      fill=""/>
            </svg>
            <div class="body flex-list">
                <div>
                    <?php foreach (array_slice(XML_PriceUa::$attrs, 0, 6) as $id => $title): ?>
                        <label class="checkbox">
                            <?php echo Html::checkbox("XML_PriceUa[{$id}]", in_array($id, XML_PriceUa::$defaultWithImage)); ?>
                            <span><?php echo $title; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div>
                    <?php foreach (array_slice(XML_PriceUa::$attrs, 6) as $id => $title): ?>
                        <label class="checkbox">
                            <?php echo Html::checkbox("XML_PriceUa[{$id}]", in_array($id, XML_PriceUa::$defaultWithImage)); ?>
                            <span><?php echo $title; ?></span>
                        </label>
                    <?php endforeach; ?>
                    <?php echo Html::hiddenInput("XML_PriceUa[cat_id]", $cat_id); ?>
                </div>
            </div>
            <div class="footer">
                <div>
                    <a class="btn_default"
                       href="<?php echo Url::to(['/client/data/download-xml', 'id' => $cat_id, 'ref' => getenv('REQUEST_URI')]); ?>">Скачати
                        в XML</a>
                </div>
                <div>
                    <a class="btn_default"
                       href="<?php echo Url::to(['/client/data/download-excel-photo', 'id' => $cat_id, 'ref' => getenv('REQUEST_URI')]); ?>">Скачати
                        в Excel</a>
                </div>
            </div>
            <span id="js_upload_indicator" class="dn"
                  style="padding:7px;"><?php echo \app\components\Icons::$loading; ?></span>
        </div>
        <?php AppActiveForm::end(); ?>
    </div>
<?php } ?>