<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\ExcelViewer;
use app\components\AppActiveForm;

\app\components\assets\ExcelViewer::register($this);
$this->title = "Заявка з Excel-файлу";
?>

<div class="table">
    <div class="block">
        <?php echo $this->context->renderPartial('_upload_button'); ?>
        <br/>
        <div class="excel_viewer">
            <?php
            $form = AppActiveForm::begin([
                        'action' => Url::to(['import-excel']),
                        'options' => ['class' => 'document']
            ]);
            ?>
            <div id="message"></div>
            <div class="sheets">
                <?php echo Html::dropDownList("ExcelViewer[sheet]", $active, $sheets, ['onChange' => 'ExcelViewer_loadContent(this)']); ?>
            </div>
            <div id="current-sheet">
                <?php echo $this->context->renderPartial('_xls_table', ['sheet' => $sheet, 'settings' => $settings]); ?>
            </div>
            <div class="btn-row">
                <div class="btn" onClick="ExcelViewer_confirm(this, false)">Доповнити кошик товарами</div>
                <div class="btn" onClick="ExcelViewer_confirm(this, true)">Очистити кошик і додати товари</div>
                <input id="clean" name="ExcelViewer[clean]" value="0" class="dn"/>
            </div>
            <?php AppActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    function ExcelViewer_loadContent(sheet) {
        var $div = $("#current-sheet");
        var $btn = $("#profile .js_upload_btn,#js_upload_btn,#js_upload_indicator").toggle();
        $.post("", $(sheet).closest("form").serialize(), function (resp) {
            $div.html(resp);
            $btn.toggle();
        });
    }
    function ExcelViewer_confirm(btn, clean) {
        var $form = $(btn).closest("form");
        var $btn = $(btn);
        $btn.parent().hide();
        $("#clean").val(Number(clean));
        var $controls = $("#profile .js_upload_btn,#js_upload_btn,#js_upload_indicator").toggle();
        var $sheetSelector = $form.find("select").hide();
        var $message = $("#message").slideUp();
        $.ajax({
            url: $form.attr("action"),
            type: "post",
            data: $form.serialize(),
            dataType: "json",
            success: function (resp) {
                if (resp.res === true) {
                    location.href = resp.url;
                } else {
                    $message.html(resp.message).slideDown();
                    $btn.parent().show();
                    $sheetSelector.show();
                    $controls.toggle();
                }
            }
        });
    }
</script>
