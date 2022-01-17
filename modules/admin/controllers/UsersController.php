<?php

namespace app\modules\admin\controllers;

use yii\helpers\Url;
use \Yii;
use app\components\AdminController;
use app\components\Auth;
use app\components\AutoLogin;
use app\models\Letters;
use app\models\Firms;
use app\models\Users;
use app\models\AccessLogic;

class UsersController extends AdminController {

    public function actionIndex($id = null) {
        $model = new Users;
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->searchUsers($id),
                    'client_id' => $id,
        ]);
    }

    public function actionUpdate($id = null) {
        $model = $id > 0 ? Users::findModel($id) : new Users();
        if (!$model->isNewRecord && (!Auth::isClient($model->role_id) || !$model->isAllowControl())) {
            return $this->redirect(['index']);
        }
        $model->setScenario('clients-control');
        if ($model->load(Yii::$app->request->post())) {
            $res = $model->saveModel();
            if ($res && $model->invite && !empty($model->email)) {
                $model->createAccountKey('+24 hour');
                $url = Url::toRoute(['/frontend/site/activate', 'id' => $model->id, 'key' => $model->key, 'uid' => Yii::$app->user->getId()], true);
                Letters::setupTask([
                    'to' => $model->email,
                    'subject' => 'Активація облікового запису',
                    'body' => $this->renderPartial('_letter_activate', ['url' => $url, 'time' => '24 годин']),
                ]);
                Yii::$app->session->setFlash('info', 'Запрошення надіслано користувачу.');
            }
            if ($res && $model->invite && empty($model->email)) {
                Yii::$app->session->setFlash('warning', 'Відправка запрошення не можлива, оскільки у користувача не вказаний email.');
            }
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                if (Yii::$app->request->post('action') === 'exit') {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            }
        }
        return $this->render('update', [
                    'model' => $model,
                    'firms' => Firms::keyvalAlt(),
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Users::findOne($id);
            if ($model && Auth::isClient($model->role_id) && $model->isAllowControl()) {
                $model->delete();
            }
            return;
        }
        return $this->redirect(['index']);
    }

    public function actionLoginas($id) {
        $model = Users::findOne($id);
        if ($model === null || !Auth::isClient($model->role_id) || !$model->isAllowControl()) {
            return $this->redirect(['index']);
        }
        AccessLogic::setLoginAs(Url::toRoute(['index']));
        Yii::$app->user->logout(false);
        AutoLogin::setCookie($model->id);
        $model->autoLogin();
        Yii::$app->user->setReturnUrl(null);
        return $this->redirect(Auth::defineHomeUrl());
    }

}
