<?php

namespace app\modules\admin\controllers;

use \Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use app\components\AdminController;
use app\components\Auth;
use app\models\Lorawan;
use app\models\Gateways;
use app\models\GatewaysLog;

class GatewaysController extends AdminController {

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        $model = new Gateways;
        $dp = $model->search();
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $dp,
        ]);
    }

    public function actionUpdate($id = null) {
        $model = Gateways::findModel($id);
        if (!$model->isNewRecord && !$model->isAllowUpdate()) {
            return $this->redirect(['index']);
        }
        if ($model->load(Yii::$app->request->post())) {
            $res = $model->saveModel();
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                if (Yii::$app->request->post('action') === 'exit') {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $model->mac]);
                }
            }
        }
        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Gateways::findOne($id);
            if ($model !== null && $model->isAllowDelete()) {
                $model->deleteModel();
            }
            return;
        }
        return $this->redirect(['index']);
    }

    public function actionLog($id) {
        $model = new GatewaysLog();
        $model->mac = $id;
        return $this->render('log', [
                    'model' => $model,
                    'dataProvider' => $model->search(),
        ]);
    }

    public function actionGetinfo($id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Lorawan::get("/api/gateways/{$id}");
        if ($data === false) {
            Yii::$app->end();
        }
        $gateway = Gateways::findOne($id);
        if ($gateway !== null && !$gateway->isAllowUpdate()) {
            Yii::$app->end();
        }
        return $data;
    }

}
