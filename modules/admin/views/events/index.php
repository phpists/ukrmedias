<?php

use yii\helpers\Html;
use app\components\AdminActionColumn;

?>
<?php

$this->params['breadcrumbs'] = [
    $this->title = 'Журнал подій',
];
echo app\components\AdminGrid::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
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
            'header' => 'Адреса',
            'attribute' => 'search_city',
            'value' => function($data) {
                return $data->getAddress();
            },
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Адреса'],
        ],
        [
            'attribute' => 'info',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('info')],
        ],
        [
            'attribute' => 'clients',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('clients')],
        ],
    ],
]);
?>