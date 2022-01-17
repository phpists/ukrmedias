<?php

namespace app\modules\client\controllers;

use yii\helpers\Url;
use \Yii;
use app\components\ClientController;
use app\models\Brands;
use app\models\Category;
use app\models\Goods;

class BrandsController extends ClientController {

    public function actionIndex() {
        $this->layout = '@app/modules/client/views/layouts/wide.php';
        return $this->render('index', [
                    'data' => Brands::findList(),
        ]);
    }

}
