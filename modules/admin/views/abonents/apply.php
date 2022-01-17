<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActiveForm;
use app\components\Auth;
use app\models\Users;
use app\models\AddrHouses;

$this->params['breadcrumbs'] = [
    ['label' => 'Абоненти', 'url' => '/admin/addresses/index'],
    ['label' => $object->getTitle(), 'url' => $object instanceof AddrHouses ? ['/admin/addresses/view', 'id' => $object->id] : ['/admin/addresses/view', 'id' => $object->house_id, 'aid' => $object->id]],
    $this->title = $model->isNewRecord ? 'Профіль користувача' : $model->getName(),
];
$form = AdminActiveForm::begin(['errorSummaryModels' => $model]);
?>
<div class="row">
    <div class="col-sm-12 col-md-4">
        <?php echo $form->autocomplete($model, 'id', Users::keyvalAbonents()); ?>
    </div>
</div>
<div class="fixed-column">
    <div class="form-group mt-5 mb-5">
        <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">зберегти</button>
    </div>
    <div class="form-group mb-5">
        <button class="btn btn-success btn-sm" type="submit" name="action" value="exit">зберегти та повернутись</button>
    </div>
    <div class="form-group mb-5">

        <a class="btn btn-info btn-sm" href="<?php echo Url::toRoute($object instanceof AddrHouses ? ['/admin/addresses/view', 'id' => $object->id] : ['/admin/addresses/view', 'id' => $object->house_id, 'aid' => $object->id]); ?>">повернутись</a>
    </div>
</div>
<?php AdminActiveForm::end(); ?>
