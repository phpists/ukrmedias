<?php

namespace app\modules\client\controllers;

use yii\helpers\Url;
use yii\helpers\Html;
use \Yii;
use app\components\ClientController;
use app\models\AccessLogic;
use app\models\Calculator;
use app\models\AddrHouses;
use app\models\AddrFlats;
use app\models\Clients;
use app\models\Services;
use app\models\Factory;
use app\models\Devices;
use app\models\Tasks;
use app\models\CalculatorSettingsHouses;
use app\models\CalculatorSettingsFlats;
use app\models\Hardware;

class CalculationsController extends ClientController {

    public function actionIndex() {
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel();
        $model = new AddrHouses();
        $model->setScenario('search');
        $dataProvider = $model->search($client);
        if (Yii::$app->request->isAjax || $dataProvider->totalCount > 1 /* || $dataProvider->totalCount === 1 && !Clients::checkServiceExists(Yii::$app->user->identity->client_id) */) {
            return $this->render('index', [
                        'model' => $model,
                        'dataProvider' => $dataProvider,
                        'service_id' => current($client->houseServices),
            ]);
        } elseif ($dataProvider->totalCount === 1) {
            $house = current($dataProvider->getModels());
            return $this->redirect(['period', 'id' => $house->id, 'aid' => current($client->houseServices)]);
        } else {
            return $this->render('/houses/index_none');
        }
    }

    public function actionPeriod($id, $aid) {
        $house = AddrHouses::findOne($id);
        if ($house === null || !$house->isMy()) {
            return $this->redirect(['index']);
        }
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
        if (count($client->houseServices) === 0) {
            return $this->redirect(['index']);
        }
        if (!in_array($aid, $client->houseServices)) {
            return $this->redirect(['period', 'id' => $id, 'aid' => current($client->houseServices)]);
        }
        $model = new Calculator();
        $model->setScenario('search');
        $dataProvider = $model->search($house->id);
        return $this->render('period', [
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                    'house' => $house,
                    'client' => $client,
                    'service_id' => $aid,
        ]);
    }

    public function actionDetails($id, $aid, $sid, $fid = null) {
        $house = AddrHouses::findOne($aid);
        if ($house === null || !$house->isMy()) {
            return $this->redirect(['index']);
        }
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
        if (count($client->houseServices) === 0) {
            return $this->redirect(['index']);
        }
        if (!in_array($sid, $client->houseServices)) {
            return $this->redirect(['details', 'id' => $id, 'aid' => $aid, 'sid' => current($client->houseServices)]);
        }
        $calculator = Calculator::findOne(['date' => $id, 'client_id' => Yii::$app->user->identity->client_id, 'house_id' => $house->id]);
        if ($calculator === null || !$calculator->isAllowMake($sid) && !$calculator->isCreated($sid)) {
            return $this->redirect(['period', 'id' => $id, 'aid' => $sid]);
        }
        $flat = $fid === null ? null : AddrFlats::findOne($fid);
        $settingsHouse = new CalculatorSettingsHouses();
        $settingsHouse->client_id = Yii::$app->user->identity->client_id;
        $settingsHouse->house_id = $house->id;
        $settingsHouse->date = $id;
        $settingsHouse->allowedServices = $client->services;
        $settingsHouse->initData(true);
        if ($flat) {
            $settings = new CalculatorSettingsFlats();
            $settings->client_id = Yii::$app->user->identity->client_id;
            $settings->flat_id = $flat->id;
            $settings->date = $id;
            $settings->house_id = $house->id;
            $settings->allowedServices = $client->services;
            $settings->initData(true);
            $res = $this->_save($client, $calculator, $settings, $sid);
        } else {
            $res = $this->_save($client, $calculator, $settingsHouse, $sid);
        }
        if ($res === true) {
            Yii::$app->session->setFlash('success', 'Інформація записана.');
            return $this->refresh();
        }
        if ($flat) {
            return $this->render('details_flat', [
                        'calculator' => $calculator,
                        'house' => $house,
                        'services' => $client->houseServices,
                        'settings' => $settings,
                        'settingsHouse' => $settingsHouse,
                        'flat' => $flat,
                        'abonents' => $house->getAbonentsByFlats(),
                        //'hasCoolWaterCounter' => Devices::find()->where(['flat_id' => $flat->id, 'service_id_1' => Services::ID_COOL_WATER])->orWhere(['flat_id' => $flat->id, 'service_id_2' => Services::ID_COOL_WATER])->exists(),
                        //'hasHotWaterCounter' => Devices::find()->where(['flat_id' => $flat->id, 'service_id_1' => Services::ID_HOT_WATER])->orWhere(['flat_id' => $flat->id, 'service_id_2' => Services::ID_HOT_WATER])->exists(),
                        'service_id' => $sid,
                        'hardware' => Hardware::findByPeriod($flat->house_id, $flat->id, $sid, $calculator->date),
            ]);
        } else {
            return $this->render('details_house', [
                        'calculator' => $calculator,
                        'house' => $house,
                        'services' => $client->houseServices,
                        'settings' => $settingsHouse,
                        'abonents' => $house->getAbonentsByFlats(),
                        'service_id' => $sid,
                        'hardware' => Hardware::findByPeriodHouse($house->id, $sid, $calculator->date),
            ]);
        }
    }

    protected function _save($client, $calculator, $settings, $service_id) {
        if ($settings->load(Yii::$app->request->post())) {
            if (!$calculator->isAllowEdit($service_id) || $client->isSupervisor()) {
                return $this->refresh();
            }
            $t = Yii::$app->db->beginTransaction();
            $res = $settings->saveModel($calculator);
            $res ? $t->commit() : $t->rollBack();
            return $res;
        }
    }

