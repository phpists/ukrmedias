<?php

namespace app\modules\api\controllers;

use \Yii;
use app\models\Api;

class ExportController extends A_Controller {

    public function actionOrders() {
        $data = [];
        return $data;
    }

    public function actionOrdersresult() {
        return ['res' => false, 'message' => 'Not implemented yet.'];
    }

}
