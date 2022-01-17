<?php

namespace app\modules\admin\controllers;

use yii\helpers\Html;
use \Yii;
use app\components\AdminController;
use app\models\Category;
use app\models\Tasks;

class CategoryController extends AdminController {

    public function actionIndex() {
        $model = new Category;
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->search(),
        ]);
    }

    public function actionUpdate($id = null) {
        $model = Category::findModel($id);
        $model->initPosition();
        if ($model->isSubmitted()) {
            $res = Yii::transaction(function ()use ($model) {
                        return $model->saveModel();
                    });
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                Tasks::start(Tasks::REFRESH_CACHE, null, date('Y-m-d H:i:s', strtotime('+1 hour')));
                if (Yii::$app->request->post('action') === 'exit') {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            }
        }
        return $this->render('update', [
                    'model' => $model,
                    'positions' => $model->parent->getPositionList($id),
                    'parents' => $model->getParentList(),
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Category::findOne($id);
            if ($model && $model->isAllowDelete()) {
                $model->initPosition();
                Yii::transaction(function ()use ($model) {
                    return $model->deleteModel();
                });
                Tasks::start(Tasks::REFRESH_CACHE, null, date('Y-m-d H:i:s', strtotime('+1 hour')));
            }
            return;
        }
        return $this->redirect(['index']);
    }

    public function actionPositions($value, $id = null) {
        $model = Category::findOne($value);
        if ($model) {
            return Html::renderSelectOptions(null, $model->getPositionList($id));
        }
    }

}
