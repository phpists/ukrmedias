<?php

namespace app\modules\client\controllers;

use \Yii;
use yii\helpers\Url;
use app\components\ClientController;
use app\components\Misc;
use app\components\RuntimeFiles;
use app\models\Invoices;
use app\models\Firms;
use app\models\UsersObjects;
use app\models\DataHelper;
use app\models\AddrHouses;
use app\models\AddrFlats;
use app\models\Tasks;

class InvoicesController extends ClientController {

    public function actionIndex($id = null) {
        $client = Firms::findOne(Yii::$app->user->identity->client_id);
        $client->initModel();
        if ($id === null) {
            $id = current($client->services);
        }
        if (!in_array($id, $client->services)) {
            return $this->redirect('/');
        }
        $year = Yii::$app->request->get('year', date('Y', strtotime('-1 month')));
        $month = Yii::$app->request->get('month', date('m', strtotime('-1 month')));
        $model = new Invoices();
        $dataProvider = $model->search($year, $month, $id);
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                    'services' => $client->services,
                    'service_id' => $id,
                    'year' => $year,
                    'month' => $month,
        ]);
    }

    public function actionDetails($id, $aid, $sid, $from, $to) {
        $invoice = Invoices::find()->where(['from' => $from, 'to' => $to, 'house_id' => $id, 'flat' => $aid, 'service_id' => $sid])->one();
        if ($invoice === null) {
            return $this->redirect(['index']);
        }
        $house = AddrHouses::findOne($invoice->house_id);
        if (!$house->isMy()) {
            return $this->redirect(['index']);
        }
        $client = Firms::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
        if (!in_array($sid, $client->houseServices)) {
            return $this->redirect(['index']);
        }
        return $this->render('details', [
                    'invoice' => $invoice,
        ]);
    }

    public function actionDownload($id, $aid, $sid, $from, $to) {
        $invoice = Invoices::find()->where(['from' => $from, 'to' => $to, 'house_id' => $id, 'flat' => $aid, 'service_id' => $sid])->one();
        if ($invoice === null) {
            return $this->redirect(['index']);
        }
        $house = AddrHouses::findOne($invoice->house_id);
        if (!$house->isMy()) {
            return $this->redirect(['index']);
        }
        $client = Firms::findOne(Yii::$app->user->identity->client_id);
        $client->initModel($house);
        if (!in_array($sid, $client->houseServices)) {
            return $this->redirect(['index']);
        }
        $data = Yii::$app->security->hashData(json_encode([
            'from' => $from,
            'to' => $to,
            'house_id' => $id,
            'flat' => $aid,
            'service_id' => $sid,
            'client_id' => Yii::$app->user->identity->client_id,
                ]), DataHelper::HASH_KEY);
        return $this->redirect(Url::toRoute(['pdf', 'data' => base64_encode($data)]));
    }

    public function actionPdf($data) {
        $data = Yii::$app->security->validateData(base64_decode($data), DataHelper::HASH_KEY);
        if ($data === false) {
            return $this->redirect(['index']);
        }
        $data = json_decode($data, true);
        unset($data['client_id']);
        if ($data['flat'] > 0) {
            $invoice = Invoices::find()->where($data)->one();
            $file = $invoice->getPdfFile();
            if (!is_file($file)) {
                $invoice->toFile();
            }
            return Misc::sendFile($file, $invoice->getHumanFnamePdf(), false);
        }
        $invoice = Invoices::find()->where($data)->one();
        unset($data['flat']);
        $isRun = Tasks::isRun(Tasks::CREATE_INVOICES_ZIP);
        $zip = RuntimeFiles::get("invoices/house{$data['house_id']}/service{$data['service_id']}", 'house.local.zip');
        if (is_file($zip)) {
            return Misc::sendFile($zip, "Всі рахунки {$invoice->search_address} (період {$invoice->getFrom()}-{$invoice->getTo()}).pdf", false);
        }
        $result = Tasks::getResult(Tasks::CREATE_INVOICES_ZIP, $data);
        if (strlen($result) > 0) {
            file_put_contents($zip, $result);
            return Misc::sendFile($zip, "Всі рахунки {$invoice->search_address} (період {$invoice->getFrom()}-{$invoice->getTo()}).pdf", false);
        }
        if (!$isRun) {
            Tasks::start(Tasks::CREATE_INVOICES_ZIP, $data);
        }
        return $this->render('wait_download', [
                    'isRun' => $isRun,
                    'progress' => Tasks::getProgress(Tasks::CREATE_INVOICES_ZIP),
        ]);
    }

    public function actionScreen($data) {
        $data = Yii::$app->security->validateData(base64_decode($data), DataHelper::HASH_KEY);
        if ($data === false) {
            Yii::$app->end();
        }
        $data = json_decode($data, true);
        unset($data['client_id']);
        $invoice = Invoices::find()->where($data)->one();
        return $invoice->print();
    }

}
