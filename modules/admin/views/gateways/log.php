<?php

use yii\helpers\Html;
use app\components\AdminActionColumn;
use app\components\Auth;
use app\models\Gateways;
?>
<?php

$this->params['breadcrumbs'] = [
    ['label' => 'Шлюзи', 'url' => 'index'],
    $this->title = 'Журнал ' . $model->mac,
];
echo app\components\AdminGrid::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'fixed-column'],
    'columns' => [
        [
            'attribute' => 'date',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getDate();
            },
        ],
        [
            'attribute' => 'status_id',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getStatus();
            },
            'filter' => false,
        ],
    ],
]);
?>
