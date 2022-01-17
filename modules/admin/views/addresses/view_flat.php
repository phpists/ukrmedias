<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\AddrFlats;
?>
<?php
$this->params['breadcrumbs'] = [
    ['label' => 'Абоненти', 'url' => 'index'],
    $this->title = $flat->getTitle(),
];
?>
<div class="row">
    <div class="col-6 col-sm-5 col-lg-3">
        Користувачі:
    </div>
</div>
<?php echo $this->context->renderPartial('_users', ['dataProvider' => $dataProvider, 'flat' => $flat]); ?>
<div class="mt-5">
    <div class="fixed-column wide">
        <div class="form-group mb-5">
            <a class="btn btn-sm btn-primary" href="<?php echo Url::toRoute(['/admin/abonents/update', 'id' => $flat->house_id, 'aid' => $flat->id]); ?>"><i class="fa fa-plus"></i> додати нового користувача</a>
        </div>
        <div class="form-group mb-5">
            <a class="btn btn-sm btn-success" href="<?php echo Url::toRoute(['/admin/abonents/apply', 'id' => $flat->house_id, 'aid' => $flat->id]); ?>"><i class="fa fa-plus"></i> додати існуючого користувача</a>
        </div>
    </div>
</div>
