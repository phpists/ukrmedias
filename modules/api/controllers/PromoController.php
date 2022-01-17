<?php

namespace app\modules\api\controllers;

use \Yii;
use app\models\Api;
use app\models\Promo;
use app\models\Tasks;

class PromoController extends A_Controller {

    public function actionSave() {
        $keys = array_diff(['id', 'date_from', 'date_to', 'goods'], array_keys(Api::$data));
        if (count($keys) > 0) {
            $this->responce(['res' => false, 'message' => 'Отсутствуют следующие данные: ' . implode(', ', $keys) . '.']);
        }
        $this->isArray('goods');
        $this->isValidArray(['goods' => ['id', 'discount']]);
        $res = Yii::transaction(function () {
                    $model = Promo::import(Api::$data);
                    if ($model->hasErrors()) {
                        Api::$message = 'Акция ' . $model->getRange() . ': ' . implode(' ', $model->getErrorsList());
                        return false;
                    }
                    $this->logName = $model->getRange();
                    return true;
                });
        if ($res) {
            Tasks::start(Tasks::SET_PROMO_GOODS, ['id' => Api::$data['id']]);
        }
        return ['res' => $res, 'message' => Api::$message];
    }

    public function actionDelete() {
        $res = Yii::transaction(function () {
                    $model = Promo::findOne(Api::$data);
                    if ($model === null) {
                        return true;
                    }
                    return $model->deleteModel();
                });
        $this->logName = Api::$data;
        if ($res) {
            Tasks::start(Tasks::SET_PROMO_GOODS);
        }
        return ['res' => $res, 'message' => ''];
    }

}
