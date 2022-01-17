<?php

namespace app\modules\client\controllers;

use yii\helpers\Html;
use yii\helpers\Url;
use \Yii;
use app\components\ClientController;
use app\components\Misc;
use app\models\PreOrders;
use app\models\PreOrdersDetails;
use app\models\PreOrdersRuntimeData;
use app\models\ExcelViewer;
use app\models\PaymentVariants;
use app\models\DeliveryVariants;
use app\models\Settings;

class CartController extends ClientController {

    public function actionIndex() {
        $this->layout = '@app/modules/client/views/layouts/wide.php';
        $model = PreOrders::createDraft(Yii::$app->user->identity->firm_id, false);
        if ($model->load(Yii::$app->request->post())) {
            $res = Yii::transaction(function () use ($model) {
                        PreOrdersRuntimeData::clean();
                        $r = $model->validateStock();
                        if ($r) {
                            $model->saveDetails(Yii::$app->request->post('Goods'));
                        }
                        $r = $model->hasErrors() === false;
                        if ($r) {
                            $model->status_id = PreOrders::STATUS_NEW;
                            $r = $model->saveModel();
                        }
                        PreOrdersRuntimeData::save();
                        return $r;
                    });
            if ($res) {
                Yii::$app->session->setFlash('order-success');
                return $this->redirect(['/client/preorders/view', 'id' => $model->id]);
            }
        }
        return $this->render('index', [
                    'model' => $model,
                    'firm' => Yii::$app->user->identity->getFirm(),
                    'details' => $model->getDetails(),
                    'payments' => PaymentVariants::keyval(),
                    'delivery' => DeliveryVariants::keyvalClient(),
                    'minAmount' => Settings::get(Settings::ORDER_MIN_AMOUNT),
                    'toMin' => $model->getToMin(),
                    'runtimeData' => PreOrdersRuntimeData::get(),
        ]);
    }

    public function actionAdd() {
        if (!Yii::$app->request->isPost || !Yii::$app->request->isAjax) {
            Yii::$app->end();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::transaction(function () {
            $model = PreOrders::createDraft(Yii::$app->user->identity->firm_id);
            $model->saveDetails(Yii::$app->request->post('Goods'), false);
            return true;
        });
        return PreOrders::getCartSummary();
    }

    public function actionDel($id, $goods_id, $variant_id) {
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
        return Url::to(['index']);
    }

    public function actionViewExcel() {
        if (!is_file(ExcelViewer::getFname())) {
            return $this->redirect(['/client/preorders/index']);
        }
        $data = ExcelViewer::getPreviewData(Yii::$app->request->post('ExcelViewer'));
        if (Yii::$app->request->isAjax) {
            if (isset($data['sheet'])) {
                return $this->renderPartial('_xls_table', ['sheet' => $data['sheet']]);
            } else {
                return '';
            }
        }
        return $this->render("xls_section_{$data['section']}", $data);
    }

    public function actionImportExcel() {
        try {
            $settings = Yii::$app->request->post('ExcelViewer');
            $data = ExcelViewer::getData($settings);
            if (count($data) === 0) {
                return json_encode(['res' => false, 'message' => '<div class="alert alert-error">Не знайдено жодного товару.</div>']);
            }
            $model = PreOrders::importExcel($data, @$settings['clean']);
            if ($model->hasErrors()) {
                return json_encode(['res' => false, 'message' => Html::errorSummary($model, ['class' => 'alert alert-error skip-click', 'header' => false, 'showAllErrors' => true, 'encode' => false])]);
            } else {
                unlink(ExcelViewer::getFname());
                //Yii::$app->session->setFlash('order-success');
                return json_encode(['res' => true, 'url' => Url::to(['/client/cart/index'])]);
            }
        } catch (\Throwable $ex) {
            Yii::dump("actionImportExcel: {$ex->getFile()}({$ex->getLine()}):\n{$ex->getMessage()}\n" . ExcelViewer::getFname());
            return json_encode(['res' => false, 'message' => '<div class="alert alert-error">Помилка обробки файлу. ' . Misc::internalErrorMessage() . '</div>']);
        }
    }

}
