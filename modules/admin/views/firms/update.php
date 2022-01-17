<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\components\AdminActiveForm;
use app\components\Auth;
use yii\widgets\DetailView;

$this->params['breadcrumbs'] = [
    ['label' => 'клієнти', 'url' => 'index'],
    $this->title = $model->isNewRecord ? 'Профіль клієнта' : $model->getTitle(),
];
$form = AdminActiveForm::begin(['errorSummaryModels' => $model]);
?>
<div class="row">
    <div class="col-sm-12 col-md-6">
        <?php echo $form->field($model, 'title')->input('text'); ?>
    </div>
    <div class="col-sm-12 col-md-6">
        <?php echo $form->field($model, 'phone')->input('text'); ?>
    </div>
</div>
<?php
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'title',
        ['attribute' => 'phones', 'value' => implode(', ', $model->getPhones())],
        ['attribute' => 'price_type_id', 'value' => $model->getPriceTypeTitle()],
        ['attribute' => 'delivery_ukrmedias', 'value' => $model->isNewRecord ? '' : $model::$valuesTrueFalse[$model->delivery_ukrmedias]],
        ['attribute' => 'discount_groups', 'value' => $model->getAdminDiscountGroups()],
        ['attribute' => 'groups', 'value' => $model->getAdminGroups()],
        'manager_name',
        'manager_phone',
        'manager_mrt',
        'filter_status',
        'filter_assortment',
        'filter_tt',
        'filter_activity',
        'filter_discipline',
    ],
]);
?>
<hr/>
<div class="row">
    <div class="col-sm-12 col-md-6">
        <label>Закріплені менеджери:</label>
        <?php
        if (count($managers) === 0) {
            echo '<div class="text-muted">менеджери відсутні</div>';
        }
        foreach ($managers as $id => $title):
            echo $form->field($model, 'managers[' . $id . ']')->checkbox([
                'checked' => in_array($id, $model->managers),
                'label' => '<span class="badge border badge-pill col">' . $title . ' <i class="fa fa-check"></i></span>',
                'value' => $id]);
        endforeach;
        ?>
    </div>
</div>
<?php if (Yii::$app->user->can(Auth::ROLE_ADMIN)): ?>
    <div class="row mt-5">
        <div class="col-sm-12 col-md-6">
            <div class="col fixed-column">
                <div class="form-group mb-5">
                    <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">зберегти</button>
                </div>
                <div class="form-group mb-5">
                    <button class="btn btn-success btn-sm" type="submit" name="action" value="exit">зберегти та повернутись</button>
                </div>
                <div class="form-group mb-5">
                    <a class="btn btn-info btn-sm" href="<?php echo yii\helpers\Url::toRoute('index') ?>">повернутись</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php AdminActiveForm::end(); ?>