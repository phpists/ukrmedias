<?php

namespace app\modules\client\controllers;

use \Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\UploadedFile;
use app\components\ClientController;
use app\components\RuntimeFiles;
use app\components\Misc;
use app\models\AddrRegions;
use app\models\AddrAreas;
use app\models\AddrCities;
use app\models\AddrStreets;
use app\models\AddrHouses;
use app\models\AddrFlats;
use app\models\Services;
use app\models\Clients;
use app\models\Counters;
use app\models\Hardware;
use app\models\Tasks;
use app\models\AccessLogic;
use app\models\CsvCounters;

class CountersController extends ClientController {

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionIndex($id = null) {
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel();
        if ($id === null) {
            $id = current($client->services);
        }
        if (!in_array($id, $client->services)) {
            return $this->redirect('/');
        }
        $model = new Hardware();
        $model->setScenario('search');
        $dataProvider = $model->searchClient(Yii::$app->user->identity->client_id, $id, $client->isSupervisor());
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                    'services' => $client->services,
                    'service_id' => $id,
        ]);
    }

    public function actionUpdate($id = null) {
        $model = $id > 0 ? Counters::findModel($id) : new Counters();
        if (!$model->isAllowUpdate()) {
            return $this->redirect(['index']);
        }
        $model->setScenario(AccessLogic::isLoginAs() ? 'admin_update' : 'import_water');
        if ($model->load(Yii::$app->request->post())) {
            $model->client_id = Yii::$app->user->identity->client_id;
            $t = Yii::$app->db->beginTransaction();
            $res = $model->saveModel();
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
        $house = $model->isNewRecord || $model->house_id === null ? new AddrHouses : AddrHouses::findOne($model->house_id);
        $model->region_id = $house->region_id;
        $model->area_id = $house->area_id;
        $client->initModel();
        $regions = AddrRegions::keyvalClient($client);
        $areas = AddrAreas::keyvalClient($model->isNewRecord ? key($regions) : $model->region_id, $client);
        $cities = AddrCities::keyvalClient($model->isNewRecord ? key($regions) : $model->region_id, $model->isNewRecord ? key($areas) : $model->area_id, $client);
        return $this->render('update', [
                    'model' => $model,
                    'regions' => $regions,
                    'areas' => $areas,
                    'cities' => $cities,
                    'streets' => AddrStreets::keyvalClient($model->isNewRecord ? key($cities) : $model->city_id, $client),
                    'houses' => $model->isNewRecord ? [] : AddrHouses::keyval($model->street_id),
                    'flats' => $model->isNewRecord ? [] : AddrFlats::keyval($model->house_id),
                    'services' => array_intersect_key(Services::$labels, array_flip($client->services)),
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Counters::findOne($id);
            if ($model->isAllowDelete()) {
                $model->deleted = 1;
                $model->update(['deleted']);
                Hardware::deleteAll(['counter_id' => $id]);
            }
            return;
        }
        return $this->redirect(['index']);
    }

    public function actionImport() {
        $time = CsvCounters::getLogTime();
        $isRun = Tasks::isRun(Tasks::IMPORT_COUNTERS_CSV);
        $isFile = !$isRun && (bool) $time;
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel();
        return $this->render('import', [
                    'info' => $isFile ? CsvCounters::getLogInfo() : '',
                    'date' => $isFile ? $time : '',
                    'isRun' => $isRun,
                    'progress' => Tasks::getProgress(Tasks::IMPORT_COUNTERS_CSV),
                    'client' => $client,
        ]);
    }

    public function actionDownloadCsv() {
        $file = RuntimeFiles::getUid('import-csv', 'counters.csv');
        $data = Counters::find()->where(['client_id' => Yii::$app->user->identity->client_id])->groupBy(['service_id', 'house_id', 'city_id'])->limit(20)->all();
        CsvCounters::create($file, $data);
        return Misc::sendFile($file, "лічильники.csv");
    }

    public function actionUpload() {
        $file = UploadedFile::getInstanceByName('file');
        if (!$file instanceof UploadedFile) {
            Yii::$app->end();
        }
        try {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $tmpFile = CsvCounters::getTmpFile();
            CsvCounters::initLog();
            $res = $file->saveAs($tmpFile);
            if (!$res) {
                return [
                    'res' => false,
                    'message' => 'Не вдалося зберегти файл.',
                ];
            }
            if (!CsvCounters::checkFields($tmpFile)) {
                unlink($tmpFile);
                return [
                    'res' => false,
                    'message' => implode('<br/>', CsvCounters::$info),
                ];
            }
            Tasks::start(Tasks::IMPORT_COUNTERS_CSV, ['logFile' => CsvCounters::$logFile, 'csv' => file_get_contents($tmpFile)]);
            if (is_file($tmpFile)) {
                unlink($tmpFile);
            }
            return [
                'res' => true,
                'message' => '',
            ];
        } catch (\Exception $ex) {
            Yii::dump($ex->getMessage());
            Yii::dump($ex->getTraceAsString());
            if (is_file($tmpFile)) {
                unlink($tmpFile);
            }
            return [
                'res' => false,
                'message' => 'Виникла внутрішня помилка. ' . Misc::internalErrorMessage(),
            ];
        }
    }

    public function actionAdd($id, $aid, $fid = null) {
        if ($fid > 0) {
            $returnUrl = Url::toRoute(['/client/flats/details', 'id' => $fid, 'aid' => $aid]);
        } else {
            $returnUrl = Url::toRoute(['/client/houses/details', 'id' => $id, 'aid' => $aid]);
        }
        $house = AddrHouses::findOne($id);
        if ($house === null || !$house->isMy()) {
            return $this->redirect($returnUrl);
        }
        $client = Clients::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
        if (count($client->houseServices) > 0 && !in_array($aid, $client->houseServices)) {
            $aid = current($client->houseServices);
            return $this->redirect($returnUrl);
        }
        if (!in_array($aid, $client->houseServices)) {
            return $this->redirect($returnUrl);
        }
        $model = new Counters();
        $model->flat_id = $fid;
        $model->service_id = $aid;
        $model->setScenario(AccessLogic::isLoginAs() ? 'admin_update' : 'import_water');
        if ($model->load(Yii::$app->request->post())) {
            $model->city_id = $house->city_id;
            $model->street_id = $house->street_id;
            $model->house_id = $house->id;
            $model->client_id = Yii::$app->user->identity->client_id;
            $t = Yii::$app->db->beginTransaction();
            $res = $model->saveModel();
            $res ? $t->commit() : $t->rollBack();
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                return $this->redirect($returnUrl);
            }
        }
        return $this->render('add', [
                    'model' => $model,
                    'house' => $house,
                    'flats' => AddrFlats::keyval($house->id),
                    'services' => Services::$labels,
                    'service_id' => $aid,
                    'flat_id' => $fid,
                    'returnUrl' => $returnUrl,
        ]);
    }

}
