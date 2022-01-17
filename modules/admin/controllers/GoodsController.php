<?php

namespace app\modules\admin\controllers;

use yii\helpers\Url;
use yii\filters\AccessControl;
use \Yii;
use app\components\AdminController;
use app\components\Auth;
use app\models\Goods;
use app\models\Category;

class GoodsController extends AdminController {

    public function actionIndex() {
        $model = new Goods;
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
        $model = Goods::findModel($id);
        if ($model->isSubmitted()) {
            $res = Yii::transaction(function () use ($model) {
                        return $model->updateModel();
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
                    'category' => Category::findModel($model->cat_id)->getTitle(),
        ]);
    }

}
