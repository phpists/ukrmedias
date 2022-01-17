<?php

namespace app\modules\api\controllers;

use \Yii;
use app\models\Api;
use app\models\Currency;

class CurrencyController extends A_Controller {

    public function actionSave() {
        $keys = array_diff(['date', 'currency', 'rate'], array_keys(Api::$data));
        if (count($keys) > 0) {
            $this->responce(['res' => false, 'message' => 'Отсутствуют следующие данные: ' . implode(', ', $keys) . '.']);
        }

        $res = Yii::transaction(function () {
                    $model = Currency::import(Api::$data);
                    if ($model->hasErrors()) {
                        Api::$message = 'Курс валюты ' . $model->currency . ' (' . $model->date . ')' . ': ' . implode(' ', $model->getErrorsList());
                        return false;
                    }
                    return true;
                });
        $this->logName = Api::$data['currency'];
        return ['res' => $res, 'message' => ''];
    }

}
