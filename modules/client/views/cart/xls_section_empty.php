<?php

use yii\helpers\Url;
use app\components\Icons;

\app\components\assets\ExcelViewer::register($this);
$this->title = "Заявка з Excel-файлу";
?>
<div class="table excel_viewer">
    <div class="block">
        <?php echo $this->context->renderPartial('_upload_button'); ?>
        <br/>
        у файлі немає листів з даними.
    </div>
</div>

