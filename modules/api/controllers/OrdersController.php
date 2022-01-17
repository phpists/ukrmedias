<?php

namespace app\modules\api\controllers;

use \Yii;
use app\models\Api;
use app\models\Orders;
use app\models\PreOrders;
use app\models\Firms;
use app\models\DeliveryVariants;
use app\models\DataHelper;

class OrdersController extends A_Controller {

    public function actionSave() {
        $keys = array_diff(array('request_id', 'status_id'), array_keys(Api::$data));
        if (Api::$data['status_id'] <> Orders::STATUS_REJECTED) {
            $keys = array_merge($keys, array_diff(array('id', 'number', 'delivery_id', 'date', 'amount', 'manager_note', 'firm_id', 'details'), array_keys(Api::$data)));
        }
        if (count($keys) > 0) {
            $this->responce(['res' => false, 'message' => 'Отсутствуют следующие данные: ' . implode(', ', $keys) . '.']);
        }
        if (Api::$data['status_id'] <> Orders::STATUS_REJECTED) {
            $this->isArray('details');
            $this->isValidArray([
                'details' => ['id', 'variant_id', 'qty', 'price', 'discount', 'title', 'brand', 'article', 'code', 'bar_code', 'color', 'size']
            ]);
        }
        $res = Yii::transaction(function () {
                    $preorder = Api::$data['request_id'] > 0 ? PreOrders::findOne(Api::$data['request_id']) : null;
                    if (Api::$data['request_id'] > 0 && $preorder === null) {
                        Api::$message = 'Заказ ' . Api::$data['number'] . ': заявки ' . Api::$data['request_id'] . ' не существует.';
                        return false;
                    }
                    if ($preorder !== null && Api::$data['status_id'] == Orders::STATUS_REJECTED) {
                        $preorder->status_id = PreOrders::STATUS_REJECTED;
                        $preorder->update(false, ['status_id']);
                        return true;
                    }
                    if ($preorder !== null) {
                        $preorder->status_id = PreOrders::STATUS_PROCEED;
                        $preorder->update(false, ['status_id']);
                    }

                    $firm = Firms::find()->where(['id_1s' => Api::$data['firm_id']])->one();
                    if ($firm === null) {
                        Api::$message = 'Заказ ' . Api::$data['number'] . ': дилера ' . Api::$data['firm_id'] . ' не существует.';
                        return false;
                    }
                    if (Api::$data['delivery_id'] <> '' && DeliveryVariants::find()->where(['id' => Api::$data['delivery_id']])->exists() === false) {
                        Api::$message = 'Заказ ' . Api::$data['number'] . ': варианта доставки ' . Api::$data['delivery_id'] . ' не существует.';
                        return false;
                    }
                    if (Api::$data['delivery_id'] == '') {
                        Api::$data['delivery_id'] = DeliveryVariants::UNDEFINED;
                    }
                    Api::$data['firm_id'] = $firm->id;
                    if (count(Api::$data['details']) === 0) {
                        Api::$message = 'Заказ ' . Api::$data['number'] . ': пустой заказ.';
                        return false;
                    }
                    $order = Orders::import(Api::$data, $preorder);
                    if ($order->hasErrors()) {
                        Api::$message = 'Заказ ' . $order->number . ': ' . implode(' ', $order->getErrorsList());
                        return false;
                    }
                    if ($order->oldStatusTxt !== null && $order->getStatus() <> $order->oldStatusTxt) {
                        DataHelper::notifyOrderStatus($order, $order->oldStatusTxt);
                    }
                    return true;
                });
        $this->logName = Api::$data['number'] . '-' . Api::$data['date'];
        return ['res' => $res, 'message' => Api::$message];
    }

    public function actionStatus() {
        $keys = array_diff(['id', 'status_id'], array_keys(Api::$data));
        if (count($keys) > 0) {
            $this->responce(['res' => false, 'message' => 'Отсутствуют следующие данные: ' . implode(', ', $keys) . '.']);
        }
        $res = Yii::transaction(function () {
                    $model = Orders::findOne(Api::$data['id']);
                    if ($model === null) {
                        Api::$message = 'Заказа с идентификатором ' . Api::$data['id'] . ' не существует.';
                        return false;
                    }
                    $oldStatusTxt = $model->getStatus();
                    $isChanged = $model->status_id <> Api::$data['status_id'];
                    $this->logName = $model->number;
                    $model->status_id = Api::$data['status_id'];
                    $model->status_date = date('Y-m-d');
                    $model->update(true, ['status_id', 'status_date']);
                    if ($model->hasErrors()) {
                        Api::$message = 'Заказ ' . $model->number . ': ' . implode(' ', $model->getErrorsList());
                        return false;
                    }
                    if ($isChanged) {
                        DataHelper::notifyOrderStatus($model, $oldStatusTxt);
                    }
                    return true;
                });
        return ['res' => $res, 'message' => Api::$message];
    }

    public function actionDelete() {
        $res = Yii::transaction(function () {
                    $model = Orders::findOne(Api::$data);
                    return $model === null ? true : $model->deleteModel();
                });
        $this->logName = Api::$data;
        return ['res' => $res, 'message' => Api::$message];
    }

}
