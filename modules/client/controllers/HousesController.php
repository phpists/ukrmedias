<?php

namespace app\modules\client\controllers;

use \Yii;
use yii\helpers\Url;
use app\components\ClientController;
use app\components\Misc;
use app\components\Auth;
use app\models\AddrRegions;
use app\models\AddrAreas;
use app\models\AddrCities;
use app\models\AddrStreets;
use app\models\AddrHouses;
use app\models\Services;
use app\models\Clients;
use app\models\DataHelper;
use app\models\Hardware;
use app\models\AddrFlats;
use app\models\Abonents;
use app\models\ReportXlsPeriod;
use app\models\ReportXlsFlat;
use app\models\ReportFlats;

class HousesController extends ClientController {

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
                        'services' => $client->services,
            ]);
        } elseif ($dataProvider->totalCount === 1) {
            $house = current($dataProvider->getModels());
            return $this->redirect(['details', 'id' => $house->id, 'aid' => current($client->houseServices)]);
        } else {
            return $this->render('index_none');
        }
    }

    public function actionUpdate($id = null) {
        $post = Yii::$app->request->post('AddrHouses');
        $model = $id > 0 ? AddrHouses::findModel($id) : AddrHouses::findModel(@$post['id']);
        if ($model->load(Yii::$app->request->post())) {
            $t = Yii::$app->db->beginTransaction();
            if (!$model->isNewRecord) {
                $res = $model->updateByClient(Yii::$app->user->identity->client_id);
            } else {
                $model->addError('no', 'Некоректний вибір адреси.');
                $res = false;
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
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($model, true);
        if ($model->isNewRecord) {
            $regions = AddrRegions::keyvalClient($client);
            $areas = AddrAreas::keyvalClient(key($regions), $client);
            $cities = AddrCities::keyvalClient(key($regions), key($areas), $client);
            return $this->render('create', [
                        'model' => $model,
                        'regions' => $regions,
                        'areas' => $areas,
                        'cities' => $cities,
                        'streets' => AddrStreets::keyvalClient(key($cities), $client),
                        'houses' => AddrHouses::keyval($model->street_id),
                        'services' => $client->services,
            ]);
        } else {
            return $this->render('update', [
                        'model' => $model,
                        'services' => $client->services,
            ]);
        }
    }

    public function actionDetails($id, $aid) {
        $house = AddrHouses::findOne($id);
        if ($house === null || !$house->isMy()) {
            return $this->redirect(['index']);
        }
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
        if (count($client->houseServices) === 0 && !$client->isSupervisor()) {
            //return $this->redirect(['index']);
        }
        if ($client->isSupervisor() && !array_key_exists($aid, Services::$labels)) {
            $aid = key(Services::$labels);
            return $this->redirect(['details', 'id' => $id, 'aid' => $aid]);
        }
        if (!$client->isSupervisor() && !in_array($aid, $client->houseServices)) {
            $aid = current($client->houseServices);
            //return $this->redirect(['details', 'id' => $id, 'aid' => $aid]);
        }
        return $this->render('details', [
                    'house' => $house,
                    'service_id' => $aid,
                    'hardware' => Hardware::findByPeriodFullData($house->id, null, $aid),
                    'hardwareHouse' => [],
                    'abonents' => $house->getAbonents(),
                    'client' => $client,
        ]);
    }

    public function actionReport($id, $aid) {
        $model = AddrHouses::findOne($id);
        if ($model === null || !$model->isMy()) {
            Yii::$app->end();
        }
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($model);
        if (!$client->isSupervisor() && !in_array($aid, $client->houseServices) || $client->isSupervisor() && !in_array($aid, $client->services)) {
            Yii::$app->end();
        }
        $reportView = Yii::$app->request->get('reportView');
        $reportGroup = Yii::$app->request->get('reportGroup');
        $reportDate = Yii::$app->request->get('reportDate');
        $reportCounter = Yii::$app->request->get('reportCounter');
        if (!array_key_exists($reportView, DataHelper::$reportViewLabels) || !array_key_exists($reportGroup, DataHelper::$groupTime) || $reportDate == '') {
            Yii::$app->end();
        }
        $dates = DataHelper::defineDates($reportDate, $reportGroup);
        $reportData = ReportFlats::getAll($dates['from'], $dates['to'], $model->id, null, $aid, DataHelper::BY_HOUSE_COUNTER, $reportGroup, $reportCounter, $client->id);
        if ($reportView == DataHelper::REPORT_VIEW_CHART) {
            $reportData = DataHelper::getChartData($dates, $reportData, $reportGroup);
        }
        $unit = Hardware::findUnit($model->id, null, $aid, date('Y-m-01', strtotime($dates['from'])));
        return $this->renderPartial("_report_{$reportView}", [
                    'reportData' => $reportData,
                    'reportGroup' => $reportGroup,
                    'unit' => $unit,
        ]);
    }

    public function actionDownload($id, $aid) {
        $model = AddrHouses::findOne($id);
        if ($model === null || !$model->isMy()) {
            Yii::$app->end();
        }
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($model);
        if (!$client->isSupervisor() && !in_array($aid, $client->houseServices) || $client->isSupervisor() && !in_array($aid, $client->services)) {
            Yii::$app->end();
        }
        $reportGroup = Yii::$app->request->get('reportGroup');
        $reportDate = Yii::$app->request->get('reportDate');
        $by = Yii::$app->request->get('by');
        $reportCounter = Yii::$app->request->get('reportCounter');
        if (!array_key_exists($reportGroup, DataHelper::$groupTime)) {
            Yii::$app->end();
        }
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->security->hashData(json_encode(['id' => $id, 'service_id' => $aid, 'date' => $reportDate, 'group' => $reportGroup, 'by' => $by, 'counterNumber' => $reportCounter]), DataHelper::HASH_KEY);
            return Url::toRoute(['xls', 'data' => base64_encode($data)]);
        }
    }

    public function actionXls($data) {
        $data = Yii::$app->security->validateData(base64_decode($data), DataHelper::HASH_KEY);
        if ($data === false) {
            Yii::$app->end();
        }
        $data = json_decode($data, true);
        $data['client_id'] = Yii::$app->user->identity->client_id;
        $model = AddrHouses::findOne($data['id']);
        $file = Yii::$app->getRuntimePath() . uniqid('/xls_') . '.xlsx';
        $client = DataHelper::findServiceClient($model->id, $data['service_id']);
        switch ($data['service_id']):
            case Services::ID_HEAT:
                ReportXlsFlat::heat($file, $model, $data, $client);
                break;
            default:
                ReportXlsFlat::water($file, $model, $data, $client);
        endswitch;
        $service = Services::$labels[$data['service_id']];
        $period = DataHelper::definePeriodAlt($data, DataHelper::defineDates($data['date'], $data['group']));
        return Misc::sendFile($file, "{$service} - індивідуальний звіт {$period}, {$model->getTitle()}.xlsx");
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = AddrHouses::findOne($id);
            if ($model) {
                $t = Yii::$app->db->beginTransaction();
                $res = $model->delClient(Yii::$app->user->identity->client_id);
                $res ? $t->commit() : $t->rollBack();
            }
            return;
        }
        return $this->redirect(['index']);
    }

    public function actionAdd($id, $aid) {
        $house = AddrHouses::findOne($id);
        if ($house === null || !$house->isMy()) {
            return $this->redirect(['index']);
        }
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
        if (count($client->houseServices) > 0 && !in_array($aid, $client->houseServices)) {
            $aid = current($client->houseServices);
            return $this->redirect(['details', 'id' => $id, 'aid' => $aid]);
        }
        if (!in_array($aid, $client->houseServices)) {
            return $this->redirect(['index']);
        }
        $house->setScenario('add-flat');
        if ($house->load(Yii::$app->request->post())) {
            $flat = AddrFlats::findOne($house->flat_id);
            if ($flat && $flat->house_id == $id) {
                $t = Yii::$app->db->beginTransaction();
                $res = $house->addClientFlat(Yii::$app->user->identity->client_id);
                if ($res) {
                    Abonents::updFlat($flat);
                }
                $res ? $t->commit() : $t->rollBack();
                if ($res) {
                    Yii::$app->session->setFlash('success', 'Інформація записана.');
                    return $this->redirect(['details', 'id' => $id, 'aid' => $aid]);
                }
            } else {
                return $this->redirect(['details', 'id' => $id, 'aid' => $aid]);
            }
        }
        return $this->render('add_flat', [
                    'house' => $house,
                    'service_id' => $aid,
                    'flats' => AddrFlats::keyval($house->id),
        ]);
    }

}
