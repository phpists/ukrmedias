<?php

namespace app\modules\admin\controllers;

use \Yii;
use yii\helpers\Url;
use app\components\AdminController;
use app\components\Auth;
use app\models\Firms;
use app\models\Users;
use app\models\AccessLogic;
use app\models\ManagersFirms;

class FirmsController extends AdminController {

    public function actionIndex() {
        $model = new Firms;
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->search(),
        ]);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionUpdate($id = null) {
        $model = $id > 0 ? Firms::findModel($id) : new Firms();
        if ($model->isNewRecord === false && $model->isAllowUpdate() === false) {
            return $this->redirect(['index']);
        }
        $model->initModel();
        if ($model->load(Yii::$app->request->post()) && $model->isAllowUpdate()) {
            $t = Yii::$app->db->beginTransaction();
            $res = $model->updateModel();
            if ($res && Yii::$app->user->can(Auth::ROLE_ADMIN)) {
                $model->saveManagers();
            }
            $res ? $t->commit() : $t->rollBack();
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
                    'managers' => Users::keyvalManagers(),
        ]);
    }

//    public function actionDelete($id) {
//        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
//            $model = Firms::findOne($id);
//            if ($model && $model->isAllowDelete()) {
//                $model->delete();
//            }
//            return;
//        }
//        return $this->redirect(['index']);
//    }

    public function actionLoginas($id) {
        $model = Firms::findOne($id);
        if ($model === null || !$model->isAllowControl()) {
            return $this->redirect(['index']);
        }
        $user = $model->findDefaultUser();
        if ($user === null) {
            Yii::$app->session->setFlash('warning', 'У клієнта "' . $model->getTitle() . '" немає користувачів для входу в кабінет.');
            return $this->redirect(['index']);
        }
        AccessLogic::setLoginAs(Url::toRoute(['/admin/firms/index']));
        Yii::$app->user->logout(false);
        $user->autoLogin();
        Yii::$app->user->setReturnUrl(null);
        return $this->redirect(Auth::defineHomeUrl());
    }

}
