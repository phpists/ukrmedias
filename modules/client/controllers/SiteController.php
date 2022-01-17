<?php

namespace app\modules\client\controllers;

use \Yii;
use app\components\Misc;
use app\components\Auth;
use app\components\ClientController;

class SiteController extends ClientController {

    public function actionError() {
        $ex = Yii::$app->errorHandler->exception;
        $code = property_exists($ex, 'statusCode') ? $ex->statusCode : $ex->getCode();
        if ($ex instanceof \yii\web\ForbiddenHttpException) {
            return $this->redirect(Auth::defineHomeUrl());
        }
        return $this->render('error', [
                    'code' => $code,
                    'message' => YII_DEBUG ? $ex->getMessage() : Misc::internalErrorMessage()
        ]);
    }

}
