<?php

namespace app\modules\admin\controllers;

use yii\helpers\Url;
use yii\filters\AccessControl;
use \Yii;
use app\components\AdminController;
use app\components\Auth;
use app\models\Brands;

class BrandsController extends AdminController {

    public function actionIndex() {
        $model = new Brands;
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
    public function actionUpdate($id) {
        $model = Brands::findOne($id);
        if ($model === null) {
            return $this->redirect(['index']);
        }
        if ($model->isSubmitted()) {
            $res = $model->saveModel();
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
                    'model' => $model
        ]);
    }

}
