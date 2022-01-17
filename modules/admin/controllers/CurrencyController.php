<?php

namespace app\modules\admin\controllers;

use yii\helpers\Url;
use yii\filters\AccessControl;
use \Yii;
use app\components\AdminController;
use app\components\Auth;
use app\models\Currency;

class CurrencyController extends AdminController {

    public function actionIndex() {
        $model = new Currency;
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->search(),
        ]);
    }

}
