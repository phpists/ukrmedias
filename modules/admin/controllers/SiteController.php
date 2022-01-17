<?php

namespace app\modules\admin\controllers;

use \Yii;
use app\components\Auth;
use app\components\Misc;
use app\components\AdminController;

class SiteController extends AdminController {

    public function behaviors() {
        return [];
    }

    public function actionError() {
        $ex = Yii::$app->errorHandler->exception;
        $code = property_exists($ex, 'statusCode') ? $ex->statusCode : $ex->getCode();
//        if ($code == 0) {
//            //Yii::dump($ex->getMessage());
//            #Yii::dump($_GET);
//            #Yii::dump($_POST);
//            #Yii::dump($_SERVER);
//        }
        if ($ex instanceof \yii\web\ForbiddenHttpException) {
            return $this->redirect(Auth::defineHomeUrl());
        }
        return $this->render('error', [
                    'code' => $code,
                    'message' => YII_DEBUG ? $ex->getMessage() : Misc::internalErrorMessage()
        ]);
    }

}
