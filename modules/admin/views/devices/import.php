<?php

use yii\helpers\Url;
use app\models\Devices;
use app\models\CsvDevices;
use app\models\Services;
use app\models\DataHelper;
use app\models\Tasks;
?>
<?php
$this->params['breadcrumbs'] = [
    ['label' => 'Пристрої', 'url' => 'index'],
    $this->title = 'Імпорт',
];
?>
<?php if ($isRun): ?>
    <div class="progress mt-3 mb-3">
        <?php if ($progress == 0): ?>
            <div id="progress_bar" data-progress="<?php echo $progress; ?>" data-url="<?php echo Url::toRoute(['/admin/data/progress', 'id' => Tasks::IMPORT_DEVICES_CSV]); ?>" class="progress-bar bg-info progress-bar-animated" role="progressbar" style="width:100%;font-weight:bold;">очікується обробка...</div>
        <?php else: ?>
            <div id="progress_bar" data-progress="<?php echo $progress; ?>" data-url="<?php echo Url::toRoute(['/admin/data/progress', 'id' => Tasks::IMPORT_DEVICES_CSV]); ?>" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width:0;font-weight:bold;"></div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <span class="btn btn-sm btn-info mb-3" data-import-csv data-url="<?php echo Url::toRoute('upload'); ?>"><i class="fa fa-upload"></i> завантажити файл...</span>
    <a class="btn btn-sm btn-info mb-3 float-right" href="<?php echo Url::toRoute('download-csv'); ?>"><i class="fa fa-download"></i> скачати зразок...</a>
    <?php if (strlen($info) > 0): ?>
        <div class="bg-light">
            <div class="mb-3">Результати останнього імпорту <?php echo $date; ?></div>
            <ul><?php echo $info; ?></ul>
        </div>
    <?php endif; ?>
<?php endif; ?>
<div class="row">
    <div class="col">
        <div class="p-2 hint-block text-monospace mt-5 bg-light">
            <p class="mb-2">CSV-файл для імпорта повинен мати наступні поля:</p>
            <?php
            $variants = CsvDevices::getVariants();
            foreach (CsvDevices::$fields as $field => $label):
                ?>
                <p class="mb-2">- <?php echo $label; ?>
                    <?php
                    if (isset(CsvDevices::$notes[$field]) || isset($variants[$field])):
                        echo '(';
                    endif;
                    if (isset(CsvDevices::$notes[$field])):
                        echo CsvDevices::$notes[$field];
                    endif;
                    if (isset(CsvDevices::$notes[$field]) && isset($variants[$field])): echo ';';
                    endif;
                    if (isset($variants[$field])):
                        echo 'можливі варіанти: "' . implode('", "', $variants[$field]) . '"';
                    endif;
                    if (isset(CsvDevices::$notes[$field]) || isset($variants[$field])):
                        echo ')';
                    endif;
                    ?>
                    ;</p>
            <?php endforeach; ?>
            <br/>
            Перша строка - строка заголовка<br/>
            Кожне поле повинно бути у подвійних лапках <kbd>"</kbd><br/>
            Разділювач полей - символ <kbd>;</kbd><br/>
            Строки, що не пройдуть валідацію, будуть проігноровані.<br/>
        </div>
    </div>
</div>
