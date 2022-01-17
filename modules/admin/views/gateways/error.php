<?php
$this->params['breadcrumbs'] = [
    $this->title = 'Шлюзи',
];
?>
<div class="alert alert-danger"><?php echo implode('<br/>', app\models\Lorawan::$errors); ?></div>