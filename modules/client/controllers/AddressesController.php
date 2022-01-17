<?php

namespace app\modules\client\controllers;

use \Yii;
use yii\helpers\Url;
use app\components\Auth;
use app\components\ClientController;
use app\models\Addresses;

class AddressesController extends ClientController {

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionIndex() {
        $model = new Addresses();
        //$model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'list' => $model->findList(),
        ]);
    }

    public function actionUpdate($id = null) {
        $model = Addresses::findModel($id);
        if ($model !== null && !$model->isNewRecord && !$model->isAllowUpdate()) {
            return $this->redirect(['index']);
        }
        $firm = Yii::$app->user->identity->getFirm();
        if ($model->load(Yii::$app->request->post())) {
            $res = Yii::transaction(function ()use ($model, $firm) {
                        $model->firm_id = $firm->id;
                        $r = $model->saveModel();
                        if ($r && $model->asDefault) {
                            $firm->default_addr_id = $model->id;
                            $firm->updateByClient();
                        }
                        return $r;
                    });
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                if (Yii::$app->request->post('action') === 'exit') {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            }
        };
        return $this->render('update', [
                    'model' => $model,
                    'firm' => $firm,
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Addresses::findOne($id);
            if ($model && $model->isAllowDelete()) {
                $model->deleteModel();
            }
            return Url::to(['index']);
        }
        return $this->redirect(['index']);
    }

}
