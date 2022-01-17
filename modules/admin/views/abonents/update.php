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
        <?php echo $form->field($model, 'role_id')->dropDownList(Auth::$roleLabelsAbonent); ?>
    </div>
    <div class="col-sm-12 col-md-4">
        <?php echo $form->field($model, 'active')->dropDownList($model::$statusLabels); ?>
    </div>
    <div class="col-sm-12 col-md-4">
        <?php echo $form->field($model, 'type_id')->dropDownList($model::$typeLabels, ['onChange' => 'changeType(this);']); ?>
    </div>
</div>
<div class="js_type_<?php echo Users::TYPE_LEGAL_PERSON; ?>" style="<?php if ($model->type_id != Users::TYPE_LEGAL_PERSON): ?>display:none;<?php endif; ?>">
    <div class="row">
        <div class="col-sm-12 col-md-4">
            <?php echo $form->field($model, 'title')->input('text'); ?>
        </div>
    </div>
</div>
<div>
    <div class="row">
        <div class="col-sm-12 col-md-4">
            <?php echo $form->field($model, 'second_name')->input('text'); ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?php echo $form->field($model, 'first_name')->input('text'); ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?php echo $form->field($model, 'middle_name')->input('text'); ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-4">
        <?php echo $form->field($model, 'email')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-4">
        <?php echo $form->field($model, 'phone')->input('text', ['placeholder' => '+38']); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-8 col-lg-4">
        <?php
        echo $form->field($model, 'invite')->checkbox([
            'checked' => $model->isNewRecord && !is_numeric($model->invite) || $model->invite,
            'label' => '<span class="badge border badge-pill col">' . $model->getAttributeLabel('invite') . ' <i class="fa fa-check"></i></span>',
        ]);
        ?>
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
<script>
    function changeType(input) {
        var $selected = $("[class*=js_type_" + input.value + "]").slideDown();
        $("[class*=js_type]").not($selected).slideUp();
    }
</script>