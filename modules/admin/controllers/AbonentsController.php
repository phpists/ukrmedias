<?php

namespace app\modules\admin\controllers;

use yii\helpers\Url;
use \Yii;
use app\components\AdminController;
use app\components\Auth;
use app\models\AddrHouses;
use app\models\AddrFlats;
use app\models\Users;
use app\models\UsersObjects;
use app\models\Letters;
use app\models\AccessLogic;

class AbonentsController extends AdminController {

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionUpdate($id, $aid = null, $uid = null) {
        if ($aid > 0) {
            $object = AddrFlats::findOne($aid);
            if ($object === null || !$object->isAllowControl()) {
                return $this->redirect('/admin/addresses/index');
            }
        } else {
            $object = AddrHouses::findOne($id);
            if ($object === null || !AccessLogic::isAllowHouseAccess($id)) {
                return $this->redirect('/admin/addresses/index');
            }
        }
        $model = $uid > 0 ? Users::findModel($uid) : new Users();
        if (!$model->isNewRecord && !Auth::isAbonent($model->role_id)) {
            return $this->redirect(['/admin/addresses/view', 'id' => $id, 'aid' => $aid]);
        }
        $model->setScenario('abonents-control');
        if ($model->load(Yii::$app->request->post())) {
            $t = Yii::$app->db->beginTransaction();
            $res = $model->saveModel();
            if ($res) {
                UsersObjects::addAbonent($model->id, $object);
            }
            if ($res && $model->invite && !empty($model->email)) {
                $model->createAccountKey('+24 hour');
                $url = Url::toRoute(['/frontend/site/activate', 'id' => $model->id, 'key' => $model->key, 'uid' => Yii::$app->user->getId()], true);
                Letters::setupTask([
                    'to' => $model->email,
                    'subject' => 'Активація облікового запису',
                    'body' => $this->renderPartial('_letter_activate', ['url' => $url, 'time' => '24 годин']),
                ]);
                Yii::$app->session->setFlash('info', 'Лист для активації облікового запису надісланий користувачу.');
            }
            $res ? $t->commit() : $t->rollBack();
            if ($res && $model->invite && empty($model->email)) {
                Yii::$app->session->setFlash('warning', 'Активація облікового запису не можлива, оскільки у користувача не вказаний email.');
            }
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                if (Yii::$app->request->post('action') === 'exit') {
                    return $this->redirect(['/admin/addresses/view', 'id' => $id, 'aid' => $aid]);
                } else {
                    return $this->redirect(['update', 'id' => $id, 'aid' => $aid, 'uid' => $model->id]);
                }
            }
        }
        return $this->render('update', [
                    'object' => $object,
                    'model' => $model,
        ]);
    }

    public function actionDelete($id, $aid = null, $uid = null) {
        if ($aid > 0) {
            $object = AddrFlats::findOne($aid);
            if ($object === null || !$object->isAllowControl()) {
                return $this->redirect('/admin/addresses/index');
            }
        } else {
            $object = AddrHouses::findOne($id);
            if ($object === null || !AccessLogic::isAllowHouseAccess($id)) {
                return $this->redirect('/admin/addresses/index');
            }
        }
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Users::findOne($uid);
            if ($model && Auth::isAbonent($model->role_id)) {
                $model->delete();
            }
            return;
        }
        return $this->redirect(['/admin/addresses/view', 'id' => $id, 'aid' => $aid]);
    }

    public function actionRemove($id, $aid = null, $uid = null) {
        if ($aid > 0) {
            $object = AddrFlats::findOne($aid);
            if ($object === null || !$object->isAllowControl()) {
                return $this->redirect('/admin/addresses/index');
            }
        } else {
            $object = AddrHouses::findOne($id);
            if ($object === null || !AccessLogic::isAllowHouseAccess($id)) {
                return $this->redirect('/admin/addresses/index');
            }
        }
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Users::findOne($uid);
            if ($model && Auth::isAbonent($model->role_id)) {
                UsersObjects::delAbonent($uid, $object);
            }
            return;
        }
        return $this->redirect(['/admin/addresses/view', 'id' => $id, 'aid' => $aid]);
    }

    public function actionLoginas($id, $aid = null, $uid = null) {
        if ($aid > 0) {
            $object = AddrFlats::findOne($aid);
            if ($object === null || !$object->isAllowControl()) {
                return $this->redirect('/admin/addresses/index');
            }
        } else {
            $object = AddrHouses::findOne($id);
            if ($object === null || !AccessLogic::isAllowHouseAccess($id)) {
                return $this->redirect('/admin/addresses/index');
            }
        }
        $model = Users::findOne($uid);
        if ($model === null || !Auth::isAbonent($model->role_id)) {
            return $this->redirect(['/admin/addresses/view', 'id' => $id, 'aid' => $aid]);
        }
        AccessLogic::setLoginAs(Url::toRoute(['/admin/addresses/view', 'id' => $id, 'aid' => $aid]));
        Yii::$app->user->logout(false);
        $model->autoLogin();
        Yii::$app->user->setReturnUrl(null);
        return $this->redirect(Auth::defineHomeUrl());
    }

    public function actionApply($id, $aid = null) {
        if ($aid > 0) {
            $object = AddrFlats::findOne($aid);
            if ($object === null || !$object->isAllowControl()) {
                return $this->redirect('/admin/addresses/index');
            }
        } else {
            $object = AddrHouses::findOne($id);
            if ($object === null || !AccessLogic::isAllowHouseAccess($id)) {
                return $this->redirect('/admin/addresses/index');
            }
        }
        $model = new Users;
        $model->setScenario('abonent-apply');
        if ($model->load(Yii::$app->request->post())) {
            $res = UsersObjects::addAbonent($model->id, $object);
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                if (Yii::$app->request->post('action') === 'exit') {
                    return $this->redirect(['/admin/addresses/view', 'id' => $id, 'aid' => $aid]);
                } else {
                    return $this->redirect(['update', 'id' => $id, 'aid' => $aid, 'uid' => $model->id]);
                }
            }
        }
        return $this->render('apply', [
                    'object' => $object,
                    'model' => $model,
        ]);
    }

}
