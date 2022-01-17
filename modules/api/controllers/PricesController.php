<?php

namespace app\modules\api\controllers;

use \Yii;
use app\models\Api;
use app\models\PriceTypes;

class PricesController extends A_Controller {

    public function actionSave() {
        $keys = array_diff(['id', 'title'], array_keys(Api::$data));
        if (count($keys) > 0) {
            $this->responce(['res' => false, 'message' => 'Отсутствуют следующие данные: ' . implode(', ', $keys) . '.']);
        }
        $res = Yii::transaction(function () {
                    $model = PriceTypes::importModel(Api::$data);
                    if ($model->hasErrors()) {
                        Api::$message = 'Ценовая группа ' . $model->title . ': ' . implode(' ', $model->getErrorsList());
                        return false;
                    }
                    return true;
                });
        $this->logName = Api::$data['title'];
        return ['res' => $res, 'message' => ''];
    }

    public function actionDelete() {
        $res = Yii::transaction(function () {
                    $model = PriceTypes::findOne(Api::$data);
                    if ($model === null) {
                        return true;
                    }
                    $this->logName = $model->title;
                    return $model->deleteModel();
                });
        return ['res' => $res, 'message' => ''];
    }

}