    public function actionSetQty($date, $id, $sid) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $house = AddrHouses::findOne($id);
        if ($house === null || !$house->isMy()) {
            return ['res' => false, 'qty' => ''];
        }
        $calculator = Calculator::findOne(['date' => $date, 'client_id' => Yii::$app->user->identity->client_id, 'house_id' => $house->id]);
        if ($calculator === null || !$calculator->isAllowMake($sid)) {
            return ['res' => false, 'qty' => ''];
        }
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
        if (!in_array($sid, $client->houseServices) || $client->isSupervisor()) {
            return ['res' => false, 'qty' => ''];
        }
        $model = CalculatorSettingsHouses::getDefaultModel($date, $client->id, $id, $sid);
        $model->value = Yii::$app->request->post('qty');
        $res = $model->save();
        return ['res' => $res, 'qty' => $model->value];
    }

    public function actionPreview($id, $aid, $sid, $period, $fid = null) {
        $house = AddrHouses::findOne($aid);
        if ($house === null || !$house->isMy() || AccessLogic::isLoginAs() == false) {
            return $this->redirect(['index']);
        }
        $calculator = Calculator::findOne(['date' => $id, 'client_id' => Yii::$app->user->identity->client_id, 'house_id' => $house->id]);
        if ($calculator === null) {
            return $this->redirect(['index']);
        }
        if (!CalculatorSettingsHouses::find()->where(['date' => $id, 'house_id' => $house->id])->exists()) {
            return $this->redirect(['details', 'id' => $id, 'aid' => $aid, 'sid' => $sid]);
        }
        if (!in_array($period, ['month'])) {
            return $this->redirect(['details', 'id' => $id, 'aid' => $aid, 'sid' => $sid]);
        }
        $flat = AddrFlats::findOne($fid);
        //$flatList = $house->getFlats();
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
        if (!in_array($sid, $client->houseServices)) {
            return $this->redirect(['details', 'id' => $id, 'aid' => $aid, 'sid' => current($client->houseServices)]);
        }
        if ($period === 'month') {
            $from = $calculator->date;
            $to = date('Y-m-t', strtotime($calculator->date));
        } else {
            $from = date('Y-01-01', strtotime($calculator->date));
            $to = date('Y-12-31', strtotime($calculator->date));
        }
        Factory::$calculator = $calculator;
        $house->setFlats($house->getClientsFlats());
        Factory::getCalculator()->preview($house, $from, $to, $sid);
        return $this->render('preview', [
                    'calculator' => $calculator,
                    'house' => $house,
                    'client' => $client,
                    'flat' => $flat,
                    'service_id' => $sid,
                    'period' => $period,
                    'services' => $client->services,
                    'abonents' => $house->getAbonentsByFlats(),
        ]);
    }

    public function actionStart($id, $aid, $sid) {
        $house = AddrHouses::findOne($aid);
        if ($house === null || !$house->isMy()) {
            return $this->redirect(['index']);
        }
        $calculator = Calculator::findOne(['date' => $id, 'client_id' => Yii::$app->user->identity->client_id, 'house_id' => $house->id]);
        if ($calculator === null || !$calculator->isAllowMake($sid)) {
            return $this->redirect(['period', 'id' => $aid, 'aid' => $sid]);
        }
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
        if (!in_array($sid, $client->houseServices) || $client->isSupervisor()) {
            return $this->redirect(['period', 'id' => $aid, 'aid' => $sid]);
        }
        $res = $calculator->checkSettings($house, $sid);
        if ($res) {
            $calculator->start($house->id, $sid);
        }
        if ($res) {
            Yii::$app->session->setFlash('success', "Розрахунок за {$calculator->getPeriod()} для послуги \"" . Services::$labels[$sid] . "\" розпочато.");
        } else {
            Yii::$app->session->setFlash('error', Html::errorSummary($calculator, ['showAllErrors' => true, 'header' => false, 'footer' => false, 'encode' => false]));
        }
        return $this->redirect(['period', 'id' => $aid, 'aid' => $sid]);
    }

    public function actionSend($id, $aid, $sid) {
        $house = AddrHouses::findOne($aid);
        if ($house === null || !$house->isMy()) {
            return $this->redirect(['index']);
        }
        $calculator = Calculator::findOne(['date' => $id, 'client_id' => Yii::$app->user->identity->client_id, 'house_id' => $house->id]);
        if ($calculator === null || !$calculator->isAllowSend($sid)) {
            return $this->redirect(['period', 'id' => $aid, 'aid' => $sid]);
        }
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
        if (!in_array($sid, $client->houseServices) || $client->isSupervisor()) {
            return $this->redirect(['period', 'id' => $aid, 'aid' => $sid]);
        }
        $calculator->{"service_{$sid}"} = Calculator::STATUS_INVOICES_SENT;
        $calculator->{"info_{$sid}"} = 'Триває розсилка рахунків.';
        $calculator->update(false);
        Yii::$app->session->setFlash('success', 'Розсилку рахунків розпочато.');
        Tasks::start(Tasks::INVOICES_TO_EMAIL, ['date' => $calculator->date, 'client_id' => Yii::$app->user->identity->client_id, 'house_id' => $house->id, 'service_id' => $sid]);
        return $this->redirect(['period', 'id' => $aid, 'aid' => $sid]);
    }

}
