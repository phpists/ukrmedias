<?php

use yii\helpers\Url;
use app\models\Tasks;
use app\models\AddrFlats;
use app\models\Users;
use app\models\CsvClientAddresses;

$this->params['breadcrumbs'] = [
    ['label' => 'Адреси', 'url' => '/client/houses/index'],
];
$this->title = 'Імпорт абонентів';
?>
<div class="main-template-employ">
    <div class="flex-title-block">
        <div class="h3 main-template__header"><?php echo $this->title; ?></div>
        <div class="top-buttons-group">
            <a href="javascript:void(0);" class="btn-add-white">
                <div class="icon">
                    <svg width="16" height="16" viewBox="0 0 16 16"><path fill="#918DA6" d="M7.33333 0C6.96514 0 6.66667 0.298477 6.66667 0.666667V6.66667H0.666667C0.298477 6.66667 0 6.96514 0 7.33333V8.66667C0 9.03486 0.298477 9.33333 0.666667 9.33333H6.66667V15.3333C6.66667 15.7015 6.96514 16 7.33333 16H8.66667C9.03486 16 9.33333 15.7015 9.33333 15.3333V9.33333H15.3333C15.7015 9.33333 16 9.03486 16 8.66667V7.33333C16 6.96514 15.7015 6.66667 15.3333 6.66667H9.33333V0.666667C9.33333 0.298477 9.03486 0 8.66667 0H7.33333Z" /></svg>
                </div>
                <span data-import-csv data-url="<?php echo Url::toRoute('upload'); ?>">Завантажити файл...</span>
            </a>
            <a href="<?php echo Url::toRoute('download-csv'); ?>" class="btn-add-white">
                <div class="icon">
                    <svg width="16" height="16" viewBox="0 0 16 16"><path fill="#918DA6" d="M2 .875C2 .668 2.168.5 2.375.5h7.28c.1 0 .195.04.265.11l3.97 3.97c.07.07.11.165.11.265v10.28a.375.375 0 0 1-.375.375H2.375A.375.375 0 0 1 2 15.125v-2.25c0-.207.168-.375.375-.375h.75c.207 0 .375.168.375.375v.75c0 .207.168.375.375.375h8.25a.375.375 0 0 0 .375-.375v-7.5a.375.375 0 0 0-.375-.375h-3a.375.375 0 0 1-.375-.375v-3A.375.375 0 0 0 8.375 2h-4.5a.375.375 0 0 0-.375.375v4.5a.375.375 0 0 1-.375.375h-.75A.375.375 0 0 1 2 6.875v-6z"/></svg>
                </div>
                <span>Скачати зразок...</span>
            </a>
        </div>
    </div>
    <div>
        <?php if ($isRun): ?>
            <div class="progress mt-3 mb-3">
                <?php if ($progress == 0): ?>
                    <div id="progress_bar" data-progress="<?php echo $progress; ?>" data-url="<?php echo Url::toRoute(['/admin/data/progress', 'id' => Tasks::IMPORT_CLIENT_ADDRESSES_CSV]); ?>" class="progress-bar bg-info progress-bar-animated" role="progressbar" style="width:100%;font-weight:bold;">очікується обробка...</div>
                <?php else: ?>
                    <div id="progress_bar" data-progress="<?php echo $progress; ?>" data-url="<?php echo Url::toRoute(['/admin/data/progress', 'id' => Tasks::IMPORT_CLIENT_ADDRESSES_CSV]); ?>" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width:0;font-weight:bold;"></div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php if (strlen($info) > 0): ?>
                <div class="bg-white p-2">
                    <div class="mb-3">Результати останнього імпорту <?php echo $date; ?></div>
                    <ul><?php echo $info; ?></ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="row">
            <div class="col">
                <div class="p-2 hint-block text-monospace">
                    <?php
                    $variants = CsvClientAddresses::getVariants();
                    foreach (CsvClientAddresses::$fields as $field => $label):
                        ?>
                        <p class="mb-2">- <?php echo $label; ?>
                            <?php
                            if (isset(CsvClientAddresses::$notes[$field]) || isset($variants[$field])):
                                echo '(';
                            endif;
                            if (isset(CsvClientAddresses::$notes[$field])):
                                echo CsvClientAddresses::$notes[$field];
                            endif;
                            if (isset(CsvClientAddresses::$notes[$field]) && isset($variants[$field])): echo ';';
                            endif;
                            if (isset($variants[$field])):
                                echo 'можливі варіанти: "' . implode('", "', $variants[$field]) . '"';
                            endif;
                            if (isset(CsvClientAddresses::$notes[$field]) || isset($variants[$field])):
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
    </div>
</div>
