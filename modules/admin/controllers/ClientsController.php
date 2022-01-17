<?php

namespace app\modules\admin\controllers;

use \Yii;
use yii\helpers\Url;
use app\components\AdminController;
use app\components\Auth;
use app\models\Clients;
use app\models\Services;
use app\models\Users;
use app\models\ClientsData;
use app\models\AccessLogic;
use app\models\Calculator;
use app\models\ManagersClients;
use app\models\AddrRegions;
use app\models\AddrAreas;
use app\models\AddrCities;

class ClientsController extends AdminController {

    public function actionIndex() {
        $model = new Clients;
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
        $model = $id > 0 ? Clients::findModel($id) : new Clients();
        if ($model->isNewRecord === false && $model->isAllowUpdate() === false) {
            return $this->redirect(['index']);
        }
        $bankDataModel = new ClientsData();
        $bankDataModel->setScenario('clients-control');
        $bankDataModel->client_id = $model->id;
        $model->initModel();
        $bankDataModel->initModel();
        if ($model->load(Yii::$app->request->post())) {
            $t = Yii::$app->db->beginTransaction();
            $isNew = $model->isNewRecord;
            $res = $model->saveModel();
            if ($res && Yii::$app->user->can(Auth::ROLE_SUPERADMIN)) {
                $model->saveManagers();
            }
            if ($res && $isNew && Yii::$app->user->identity->role_id == Auth::ROLE_ADMIN) {
                ManagersClients::set(Yii::$app->user->getId(), $model->id);
            }
            if ($res && $bankDataModel->load(Yii::$app->request->post())) {
                $bankDataModel->client_id = $model->id;
                $res = $bankDataModel->saveModel();
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
                    'services' => Services::$labels,
                    'managers' => Users::keyvalAdmins(),
                    'bankDataModel' => $bankDataModel,
                    'regions' => AddrRegions::keyval(),
                    'areas' => AddrAreas::keyval($model->region_id),
                    'cities' => AddrCities::keyval($model->region_id, $model->area_id),
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Clients::findOne($id);
            if ($model && $model->isAllowDelete()) {
                $model->delete();
            }
            return;
        }
        return $this->redirect(['index']);
    }

    public function actionLoginas($id) {
        $model = Clients::findOne($id);
        if ($model === null || !$model->isAllowControl()) {
            return $this->redirect(['index']);
        }
        $user = $model->findDefaultUser();
        if ($user === null) {
            Yii::$app->session->setFlash('warning', 'У клієнта "' . $model->getTitle() . '" немає користувачів для входу в кабінет.');
            return $this->redirect(['index']);
        }
        AccessLogic::setLoginAs(Url::toRoute(['/admin/clients/index']));
        Yii::$app->user->logout(false);
        $user->autoLogin();
        Yii::$app->user->setReturnUrl(null);
        return $this->redirect(Auth::defineHomeUrl());
    }

}
