<?php

namespace app\modules\api\controllers;

use \Yii;
use app\components\Misc;
use app\models\Api;

class SystemController extends A_Controller {

    public function actionTest() {
        $hash = Api::getTestHash();
        return [
            'your_method' => strip_tags(Yii::$app->request->method),
            'your_dataset' => strip_tags((string) Api::$data['dataset']),
            'your_time' => strip_tags((string) Api::$data['time']),
            'your_signature' => strip_tags((string) Api::$data['signature']),
            'server_time' => date('Y-m-d H:i:s'),
            'test_pkey' => Api::TEST_PKEY,
            'calc_signature' => $hash,
            'time_check' => Api::checkTime(Api::$data['time'], true),
            'signature_check' => $hash === Api::$data['signature'],
        ];
    }

    public function actionError() {
        $ex = Yii::$app->errorHandler->exception;
        $url = Yii::$app->request->getUrl();
        return ['res' => false, 'message' => YII_DEBUG ? $ex->getMessage() : Misc::internalErrorMessage()];
    }

}
