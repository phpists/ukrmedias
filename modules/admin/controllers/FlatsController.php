<?php

namespace app\modules\admin\controllers;

use \Yii;
use yii\web\UploadedFile;
use app\components\AdminController;
use app\components\RuntimeFiles;
use app\components\Misc;
use app\models\AddrRegions;
use app\models\AddrAreas;
use app\models\AddrCities;
use app\models\AddrStreets;
use app\models\AddrHouses;
use app\models\AddrFlats;
use app\models\CsvAddresses;
use app\models\Tasks;

class FlatsController extends AdminController {

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        $model = new AddrFlats;
        $dp = $model->search();
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $dp,
        ]);
    }

    public function actionUpdate($id = null) {
        $model = $id > 0 ? AddrFlats::findModel($id) : new AddrFlats();
        $model->setScenario('import');
        if ($model->load(Yii::$app->request->post())) {
            $t = Yii::$app->db->beginTransaction();
            $res = $model->saveModel();
            if ($res) {
                AddrHouses::updateFlatsQtyById($model->house_id);
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
                    'regions' => AddrRegions::keyval(),
                    'areas' => AddrAreas::keyval($model->region_id),
                    'cities' => AddrCities::keyval($model->region_id, $model->area_id),
                    'streets' => AddrStreets::keyval($model->city_id),
                    'houses' => AddrHouses::keyval($model->street_id),
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = AddrFlats::findOne($id);
            if ($model !== null) {
                $t = Yii::$app->db->beginTransaction();
                $res = $model->deleteModel();
                if ($res) {
                    AddrHouses::updateFlatsQtyById($model->house_id);
                }
                $res ? $t->commit() : $t->rollBack();
            }
            return;
        }
        return $this->redirect(['index']);
    }

    public function actionImport() {
        $time = CsvAddresses::getLogTime();
        $isRun = Tasks::isRun(Tasks::IMPORT_ADDRESSES_CSV);
        $isFile = !$isRun && (bool) $time;
        return $this->render('import', [
                    'info' => $isFile ? CsvAddresses::getLogInfo() : '',
                    'date' => $isFile ? $time : '',
                    'isRun' => $isRun,
                    'progress' => Tasks::getProgress(Tasks::IMPORT_ADDRESSES_CSV),
        ]);
    }

    public function actionDownloadCsv() {
        $data = AddrFlats::find()->groupBy(['region_id', 'area_id', 'city_id'])->limit(100)->all();
        $file = RuntimeFiles::getUid('import-csv', 'addresses.csv');
        CsvAddresses::create($file, $data);
        return Misc::sendFile($file, "довідник адрес.csv");
    }

    public function actionUpload() {
        $file = UploadedFile::getInstanceByName('file');
        if (!$file instanceof UploadedFile) {
            Yii::$app->end();
        }
        try {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $tmpFile = CsvAddresses::getTmpFile();
            CsvAddresses::initLog();
            $res = $file->saveAs($tmpFile);
            if (!$res) {
                return [
                    'res' => false,
                    'message' => 'Не вдалося зберегти файл.',
                ];
            }
            if (!CsvAddresses::checkFields($tmpFile)) {
                unlink($tmpFile);
                return [
                    'res' => false,
                    'message' => implode('<br/>', CsvAddresses::$info),
                ];
            }
            Tasks::start(Tasks::IMPORT_ADDRESSES_CSV, ['logFile' => CsvAddresses::$logFile, 'csv' => file_get_contents($tmpFile)]);
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

}
