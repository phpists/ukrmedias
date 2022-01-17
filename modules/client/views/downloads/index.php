<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use app\components\AppPjax;

$this->title = 'Завантаження';
?>
<div class="table">
    <div id="form" class="brands_choise">
        <!--form class="js_ajax_form" data-update="#downloads-list"-->
        <h4>Бренди</h4>
        <div class="brands">
            <label class="item Selected">
                <?php echo Html::activeRadio($model, 'brand_id', ['value' => '', 'label' => false, 'uncheck' => false, 'onChange' => 'updateList()']); ?>
                Усі бренди
            </label>
            <?php foreach ($brands as $id => $title): ?>
                <label class="item notSelected">
                    <?php echo Html::activeRadio($model, 'brand_id', ['value' => $id, 'label' => false, 'uncheck' => false, 'onChange' => 'updateList()']); ?>
                    <?php echo $title; ?>
                </label>
            <?php endforeach; ?>
        </div>
        <h4>Тип документу</h4>
        <div class="brands">
            <label class="item Selected">
                <?php echo Html::activeRadio($model, 'type_id', ['value' => '', 'label' => false, 'uncheck' => false, 'onChange' => 'updateList()']); ?>
                Усі документи
            </label>
            <?php foreach ($types as $id => $title): ?>
                <label class="item notSelected">
                    <?php echo Html::activeRadio($model, 'type_id', ['value' => $id, 'label' => false, 'uncheck' => false, 'onChange' => 'updateList()']); ?>
                    <?php echo $title; ?>
                </label>
            <?php endforeach; ?>
        </div>
        <!--/form-->
    </div>
    <?php AppPjax::begin(); ?>
    <div id="downloads-list" class="card_choise">
        <?php foreach ($data as $dataModel): ?>
            <a class="card js_brand_<?php echo $dataModel->brand_id; ?> js_type_<?php echo $dataModel->type_id; ?>" href="<?php echo $dataModel->getModelFiles('file')->getDownloadUrl('client'); ?>" data-pjax="0">
                <img src="<?php echo $dataModel->getModelFiles('cover')->getSrc('small'); ?>" alt=""/>
                <p><?php echo $dataModel->title; ?></p>
            </a>
        <?php endforeach; ?>
    </div>
    <?php AppPjax::end(); ?>
</div>
<script>
    function updateList() {
        var brand_id = $("#form").find("input[name*=brand_id]:checked").val();
        var type_id = $("#form").find("input[name*=type_id]:checked").val();
        $("#downloads-list").find("a.card").each(function () {
            var $item = $(this);
            if ((brand_id.length === 0 || brand_id.length > 0 && $item.hasClass("js_brand_".concat(brand_id))) && (type_id.length === 0 || type_id.length > 0 && $item.hasClass("js_type_".concat(type_id)))) {
                $item.show();
            } else {
                $item.hide();
            }
        });
    }
</script>