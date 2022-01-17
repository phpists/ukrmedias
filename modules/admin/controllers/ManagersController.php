<?php

namespace app\modules\admin\controllers;

use yii\helpers\Url;
use yii\filters\AccessControl;
use \Yii;
use app\components\AdminController;
use app\components\Auth;
use app\models\Users;
use app\models\Letters;
use app\models\Firms;
use app\models\AccessLogic;

class ManagersController extends AdminController {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => [Auth::ROLE_ADMIN],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $model = new Users;
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->searchManagers(),
        ]);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionUpdate($id = null) {
        $model = $id > 0 ? Users::findModel($id) : new Users();
        if (!$model->isNewRecord && !Auth::isManager($model->role_id)) {
            return $this->redirect(['index']);
        }
        $model->setScenario('managers-control');
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
                    'model' => $model
        ]);
    }

    public function actionSettings($id) {
        $model = Users::findOne($id);
        if ($model === null || !$model->isAllowSettings()) {
            return $this->redirect(['index']);
        }
        $model->setScenario('managers-control');
        $model->initSettings();
        if ($model->load(Yii::$app->request->post())) {
            $t = Yii::$app->db->beginTransaction();
            $res = $model->updateSettings();
            $res ? $t->commit() : $t->rollBack();
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                if (Yii::$app->request->post('action') === 'exit') {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['settings', 'id' => $model->id]);
                }
            }
        }
        return $this->render('settings', [
                    'clients' => Firms::keyval(),
                    'model' => $model
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Users::findOne($id);
            if ($model && $model->isAllowDelete() && Auth::isManager($model->role_id)) {
                $model->delete();
            }
            return;
        }
        return $this->redirect(['index']);
    }

//    public function actionLoginas($id) {
//        $user = Users::findOne($id);
//        if ($user === null || !$user->isAllowLoginAsManager() || !Auth::isManager($user->role_id)) {
//            return $this->redirect(['index']);
//        }
//        AccessLogic::setLoginAs(Url::toRoute(['index']));
//        Yii::$app->user->logout(false);
//        $user->autoLogin();
//        Yii::$app->user->setReturnUrl(null);
//        return $this->redirect(Auth::defineHomeUrl());
//    }

}
