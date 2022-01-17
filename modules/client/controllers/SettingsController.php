<?php

namespace app\modules\client\controllers;

use \Yii;
use app\components\ClientController;
use app\models\Firms;
use app\models\ClientsData;

class SettingsController extends ClientController {

    public function actionIndex() {
        $model = Firms::findOne(Yii::$app->user->identity->client_id);
        if ($model->load(Yii::$app->request->post())) {
            $res = $model->updateByClient();
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                return $this->refresh();
            }
        }
        return $this->render('index', [
                    'model' => $model,
                    'taxAmount' => ClientsData::getTaxAmount($model->id),
        ]);
    }

}
