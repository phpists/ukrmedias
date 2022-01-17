<?php

namespace app\modules\admin\controllers;

use \Yii;
use app\components\AdminController;
use app\models\AddrRegions;
use app\models\AddrAreas;
use app\models\AddrCities;

class CitiesController extends AdminController {

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        $model = new AddrCities;
        $dp = $model->search();
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $dp,
        ]);
    }

    public function actionUpdate($id = null) {
        $model = $id > 0 ? AddrCities::findModel($id) : new AddrCities();
        if ($model->load(Yii::$app->request->post())) {
            $t = Yii::$app->db->beginTransaction();
            $res = $model->saveModel();
            $res ? $t->commit() : $t->rollBack();
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
                    'regions' => AddrRegions::keyval(),
                    'areas' => AddrAreas::keyval($model->region_id),
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = AddrCities::findOne($id);
            if ($model !== null) {
                $t = Yii::$app->db->beginTransaction();
                $res = $model->deleteModel();
                $res ? $t->commit() : $t->rollBack();
            }
            return;
        }
        return $this->redirect(['index']);
    }

}
