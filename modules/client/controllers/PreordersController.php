<?php

namespace app\modules\client\controllers;

use yii\helpers\Url;
use \Yii;
use app\components\ClientController;
use app\components\Misc;
use app\models\PreOrders;
use app\models\PreOrdersDetails;
use app\models\Goods;
use app\models\Variants;

class PreordersController extends ClientController {

    public function actionIndex() {
        $model = new PreOrders();
        $model->setScenario('search');
        $dataProvider = $model->searchByClient();
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        $model = PreOrders::findOne($id);
        if ($model === null || !$model->isAllowView()) {
            return $this->redirect(['index']);
        }
        return $this->render('view', [
                    'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = PreOrders::findOne($id);
            if ($model && $model->isAllowDelete()) {
                $model->delete();
            }
            return Url::to(['index']);
        }
        return $this->redirect(['index']);
    }

    public function actionRepeate($id, $clean = null) {
        $model = PreOrders::findOne($id);
        if ($model === null || !$model->isAllowView()) {
            return $this->redirect(['index']);
        }
        $res = PreOrders::repeate($clean, $model->getDetails());
        if ($res) {
            return $this->redirect(['/client/cart/index']);
        }
        return $this->redirect(['index']);
    }

}
