<?php

namespace app\modules\admin\controllers;

use \Yii;
use app\components\AdminController;
use app\models\Pages;
use app\models\Slider;

class SlidersController extends AdminController {

    public function actionIndex() {
        $model = new Slider;
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->search(),
                    'route' => ['index'],
        ]);
    }

    public function actionUpdate($id = null) {
        $model = Slider::findModel($id);
        if ($model->isSubmitted()) {
            $res = Yii::transaction(function ()use ($model) {
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
            $model = Slider::findOne($id);
            if ($model) {
                Yii::transaction(function ()use ($model) {
                    return $model->deleteModel();
                });
            }
            return;
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }

    public function actionUpdatePos() {
        Slider::updatePos((array) Yii::$app->request->post('ids'));
    }

}
