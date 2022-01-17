<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\data\ArrayDataProvider;
use app\components\AdminActiveForm;
use app\components\AdminGrid;

$this->params['breadcrumbs'] = [
    ['label' => 'Товари', 'url' => 'index'],
    $this->title = $model->title,
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
            <div class="col-sm-12 col-md-9">
                <div class="form-group required">
                    <label class="control-label"><?php echo $model->getCpuModel()->getAttributeLabel('cpu'); ?></label>
                    <div class="input-group">
                        <?php echo Html::activeTextInput($model->getCpuModel(), 'cpu', ['class' => 'form-control', 'disabled' => true]); ?>
                        <?php echo Html::hiddenInput(null, $model->title, ['id' => 'goods-title']); ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-3">
                <?php echo $form->field($model->getCpuModel(), 'visible')->dropDownList($model::$valuesTrueFalse); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-6">
                <h5>Основні дані</h5>
                <?php
                echo DetailView::widget([
                    'model' => $model,
                    'attributes' => [['attribute' => 'cat_id', 'value' => $category], 'brand', 'title', 'article', 'code', 'qty_pack', 'weight', 'volume', 'price', 'pic'],
                ]);
                ?>
            </div>
            <div class="col-sm-12 col-md-6">
                <h5>Варіанти</h5>
                <?php
                echo AdminGrid::widget([
                    'showHeader' => false,
                    'layout' => '{items}',
                    'dataProvider' => new ArrayDataProvider(['allModels' => $model->getVariants()]),
                    'columns' => [
                        'size',
                        'color',
                        [
                            'value' => function ($model) {
                                return "{$model->getQty()} / {$model->getQtyAlt()} шт.";
                            }
                        ],
                        [
                            'value' => function ($model) {
                                return $model->getIsNewTxt();
                            }
                        ],
                    ]
                ]);
                ?>
            </div>
            <div class="col-sm-12 col-md-6">
                <h5>Параметри</h5>
                <?php
                echo AdminGrid::widget([
                    'showHeader' => false,
                    'layout' => '{items}',
                    'dataProvider' => new ArrayDataProvider(['allModels' => $model->getParams()]),
                    'columns' => [
                        'title',
                        [
                            'value' => function ($model) {
                                return $model->getValueTxt();
                            }
                        ],
                    ]
                ]);
                ?>
            </div>
        </div>
        <style>#video_preview_1 iframe, #video_preview_2 iframe{width:100%;}</style>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $form->field($model, 'video_1')->input('text', ['onChange' => 'videoPreview("#video_preview_1",this.value);']); ?>
            </div>
            <div class="col-sm-12 col-md-6" id="video_preview_1">
                <?php echo $model->video_1; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $form->field($model, 'video_2')->input('text', ['onChange' => 'videoPreview("#video_preview_2",this.value);']); ?>
            </div>
            <div class="col-sm-12 col-md-6" id="video_preview_2">
                <?php echo $model->video_2; ?>
            </div>
        </div>
        <div class="row">
            <?php //echo $model->getModelFiles('photos')->fileField(); ?>
            <?php foreach ($model->getModelFiles('photos')->findMulti() as $dataModel): ?>
                <div class="col-sm-12 col-md-2">
                    <img class="img-thumbnail rounded float-left" src="<?php echo $dataModel->getSrc('small'); ?>"/>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $model->getDescr(); ?>
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