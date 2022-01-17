<?php

namespace app\modules\admin\controllers;

use \Yii;
use app\components\AdminController;
use app\models\Users;

class ProfileController extends AdminController {

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionUpdate() {
        $model = Users::findOne(Yii::$app->user->getId());
        if ($model->load(Yii::$app->request->post())) {
            $res = $model->updateModel();
            if ($res) {
                Yii::$app->session->setFlash('success', 'Ваш профіль оновлено.');
                return $this->refresh();
            }
        }
        return $this->render('update', [
                    'model' => $model
        ]);
    }

    public function actionPassword() {
        $model = Users::findOne(Yii::$app->user->getId());
        $model->setScenario('password');
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->password <> '' && $model->pass <> '' && Yii::$app->security->validatePassword($model->password, $model->pass)) {
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

}
