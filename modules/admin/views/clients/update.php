<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\components\AdminActiveForm;
use app\components\Auth;
use app\models\Services;

$this->params['breadcrumbs'] = [
    ['label' => 'Клієнти', 'url' => 'index'],
    $this->title = $model->isNewRecord ? 'Профіль клієнта' : $model->getTitle(),
];
$form = AdminActiveForm::begin(['errorSummaryModels' => $model]);
?>
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#main" role="tab" aria-controls="home" aria-selected="true">Профіль</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#region" role="tab" aria-controls="home" aria-selected="true">Регіон</a>
    </li>
    <?php if (Yii::$app->user->can(Auth::ROLE_SUPERADMIN)): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#managers" role="tab" aria-controls="profile" aria-selected="false">Адміністратори</a>
        </li>
    <?php endif; ?>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="main" role="tabpanel" aria-labelledby="home-tab">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="row">
                    <div class="col-sm-8">
                        <?php echo $form->field($model, 'title')->input('text'); ?>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <?php echo $form->field($model, 'balance')->input('text'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <?php echo $form->field($model, 'active')->dropDownList(['0' => 'обмежений', '1' => 'повний']); ?>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <?php echo $form->field($model, 'supervisor')->dropDownList($model::$valuesTrueFalse); ?>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <?php echo $form->field($model, 'api')->dropDownList($model::$valuesTrueFalse); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?php echo $form->field($model, 'code')->input('text'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?php echo $form->field($model, 'bank')->input('text'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?php echo $form->field($model, 'iban')->input('text'); ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <label>Послуги</label>
                <div class="row">
                    <?php foreach ($services as $id => $title): ?>
                        <div class="col-sm-12 col-md-6">
                            <?php
                            echo $form->field($model, 'services[' . $id . ']')->checkbox([
                                'checked' => in_array($id, $model->services),
                                'label' => '<span class="badge border badge-pill col">' . $title . ' <i class="fa fa-check"></i></span>',
                                'value' => $id]);
                            ?>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <?php
                            if ($bankDataModel->dataModels[$id]->tax === '0.00') {
                                $bankDataModel->dataModels[$id]->tax = '';
                            }
                            echo $form->field($bankDataModel->dataModels[$id], '[data][' . $id . ']tax')->label(false)->input('text', ['placeholder' => 'ціна за користування кабінетом']);
                            ?>
                        </div>
                        <div class="col-sm-12">
                            <hr class="mt-3 mb-3"/>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="region" role="tabpanel" aria-labelledby="profile-tab">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <?php echo $form->autocomplete($model, 'region_id', $regions, ['prompt' => 'обласний центр', 'onChange' => 'changeRegion(this)']); ?>
                <?php echo $form->autocomplete($model, 'area_id', $areas, ['prompt' => empty($this->region_id) ? '-' : 'районний центр', 'onChange' => 'loadCities(document.getElementById("clients-region_id"), this, "#clients-city_id")']); ?>
                <?php echo $form->autocomplete($model, 'city_id', $cities); ?>
            </div>
        </div>
    </div>
    <?php if (Yii::$app->user->can(Auth::ROLE_SUPERADMIN)): ?>
        <div class="tab-pane fade" id="managers" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <label>&nbsp;</label>
                    <?php
                    foreach ($managers as $id => $title):
                        echo $form->field($model, 'managers[' . $id . ']')->checkbox([
                            'checked' => in_array($id, $model->managers),
                            'label' => '<span class="badge border badge-pill col">' . $title . ' <i class="fa fa-check"></i></span>',
                            'value' => $id]);
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<div class="row mt-5">
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
<?php AdminActiveForm::end(); ?>
<script>
    function changeRegion(input) {
        loadAreas(input, "#clients-area_id", function () {
            loadCities(input, document.getElementById("clients-area_id"), "#clients-city_id");
        });
    }
</script>