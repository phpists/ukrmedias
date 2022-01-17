<?php

namespace app\modules\api\controllers;

use \Yii;
use app\models\Api;
use app\models\Firms;

class FirmsController extends A_Controller {

    public function actionSave() {
        $keys = array_diff(['id_1s', 'title', 'phones', 'price_type_id', 'delivery_ukrmedias', 'discount_groups', 'groups', 'manager_name', 'manager_phone', 'manager_mrt', 'filter_status', 'filter_assortment', 'filter_tt', 'filter_activity', 'filter_discipline',], array_keys(Api::$data));
        if (count($keys) > 0) {
            $this->responce(['res' => false, 'message' => 'Отсутствуют следующие данные: ' . implode(', ', $keys) . '.']);
        }
        $this->isArray('phones', 'discount_groups', 'groups');
        $this->isValidArray([
            'discount_groups' => ['id', 'title', 'discount'],
            'groups' => ['id', 'title'],
        ]);
        $res = Yii::transaction(function () {
                    $firm = Firms::findModel(['id_1s' => Api::$data['id_1s']]);
                    Api::$data['to_1s'] = 1;
                    $r = $firm->saveModel(Api::$data);
                    if (!$r) {
                        Api::$message = implode(' ', $firm->getErrorsList());
                    }
                    return $r;
                });
        $this->logName = Api::$data['title'];
        return ['res' => $res, 'message' => Api::$message];
    }

    public function actionDelete() {
        $res = Yii::transaction(function () {
                    $firm = Firms::find()->where(['id_1s' => Api::$data])->one();
                    return $firm === null ? true : $firm->deleteModel();
                });
        $this->logName = Api::$data;
        return ['res' => $res, 'message' => ''];
    }

}
