<?php

namespace app\modules\client\controllers;

use app\components\ClientController;
use app\models\Brands;

class BrandsController extends ClientController
{

    public function actionIndex()
    {
        $this->layout = '@app/modules/client/views/layouts/wide.php';
        return $this->render('index', [
            'data' => Brands::findList(),
        ]);
    }

}
