<?php

namespace app\modules\admin\controllers;

use yii\helpers\Url;
use \Yii;
use app\components\AdminController;
use app\models\Letters;
use app\models\Firms;
use app\models\PreOrders;
use app\models\PreOrdersDetails;
use app\models\Goods;
use app\models\Variants;

class PreordersController extends AdminController {

    public function actionIndex() {
        $model = new PreOrders;
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->searchByManager(),
        ]);
    }

    public function actionCreate($firm_id = null) {
        $firms = Firms::keyvalAlt();
        if ($firm_id === null) {
            return $this->render('create', [
                        'model' => new PreOrders(),
                        'firms' => $firms,
            ]);
        }
        if (!array_key_exists($firm_id, $firms)) {
            return $this->redirect(['create']);
        }
        $model = PreOrders::createDraft($firm_id);
        return $this->redirect(['update', 'id' => $model->id]);
    }

    public function actionUpdate($id) {
        $model = PreOrders::findOne($id);
        if ($model === null || !$model->isAllowUpdate()) {
            return $this->redirect(['index']);
        }
        $firm = Firms::findOne($model->firm_id);
        $goodsModel = new Goods();
        $goodsModel->setScenario('search');
        $goodsModel::$price_type_id = $firm->price_type_id;
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->post('action') === 'exit') {
                $model->status_id = PreOrders::STATUS_NEW;
            }
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
                    'order' => $model,
                    'goodsModel' => $goodsModel,
                    'dataProvider' => $goodsModel->searchWithVariants(),
                    'detailsDataProvider' => PreOrdersDetails::search($model->id),
        ]);
    }

    public function actionView($id) {
        $model = PreOrders::findOne($id);
        if ($model === null || !$model->isAllowView()) {
            return $this->redirect(['index']);
        }
        return $this->render('view', [
                    'order' => $model,
                    'detailsDataProvider' => PreOrdersDetails::search($model->id),
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = PreOrders::findOne($id);
            if ($model && $model->isAllowDelete()) {
                $model->delete();
            }
            return;
        }
        return $this->redirect(['index']);
    }

    public function actionAddGoods($id, $goods_id) {
        if (!Yii::$app->request->isPost || !Yii::$app->request->isAjax || strpos($goods_id, '-') === false) {
            Yii::$app->end();
        }
        $model = PreOrders::findOne($id);
        if ($model === null || !$model->isAllowUpdate()) {
            Yii::$app->end();
        }
        list($goods_id, $variant_id) = explode('-', $goods_id);
        $goods = Goods::findOne($goods_id);
        $variant = Variants::findOne($variant_id);
        if ($goods === null || $variant === null) {
            Yii::$app->end();
        }
        $firm = Firms::findOne($model->firm_id);
        Goods::$price_type_id = $firm->price_type_id;
        Yii::transaction(function ()use ($model, $id, $goods, $variant) {
            $details = PreOrdersDetails::findModel(['doc_id' => $id, 'goods_id' => $goods->id, 'variant_id' => $variant->id]);
            $details->saveModel($id, (int) Yii::$app->request->post('qty'), $goods, $variant);
            $model->updateAmount(false);
            return true;
        });
    }

    public function actionDelGoods($id, $goods_id, $variant_id) {
        if (!Yii::$app->request->isPost || !Yii::$app->request->isAjax) {
            Yii::$app->end();
        }
        $model = PreOrders::findOne($id);
        if ($model === null || !$model->isAllowUpdate()) {
            Yii::$app->end();
        }
        Yii::transaction(function ()use ($model, $id, $goods_id, $variant_id) {
            PreOrdersDetails::deleteAll(['doc_id' => $id, 'goods_id' => $goods_id, 'variant_id' => $variant_id]);
            $model->updateAmount(false);
            return true;
        });
    }

}
