<?php

namespace app\modules\admin\controllers;

use \Yii;
use yii\web\UploadedFile;
use app\components\AdminController;
use app\components\Misc;
use app\components\RuntimeFiles;
use app\models\AccessLogic;
use app\models\AddrRegions;
use app\models\AddrAreas;
use app\models\AddrCities;
use app\models\AddrStreets;
use app\models\AddrHouses;
use app\models\AddrFlats;
use app\models\Services;
use app\models\Places;
use app\models\Lorawan;
use app\models\Devices;
use app\models\DevicesProfiles;
use app\models\DevicesCommands;
use app\models\Tasks;
use app\models\CsvDevices;
use app\models\Counters;

class DevicesController extends AdminController {

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        $model = new Devices();
        $dp = $model->search();
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $dp,
        ]);
    }

    public function actionUpdate($id = null) {
        $model = Devices::findModel($id);
        if (!$model->isNewRecord && !AccessLogic::isAllowHouseAccess($model->house_id)) {
            return $this->redirect(['index']);
        }
        if ($model->load(Yii::$app->request->post())) {
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
        $cityModel = AddrCities::findModel($model->city_id);
        $regions = AddrRegions::keyval();
        $model->region_id = $cityModel->region_id;
        $model->area_id = $cityModel->area_id;
        $areas = AddrAreas::keyval($cityModel->region_id);
        return $this->render('update', [
                    'model' => $model,
                    'cities' => AddrCities::keyval($cityModel->region_id, $cityModel->area_id),
                    'streets' => AddrStreets::keyval($model->city_id),
                    'houses' => AddrHouses::keyval($model->street_id),
                    'flats' => AddrFlats::keyval($model->house_id),
                    'services' => Services::$labels,
                    'places' => Places::$labels,
                    'profiles' => DevicesProfiles::keyval(),
                    'counters_1' => Counters::keyval($model->house_id, $model->flat_id, $model->service_id_1),
                    'counters_2' => Counters::keyval($model->house_id, $model->flat_id, $model->service_id_2),
                    'regions' => $regions,
                    'areas' => $areas,
        ]);
    }

    public function actionGetinfo($id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Lorawan::get("/api/devices/{$id}");
        if ($data === false) {
            Yii::$app->end();
        }
        $device = Devices::find()->where(['deveui' => $id])->one();
        if ($device === null || !AccessLogic::isAllowHouseAccess($device->house_id)) {
            Yii::$app->end();
        }
        return $data;
    }

    public function actionControl($id) {
        $device = Devices::findOne($id);
        if ($device === null || !AccessLogic::isAllowHouseAccess($device->house_id)) {
            return $this->redirect(['index']);
        }
        $model = DevicesCommands::findModel($device->id);
        $model->setScenario(Yii::$app->request->post('cmd'));
        if ($model->load(Yii::$app->request->post())) {
            $res = $model->saveModel($device);
            if ($res) {
                if (empty($model->date)) {
                    Yii::$app->session->setFlash('success', 'Команда "' . DevicesCommands::$labels[$model->getScenario()] . '" поставлена в чергу на відправку.');
                } else {
                    Yii::$app->session->setFlash('success', 'Команда "' . DevicesCommands::$labels[$model->getScenario()] . '" поставлена в чергу на відправку ' . $model->getDate() . '.');
                }
                return $this->refresh();
            }
        }
        if ($device->type_id == Devices::TYPE_UNIVERSAL) {
            return $this->render('control_universal', [
                        'model' => $model,
                        'device' => $device,
            ]);
        } else {
            return $this->render('control', [
                        'model' => $model,
                        'device' => $device,
            ]);
        }
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = Devices::findOne($id);
            if ($model !== null && AccessLogic::isAllowHouseAccess($model->house_id)) {
                $model->deleteModel();
            }
            return;
        }
        return $this->redirect(['index']);
    }

    public function actionImport() {
        $time = CsvDevices::getLogTime();
        $isRun = Tasks::isRun(Tasks::IMPORT_DEVICES_CSV);
        $isFile = !$isRun && (bool) $time;
        return $this->render('import', [
                    'info' => $isFile ? CsvDevices::getLogInfo() : '',
                    'date' => $isFile ? $time : '',
                    'isRun' => $isRun,
                    'progress' => Tasks::getProgress(Tasks::IMPORT_DEVICES_CSV),
        ]);
    }

    public function actionDownloadCsv() {
        $data = Devices::find()->groupBy(['type_id', 'service_id_1', 'unit_1', 'rate_1'])->limit(count(Devices::$types) * count(Services::$labels) * count(Devices::$units) * count(Devices::$rates))->all();
        $file = RuntimeFiles::getUid('import-csv', 'devices.csv');
        CsvDevices::create($file, $data);
        return Misc::sendFile($file, "пристрої.csv");
    }

    public function actionUpload() {
        $file = UploadedFile::getInstanceByName('file');
        if (!$file instanceof UploadedFile) {
            Yii::$app->end();
        }
        try {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $tmpFile = CsvDevices::getTmpFile();
            CsvDevices::initLog();
            $res = $file->saveAs($tmpFile);
            if (!$res) {
                return [
                    'res' => false,
                    'message' => 'Не вдалося зберегти файл.',
                ];
            }
            if (!CsvDevices::checkFields($tmpFile)) {
                unlink($tmpFile);
                return [
                    'res' => false,
                    'message' => implode('<br/>', CsvDevices::$info),
                ];
            }
            Tasks::start(Tasks::IMPORT_DEVICES_CSV, ['logFile' => CsvDevices::$logFile, 'csv' => file_get_contents($tmpFile)]);
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

    public function actionTo($deveui) {
        $model = Devices::find()->where(['deveui' => $deveui])->one();
        if ($model === null || !AccessLogic::isAllowHouseAccess($model->house_id)) {
            return $this->redirect(['index']);
        }
        return $this->redirect(['update', 'id' => $model->id]);
    }

}
