<?php

namespace app\modules\client\controllers;

use \Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use app\components\Auth;
use app\components\ClientController;
use app\models\Users;
use app\models\Letters;

class ManagersController extends ClientController {

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionIndex() {
        $model = new Users();
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->searchUsers(Yii::$app->user->identity->firm_id),
        ]);
    }

    public function actionUpdate($id = null) {
        $model = Users::findModel($id);
        if ($model->isNewRecord === false && !$model->isAllowUpdate()) {
            return $this->redirect(['index']);
        }
        $model->setScenario('clients-control');
        if ($model->load(Yii::$app->request->post())) {
            $res = $model->saveByClient();
//            if ($res && $model->invite && !empty($model->email)) {
//                $model->createAccountKey('+24 hour');
//                $url = Url::toRoute(['/frontend/site/activate', 'id' => $model->id, 'key' => $model->key, 'uid' => Yii::$app->user->getId()], true);
//                Letters::setupTask([
//                    'to' => $model->email,
//                    'subject' => 'Активація облікового запису',
//                    'body' => $this->renderPartial('_letter_activate', ['url' => $url, 'time' => '24 годин']),
//                ]);
//                Yii::$app->session->setFlash('info', 'Лист для активації облікового запису надісланий користувачу.');
//            }
//            if ($res && $model->invite && empty($model->email)) {
//                Yii::$app->session->setFlash('warning', 'Активація облікового запису не можлива, оскільки у користувача не вказаний email.');
//            }
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
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Users::findOne($id);
            if ($model && $model->isAllowDelete()) {
                $model->deleteModel();
            }
            return Url::to(['index']);
        }
        return $this->redirect(['index']);
    }

}
