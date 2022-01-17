<?php

namespace app\modules\admin\controllers;

use yii\helpers\Url;
use yii\filters\AccessControl;
use \Yii;
use app\components\AdminController;
use app\components\Auth;
use app\models\PaymentVariants;

class PaymentVariantsController extends AdminController {

    public function actionIndex() {
        $model = new PaymentVariants;
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
    public function actionUpdate($id = null) {
        $model = $id > 0 ? PaymentVariants::findModel($id) : new PaymentVariants;
        if ($model->isSubmitted()) {
            $res = Yii::transaction(function () use ($model) {
                        return $model->saveModel();
                    });
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                if (Yii::$app->request->post('action') === 'exit') {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            }
        }
        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = PaymentVariants::findOne($id);
            if ($model) {
                return $model->deleteModel();
            }
            return;
        }
        return $this->redirect(['index']);
    }

}
