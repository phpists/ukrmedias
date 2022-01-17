<?php

namespace app\modules\client\controllers;

use \Yii;
use app\components\Auth;
use app\components\ClientController;
use app\models\Firms;
use app\models\Addresses;
use app\models\DeliveryVariants;
use app\models\PaymentVariants;
use app\models\Users;
use app\models\Slider;
use app\models\Promo;

class ProfileController extends ClientController {

    public function actionIndex() {
        $model = Users::findOne(Yii::$app->user->getId());
        $firm = Firms::findOne($model->firm_id);
        if ($model->load(Yii::$app->request->post()) && $firm->load(Yii::$app->request->post())) {
            $res = Yii::transaction(function ()use ($model, $firm) {
                        if (!$model->validate() || !$firm->validate()) {
                            return false;
                        }
                        $model->updateByClient();
                        $firm->updateByClient();
                        return true;
                    });
            if ($res) {
                Yii::$app->session->setFlash('success', 'Профіль оновлено.');
                return $this->refresh();
            }
        }
        return $this->render('index', [
                    'model' => $model,
                    'firm' => $firm,
                    'addresses' => Addresses::keyval(),
                    'deliveryVariants' => DeliveryVariants::keyvalClient(),
                    'paymentVariants' => PaymentVariants::keyval(),
        ]);
    }

    public function actionPassword() {
        $model = Users::findOne(Yii::$app->user->getId());
        $model->setScenario('password');
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (Yii::$app->security->validatePassword($model->password, $model->pass)) {
                $model->createPassword();
                Yii::$app->session->setFlash('success', 'Ваш пароль змінений.');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error', 'Поточний пароль не вірний.');
            }
        }
        return $this->render('password', [
                    'model' => $model
        ]);
    }

    public function actionDashboard() {
        $this->layout = '@app/modules/client/views/layouts/wide.php';
        return $this->render('dashboard', [
                    'promo' => Promo::findActual(),
                    'data' => Slider::findList(Slider::PLACE_MAINPAGE),
        ]);
    }

    public function actionRegistration() {
        $this->layout = '@app/modules/frontend/views/layouts/mini.php';
        $model = Users::findOne(Yii::$app->user->getId());
        $model->setScenario('registration');
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::transaction(function ()use ($model) {
                $model->registerAlt();
                $model->autoLogin();
                return true;
            });
            return $this->redirect(Auth::defineHomeUrl());
        }
        return $this->render('registration', [
                    'model' => $model,
        ]);
    }

}
