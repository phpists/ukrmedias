<?php

namespace app\modules\client\controllers;

use \Yii;
use yii\helpers\Url;
use app\components\ClientController;
use app\components\Misc;
use app\models\AddrHouses;
use app\models\AddrFlats;
use app\models\Clients;
use app\models\DataHelper;
use app\models\Hardware;
use app\models\ReportFlats;
use app\models\ReportXlsFlat;
use app\models\Services;
use app\models\Devices;

class FlatsController extends ClientController {

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionDetails($id, $aid) {
        $flat = AddrFlats::findOne($id);
        if ($flat === null) {
            return $this->redirect('/client/houses/index');
        }
        $house = AddrHouses::findOne($flat->house_id);
        if (!$house->isMy()) {
            return $this->redirect('/client/houses/index');
        }
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
        if (count($client->houseServices) === 0 && !$client->isSupervisor()) {
            return $this->redirect('/client/houses/index');
        }
        if ($client->isSupervisor() && !array_key_exists($aid, Services::$labels)) {
            $aid = key(Services::$labels);
            return $this->redirect(['details', 'id' => $id, 'aid' => $aid]);
        }
        if (!$client->isSupervisor() && !in_array($aid, $client->houseServices)) {
            $aid = current($client->houseServices);
            return $this->redirect(['details', 'id' => $id, 'aid' => $aid]);
        }
        return $this->render('details', [
                    'house' => $house,
                    'flat' => $flat,
                    'service_id' => $aid,
                    'hardware' => Hardware::findByPeriodFullData($house->id, $flat->id, $aid),
                    'hardwareHouse' => $client->isSupervisor() ? Hardware::findByPeriodFullData($house->id, null, $aid) : [],
                    'abonents' => $flat->getAbonents(),
                    'client' => $client,
        ]);
    }

    public function actionReport($id, $aid) {
        $flat = AddrFlats::findOne($id);
        if ($flat === null) {
            Yii::$app->end();
        }
        $house = AddrHouses::findOne($flat->house_id);
        if (!$house->isMy()) {
            Yii::$app->end();
        }
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
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
        $reportData = ReportFlats::getAll($dates['from'], $dates['to'], $flat->house_id, $flat->id, $aid, DataHelper::BY_FLAT_COUNTERS, $reportGroup, $reportCounter, $client->id);
        $unit = Hardware::findUnit($flat->house_id, $flat->id, $aid, date('Y-m-01', strtotime($dates['from'])));
        return $this->renderPartial("/houses/_report_{$reportView}", [
                    'reportData' => $reportView == DataHelper::REPORT_VIEW_CHART ? DataHelper::getChartData($dates, $reportData, $reportGroup) : $reportData,
                    'prevValue' => '', #in_array($reportView, [DataHelper::REPORT_VIEW_TABLE, DataHelper::REPORT_VIEW_LIST]) ? ReportFlats::getPrevValue($dates['from'], $house->id, null, $aid, DataHelper::BY_FLAT_COUNTERS, $reportGroup) : '',
                    'reportGroup' => $reportGroup,
                    'unit' => $unit,
        ]);
    }

    public function actionDownload($id, $aid) {
        $flat = AddrFlats::findOne($id);
        if ($flat === null) {
            Yii::$app->end();
        }
        $house = AddrHouses::findOne($flat->house_id);
        if (!$house->isMy()) {
            Yii::$app->end();
        }
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
        if (!$client->isSupervisor() && !in_array($aid, $client->houseServices) || $client->isSupervisor() && !in_array($aid, $client->services)) {
            Yii::$app->end();
        }
        $reportGroup = Yii::$app->request->get('reportGroup');
        $reportDate = Yii::$app->request->get('reportDate');
        $reportCounter = Yii::$app->request->get('reportCounter');
        if (!array_key_exists($reportGroup, DataHelper::$groupTime)) {
            Yii::$app->end();
        }
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->security->hashData(json_encode(['id' => $id, 'service_id' => $aid, 'date' => $reportDate, 'group' => $reportGroup, 'counterNumber' => $reportCounter]), DataHelper::HASH_KEY);
            return Url::toRoute(['xls', 'data' => base64_encode($data)]);
        }
    }

    public function actionXls($data) {
        $data = Yii::$app->security->validateData(base64_decode($data), DataHelper::HASH_KEY);
        if ($data === false) {
            Yii::$app->end();
        }
        $data = json_decode($data, true);
        $flat = AddrFlats::findOne($data['id']);
        $file = Yii::$app->getRuntimePath() . uniqid('/xls_') . '.xlsx';
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        switch ($data['service_id']):
            case Services::ID_HEAT:
                ReportXlsFlat::heat($file, $flat, $data, $client);
                break;
            default:
                ReportXlsFlat::water($file, $flat, $data, $client);
        endswitch;
        $service = Services::$labels[$data['service_id']];
        $period = DataHelper::definePeriodAlt($data, DataHelper::defineDates($data['date'], $data['group']));
        return Misc::sendFile($file, "{$service} - ({$period}, {$flat->getTitle()}).xlsx");
    }

}
