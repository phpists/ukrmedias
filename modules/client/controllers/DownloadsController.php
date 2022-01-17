<?php

namespace app\modules\client\controllers;

use \Yii;
use app\components\ClientController;
use app\models\Brands;
use app\models\Downloads;

class DownloadsController extends ClientController {

    public function actionIndex() {
        $model = new Downloads();
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'brands' => Downloads::getBrands(),
                    'types' => Downloads::$typeLabels,
                    'data' => $model->searchClient()->getModels(),
        ]);
    }

}
