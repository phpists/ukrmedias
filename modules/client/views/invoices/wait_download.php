<?php

use yii\helpers\Url;
use app\models\CsvCounters;
use app\models\Tasks;

$this->params['breadcrumbs'] = [
    ['label' => 'Рахунки', 'url' => 'index'],
];
$this->title = 'Підготовка рахунків для всього будинку...';
?>
<div class="main-template-employ main-template-import">
    <div class="flex-title-block">
        <div class="h3 main-template__header"><?php echo $this->title; ?></div>
    </div>
    <div>
        <div class="progress mt-3 mb-3">
            <?php if ($progress == 0): ?>
                <div id="progress_bar" data-progress="<?php echo $progress; ?>" data-url="<?php echo Url::toRoute(['/admin/data/progress', 'id' => Tasks::CREATE_INVOICES_ZIP]); ?>" class="progress-bar bg-info progress-bar-animated" role="progressbar" style="width:100%;font-weight:bold;">очікується обробка...</div>
            <?php else: ?>
                <div id="progress_bar" data-progress="<?php echo $progress; ?>" data-url="<?php echo Url::toRoute(['/admin/data/progress', 'id' => Tasks::CREATE_INVOICES_ZIP]); ?>" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width:0;font-weight:bold;">123</div>
            <?php endif; ?>
        </div>
        <div class="row">
            <div class="col">

            </div>
        </div>
    </div>
</div>