<?php

namespace app\modules\admin\controllers;

use \Yii;
use app\components\AdminController;
use app\models\Feedback;

class FeedbackController extends AdminController {

    public function actionIndex() {
        $model = new Feedback;
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->search(),
        ]);
    }

    public function actionStatus($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Feedback::findOne($id);
            if ($model && $model->isAllowControl()) {
                $model->toggleStatus();
            }
            Yii::$app->end();
        }
        return $this->redirect(['index']);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Feedback::findOne($id);
            if ($model && $model->isAllowControl() && $model->isAllowDelete()) {
                $model->delete();
            }
            return;
        }
        return $this->redirect(['index']);
    }

}
