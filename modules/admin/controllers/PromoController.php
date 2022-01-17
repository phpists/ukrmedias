<?php

namespace app\modules\admin\controllers;

use \Yii;
use yii\helpers\Url;
use app\components\AdminController;
use app\components\Auth;
use app\models\Promo;
use app\models\Goods;

class PromoController extends AdminController {

    public function actionIndex() {
        $model = new Promo;
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->search(),
        ]);
    }

    public function actionUpdate($id) {
        $model = Promo::findOne($id);
        if ($model === null) {
            return $this->redirect(['index']);
        }
        if (Yii::$app->request->isPost) {
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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionView($id) {
        $model = Promo::findOne($id);
        if ($model === null) {
            return $this->redirect(['index']);
        }
        $goodsModel = new Goods();
        $goodsModel->setScenario('search');
        return $this->render('view', [
                    'model' => $model,
                    'goodsModel' => $goodsModel,
                    'dataProvider' => $goodsModel->searchByPromo($id),
        ]);
    }

}
