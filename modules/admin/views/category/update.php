<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AdminActiveForm;

$this->params['breadcrumbs'] = [
    ['label' => 'Категорії', 'url' => 'index'],
    $this->title = $model->isNewRecord ? 'Нова категорія' : $model->title,
];
$form = AdminActiveForm::begin(['errorSummaryModels' => [$model, $model->getCpuModel()]]);
?>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#main" role="tab" aria-controls="home" aria-selected="true">Основні дані</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#seo" role="tab" aria-controls="home" aria-selected="true">SEO</a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="main" role="tabpanel" aria-labelledby="home-tab">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <?php echo $form->field($model, 'title')->input('text', ['disabled' => $model->isExternal()]); ?>
            </div>
            <div class="col-sm-12 col-md-6">
                <?php echo $form->field($model, 'title_alt')->input('text'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="form-group required">
                    <label class="control-label"><?php echo $model->getCpuModel()->getAttributeLabel('cpu'); ?></label>
                    <div class="input-group">
                        <?php echo Html::activeTextInput($model->getCpuModel(), 'cpu', ['class' => 'form-control']); ?>
                        <div class="input-group-append">
                            <span class="input-group-text cursor" onClick="createCpu('#category-title', '#cpu-cpu');">@</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-3">
                <?php echo $form->field($model->getCpuModel(), 'visible')->dropDownList($model::$valuesTrueFalse); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'parent_id')->dropDownList($parents, ['onChange' => 'loadOptions(this, "#category-position_id")', 'data-url' => Url::to(['positions', 'id' => $model->id])]);
                ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?php echo $form->field($model, 'position_id')->dropDownList($positions); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-3">
                <?php $model->getModelFiles('cover_1')->fileField(); ?>
            </div>
        </div>
    </div>
    <div class="tab-pane fade show" id="seo" role="tabpanel" aria-labelledby="home-tab">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <?php echo $form->field($model->CpuModel, 'meta_title')->input('text'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <?php echo $form->field($model->CpuModel, 'meta_keywords')->input('text'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <?php echo $form->field($model->CpuModel, 'meta_descr')->textarea(['rows' => 3]); ?>
            </div>
        </div>
    </div>
</div>
<br/>
<div class="fixed-column">
    <div class="form-group mt-5 mb-5">
        <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">зберегти</button>
    </div>
    <div class="form-group mb-5">
        <button class="btn btn-success btn-sm" type="submit" name="action" value="exit">зберегти та повернутись</button>
    </div>
    <div class="form-group mb-5">
        <a class="btn btn-info btn-sm" href="<?php echo yii\helpers\Url::to('index') ?>">повернутись</a>
    </div>
</div>
<?php AdminActiveForm::end(); ?>