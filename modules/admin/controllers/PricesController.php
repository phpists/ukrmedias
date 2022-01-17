<?php

namespace app\modules\admin\controllers;

use \Yii;
use yii\helpers\Url;
use app\components\AdminController;
use app\components\Auth;
use app\models\PriceTypes;
use app\models\Goods;

class PricesController extends AdminController {

    public function actionIndex() {
        $model = new PriceTypes;
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->search(),
        ]);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionView($id) {
        $model = PriceTypes::findOne($id);
        if ($model === null) {
            return $this->redirect(['index']);
        }
        $goodsModel = new Goods();
        $goodsModel->setScenario('search');
        return $this->render('view', [
                    'model' => $model,
                    'goodsModel' => $goodsModel,
                    'dataProvider' => $goodsModel->searchByPriceType($id),
        ]);
    }

}
