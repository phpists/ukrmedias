<?php

use yii\helpers\Url;
use app\models\Users;

$this->title = 'Мої співробітники';
?>

<div class="table">
    <div class="add_employee">
        <a href="<?php echo Url::to(['update']); ?>">
            <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M15 0C15.5523 0 16 0.447715 16 1V2H17C17.5523 2 18 2.44772 18 3C18 3.55228 17.5523 4 17 4H16V5C16 5.55228 15.5523 6 15 6C14.4477 6 14 5.55228 14 5V4H13C12.4477 4 12 3.55228 12 3C12 2.44772 12.4477 2 13 2H14V1C14 0.447715 14.4477 0 15 0Z" fill=""/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5 13H13C15.7614 13 18 15.2386 18 18C18 18.5523 17.5523 19 17 19C16.4872 19 16.0645 18.614 16.0067 18.1166L15.9949 17.8237C15.907 16.3072 14.6928 15.093 13.1763 15.0051L13 15H5C3.34315 15 2 16.3431 2 18C2 18.5523 1.55228 19 1 19C0.447715 19 0 18.5523 0 18C0 15.3112 2.12231 13.1182 4.78311 13.0046L5 13Z" fill=""/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C10 2.55228 9.55228 3 9 3C8.23068 3 7.46466 3.2927 6.87868 3.87868C5.70711 5.05025 5.70711 6.94975 6.87868 8.12132C8.05025 9.29289 9.94975 9.29289 11.1213 8.12132C11.5118 7.7308 12.145 7.7308 12.5355 8.12132C12.9261 8.51185 12.9261 9.14501 12.5355 9.53553C10.5829 11.4882 7.41709 11.4882 5.46447 9.53553C3.51184 7.58291 3.51184 4.41709 5.46447 2.46447C6.44058 1.48835 7.72194 1 9 1C9.55229 1 10 1.44772 10 2Z" fill=""/>
            </svg>
            Додати співробітника
        </a>
    </div>
    <?php
    echo \app\components\AppListView::widget([
        'filterModel' => $model,
        'dataProvider' => $dataProvider,
        'layout' => '<div class="inform-table-wrap">{sorter}<div class="staff">{items}</div>{summary}{pager}</div>',
        'headerTpl' => '/layouts/_list_header',
        'headerAttrs' => ['second_name', 'active'],
        //'options' => ['class' => 'staff'],
        'itemView' => '_item',
    ]);
    ?>
</div>


