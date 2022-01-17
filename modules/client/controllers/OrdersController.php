<?php

namespace app\modules\client\controllers;

use \Yii;
use app\components\ClientController;
use app\components\Misc;
use app\models\Orders;
use app\models\OrderXls;
use app\models\PreOrders;

class OrdersController extends ClientController {

    public function actionIndex() {
        $model = new Orders();
        $model->setScenario('search');
        $dataProvider = $model->searchByClient();
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        $model = Orders::findOne($id);
        if ($model === null || !$model->isAllowView()) {
            return $this->redirect(['index']);
        }
        return $this->render('view', [
                    'model' => $model,
        ]);
    }

    public function actionXls($id) {
        $model = Orders::findOne($id);
        if ($model === null || !$model->isAllowDownload()) {
            return $this->redirect(['index']);
        }

        $file = Yii::$app->getRuntimePath() . uniqid('/order_') . '.xlsx';
        OrderXls::create($file, $model);
        return Misc::sendFile($file, "Замовлення №{$model->getNumber()} - {$model->getDate()}.xlsx");
    }

    public function actionRepeate($id, $clean = null) {
        $model = Orders::findOne($id);
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
