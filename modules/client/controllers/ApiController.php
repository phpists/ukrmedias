<?php

namespace app\modules\client\controllers;

use \Yii;
use app\components\ClientController;
use app\components\Auth;
use app\models\Clients;
use app\models\Api;

class ApiController extends ClientController {

    public function actionIndex() {
        Api::$client = $model = Clients::findOne(Yii::$app->user->identity->client_id);
        if (!$model->isAllowApi()) {
            return $this->redirect(Auth::defineHomeUrl());
        }
        Api::$client->initModel();
        if ($model->load(Yii::$app->request->post())) {
            $res = $model->updateByClient();
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                return $this->refresh();
            }
        }
        return $this->render('index', [
                    'model' => $model,
        ]);
    }

    public function actionApiKey() {
        return Yii::$app->security->generateRandomString(64);
    }

}
