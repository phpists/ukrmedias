<?php

namespace app\modules\admin\controllers;

use \Yii;
use yii\web\UploadedFile;
use app\components\Misc;
use app\components\RuntimeFiles;
use app\components\AdminController;
use app\models\AccessLogic;
use app\models\AddrHouses;
use app\models\AddrFlats;
use app\models\Users;
use app\models\Abonents;
use app\models\Clients;
use app\models\CsvAbonents;
use app\models\Tasks;
use app\models\UsersObjects;

class AddressesController extends AdminController {

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        $model = new Abonents;
        $dp = $model->search();
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $dp,
        ]);
    }

    public function actionView($id, $aid = null) {
        if ($aid > 0) {
            $model = AddrFlats::findOne($aid);
            if ($model === null || !$model->isAllowControl()) {
                return $this->redirect(['index']);
            }
            $usersModel = new Users();
            return $this->render('view_flat', [
                        'flat' => $model,
                        'dataProvider' => $usersModel->searchAbonentsByFlat($model->id)
            ]);
        } else {
            $model = AddrHouses::findOne($id);
            if ($model === null || !AccessLogic::isAllowHouseAccess($model->id)) {
                return $this->redirect(['index']);
            }
            $usersModel = new Users();
            $flat = new AddrFlats();
            $flat->house_id = $model->id;
            $flat->setAttributes($model->getAttributes(['search_city', 'search_street']), false);
            $flat->search_house = $model->no;
            return $this->render('view_house', [
                        'house' => $model,
                        'dataProvider' => $usersModel->searchAbonentsByHouse($model->id),
                        'flat' => $flat,
            ]);
        }
    }

    public function actionUpdate($id, $aid = null) {
        if ($aid > 0) {
            $object = AddrFlats::findOne($aid);
            if ($object === null || !$object->isAllowControl()) {
                return $this->redirect(['index']);
            }
        } else {
            $object = AddrHouses::findOne($id);
            if ($object === null || !AccessLogic::isAllowHouseAccess($object->id)) {
                return $this->redirect(['index']);
            }
        }
        if ($object->load(Yii::$app->request->post())) {
            $res = $object->saveModel();
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                if (Yii::$app->request->post('action') === 'exit') {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $id, 'aid' => $aid]);
                }
            }
        }
        if ($object instanceof AddrHouses) {
            return $this->render('update_house', [
                        'model' => $object,
            ]);
        } else {
            return $this->render('update_flat', [
                        'model' => $object,
            ]);
        }
    }

    public function actionTransfer($id) {
        $house = AddrHouses::findOne($id);
        if ($house === null || !AccessLogic::isAllowHouseAccess($house->id)) {
            return $this->redirect(['index']);
        }
        $model = Abonents::findOne(['house_id' => $id, 'fid' => 0]);
        if ($model === null || !$model->isAllowTransfer()) {
            return $this->redirect(['index']);
        }
        $model->setScenario('transfer');
        $clientsKeyval = Clients::keyvalToTransfer($house);
        if ($model->load(Yii::$app->request->post())) {
            $t = $model->getDb()->beginTransaction();
            $res = $model->transfer($house, Yii::$app->user->getId(), array_keys($clientsKeyval));
            $res ? $t->commit() : $t->rollBack();
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                if (Yii::$app->request->post('action') === 'exit') {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['transfer', 'id' => $id]);
                }
            }
        }
        return $this->render('transfer', [
                    'model' => $model,
                    'clients' => $model->findClients(),
                    'clientsKeyval' => $clientsKeyval,
        ]);
    }

    public function actionImport() {
        $isRun = Tasks::isRun(Tasks::IMPORT_ABONENTS_CSV);
        $time = CsvAbonents::getLogTime();
        $isFile = !$isRun && (bool) $time;
        return $this->render('import', [
                    'info' => $isFile ? CsvAbonents::getLogInfo() : '',
                    'date' => $isFile ? $time : '',
                    'isRun' => $isRun,
                    'progress' => Tasks::getProgress(Tasks::IMPORT_ABONENTS_CSV),
        ]);
    }

    public function actionDownloadCsv() {
        $data = UsersObjects::find()->groupBy(['user_id', 'type'])->limit(20)->all();
        $file = RuntimeFiles::getUid('import-csv', 'abonents.csv');
        CsvAbonents::create($file, $data);
        return Misc::sendFile($file, "користувачі абонентів.csv");
    }

    public function actionUpload() {
        $file = UploadedFile::getInstanceByName('file');
        if (!$file instanceof UploadedFile) {
            Yii::$app->end();
        }
        try {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $tmpFile = CsvAbonents::getTmpFile();
            CsvAbonents::initLog();
            $res = $file->saveAs($tmpFile);
            if (!$res) {
                return [
                    'res' => false,
                    'message' => 'Не вдалося зберегти файл.',
                ];
            }
            if (!CsvAbonents::checkFields($tmpFile)) {
                unlink($tmpFile);
                return [
                    'res' => false,
                    'message' => implode('<br/>', CsvAbonents::$info),
                ];
            }
            Tasks::start(Tasks::IMPORT_ABONENTS_CSV, ['csv' => file_get_contents($tmpFile)]);
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
