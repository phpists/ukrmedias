<?php

use yii\helpers\Html;
use app\components\AdminActionColumn;
use app\components\Auth;
use app\models\Devices;
use app\models\Services;

?>
<?php

$this->params['breadcrumbs'] = [
    $this->title = 'Фрейми',
];
echo app\components\AdminGrid::widget([
    'cols' => ['160px', '170px', '110px', '60px', '280px', '', '100px'],
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'columns' => [
        [
            'attribute' => 'date',
            'format' => 'raw',
            'value' => function($data) {
                return $data->getDate();
            },
            'filter' => false,
        ],
        [
            'attribute' => 'deveui',
            'format' => 'raw',
            'value' => function($data) {
                return $data->getDevUrl();
            },
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('deveui')],
        ],
        [
            'attribute' => 'devaddr',
            'format' => 'raw',
            'filter' => false,
        ],
        [
            'attribute' => 'port',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('port')],
        ],
        [
            'attribute' => 'data',
            'format' => 'raw',
            'filter' => false,
            'value' => function($data) {
                return $data->getAdminData();
            },
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function($data) {
                return $data->getStatus();
            },
            'filter' => false,
        ],
        [
            'label' => 'Інфо',
            'format' => 'raw',
            'value' => function($data) {
                return $data->getInfoTxt();
            },
            'filter' => false,
        ],
    ],
]);
