<?php

namespace app\modules\api\controllers;

use \Yii;
use app\models\Api;
use app\models\Orders;
use app\models\PreOrders;
use app\models\Firms;
use app\models\DeliveryVariants;
use app\models\DataHelper;

class PreordersController extends A_Controller {

    public function actionStatus() {
        $keys = array_diff(['id', 'status_id'], array_keys(Api::$data));
        if (count($keys) > 0) {
            $this->responce(['res' => false, 'message' => 'Отсутствуют следующие данные: ' . implode(', ', $keys) . '.']);
        }
        $res = Yii::transaction(function () {
                    $model = PreOrders::findOne(Api::$data['id']);
                    if ($model === null) {
                        Api::$message = 'Заявки с идентификатором ' . Api::$data['id'] . ' не существует.';
                        return false;
                    }
                    $this->logName = $model->id;
                    $model->status_id = Api::$data['status_id'];
                    $model->update(false, ['status_id']);
                    return true;
                });
        return ['res' => $res, 'message' => Api::$message];
    }

}
