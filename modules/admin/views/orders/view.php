<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->params['breadcrumbs'] = [
    ['label' => 'Замовлення', 'url' => 'index'],
    $this->title = "Замовлення № {$order->getNumber()} від {$order->getDate()}",
];
?>

<?php
echo app\components\AdminGrid::widget([
    'id' => 'order-details',
    'layout' => '{items}',
    'dataProvider' => $detailsDataProvider,
    'emptyText' => 'немає товарів',
    'showFooter' => true,
    'placeFooterAfterBody' => true,
    'columns' => [
        [
            'attribute' => 'brand',
            'format' => 'raw',
            'footer' => 'Всього:',
            'footerOptions' => ['style' => 'text-align:right;', 'colspan' => 5],
        ],
        [
            'attribute' => 'title',
            'format' => 'raw',
            'footerOptions' => ['style' => 'display:none;'],
            'value' => function ($model) {
                return $model->getAdminTitle();
            }
        ],
        [
            'header' => 'Артикул / Код',
            'format' => 'raw',
            'footerOptions' => ['style' => 'display:none;'],
            'value' => function ($model) {
                return $model->getAdminCode();
            }
        ],
        [
            'attribute' => 'qty',
            'format' => 'raw',
            'contentOptions' => ['class' => 'input-cell text-center'],
            'footerOptions' => ['style' => 'display:none;'],
        ],
        [
            'attribute' => 'price',
            'format' => 'raw',
            'filter' => false,
            'contentOptions' => ['class' => 'price righted'],
            'footerOptions' => ['style' => 'display:none;'],
            'value' => function ($model) {
                return $model->getPrice();
            },
        ],
        [
            'header' => 'Сума',
            'format' => 'raw',
            'filter' => false,
            'contentOptions' => ['class' => 'amount righted'],
            'value' => function ($model) {
                return $model->getAmount();
            },
            'footer' => $order->amount,
            'footerOptions' => ['id' => 'order-amount', 'style' => 'font-weight:bold;', 'class' => 'righted'],
        ],
    ],
]);
?>
<br/>
<?php
echo DetailView::widget([
    'model' => $order,
    'attributes' => [
        'number',
        ['attribute' => 'status_id', 'value' => $order->getStatus(), 'format' => 'raw'],
        ['attribute' => 'firm_id', 'value' => $order->getFirmTitle()], 'payment_title', 'delivery_title', 'region', 'city', 'address', 'consignee', 'name', 'phone',
        ['attribute' => 'client_note', 'value' => $order->getClientNote(), 'format' => 'raw'],
        ['attribute' => 'manager_note', 'value' => $order->getManagerNote(), 'format' => 'raw'],
        ['label' => 'Експрес-накладна', 'value' => $order->getNP_summary(), 'format' => 'raw', 'visible' => $order->isNp()],
    ],
]);
?>
<br/>
<div class="fixed-column">
    <div class="form-group mb-5">
        <a class="btn btn-info btn-sm" href="<?php echo yii\helpers\Url::toRoute('index') ?>">повернутись</a>
    </div>
</div>
