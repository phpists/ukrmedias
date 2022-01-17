<?php

namespace app\modules\api\controllers;

use \Yii;
use app\models\Api;
use app\models\DeliveryVariants;

class DeliveryController extends A_Controller {

    public function actionSave() {
        $keys = array_diff(['id', 'title'], array_keys(Api::$data));
        if (count($keys) > 0) {
            $this->responce(['res' => false, 'message' => 'Отсутствуют следующие данные: ' . implode(', ', $keys) . '.']);
        }
        $res = Yii::transaction(function () {
                    $model = DeliveryVariants::import(Api::$data);
                    if ($model->hasErrors()) {
                        Api::$message = 'Доставка ' . $model->title . ': ' . implode(' ', $model->getErrorsList());
                        return false;
                    }
                    return true;
                });
        $this->logName = Api::$data['title'];
        return ['res' => $res, 'message' => ''];
    }

}
