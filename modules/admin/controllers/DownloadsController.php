<?php

namespace app\modules\admin\controllers;

use \Yii;
use app\components\AdminController;
use app\models\Brands;
use app\models\Downloads;

class DownloadsController extends AdminController {

    public function actionIndex() {
        $model = new Downloads;
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
        $model = Downloads::findModel($id);
        if ($model === null) {
            return $this->redirect(['index']);
        }
        if ($model->load(Yii::$app->request->post())) {
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
                    'model' => $model,
                    'brands' => Brands::keyval(),
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Downloads::findOne($id);
            if ($model) {
                Yii::transaction(function () use ($model) {
                    return $model->deleteModel();
                });
            }
            return;
        }
        return $this->redirect(['index']);
    }

}
