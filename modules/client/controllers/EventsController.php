<?php

namespace app\modules\client\controllers;

use \Yii;
use app\components\ClientController;
use app\models\Events;

class EventsController extends ClientController {

    public function actionIndex() {
        $model = new Events();
        $model->setScenario('search');
        $dataProvider = $model->searchClients();
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionToggleStatus() {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Events::findOne(Yii::$app->request->post('id'));
            if ($model) {
                $model->toggleStatus();
            }
            return;
        }
        return $this->redirect(['index']);
    }

}
