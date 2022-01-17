<?php

namespace app\modules\client\controllers;

use yii\web\UploadedFile;
use \Yii;
use app\components\ClientController;
use app\components\Misc;
use app\components\RuntimeFiles;
use app\models\UsersObjects;
use app\models\Tasks;
use app\models\CsvClientAddresses;

class AbonentsController extends ClientController {

    public function actionImport() {
        $time = CsvClientAddresses::getLogTime();
        $isRun = Tasks::isRun(Tasks::IMPORT_CLIENT_ADDRESSES_CSV);
        $isFile = !$isRun && (bool) $time;
        return $this->render('import', [
                    'info' => $isFile ? CsvClientAddresses::getLogInfo() : '',
                    'date' => $isFile ? $time : '',
                    'isRun' => $isRun,
                    'progress' => Tasks::getProgress(Tasks::IMPORT_CLIENT_ADDRESSES_CSV),
        ]);
    }

    public function actionDownloadCsv() {
        $file = RuntimeFiles::getUid('import-csv', 'clients_abonents.csv');
        CsvClientAddresses::createAlt($file, Yii::$app->user->identity->client_id);
        return Misc::sendFile($file, "мої абоненти.csv");
    }

    public function actionUpload() {
        $file = UploadedFile::getInstanceByName('file');
        if (!$file instanceof UploadedFile) {
            Yii::$app->end();
        }
        try {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $tmpFile = CsvClientAddresses::getTmpFile();
            CsvClientAddresses::initLog();
            $res = $file->saveAs($tmpFile);
            if (!$res) {
                return [
                    'res' => false,
                    'message' => 'Не вдалося зберегти файл.',
                ];
            }
            if (!CsvClientAddresses::checkFields($tmpFile)) {
                unlink($tmpFile);
                return [
                    'res' => false,
                    'message' => implode('<br/>', CsvClientAddresses::$info),
                ];
            }
            Tasks::start(Tasks::IMPORT_CLIENT_ADDRESSES_CSV, ['logFile' => CsvClientAddresses::$logFile, 'csv' => file_get_contents($tmpFile)]);
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
