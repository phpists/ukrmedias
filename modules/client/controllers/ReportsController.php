<?php

namespace app\modules\client\controllers;

use \Yii;
use app\components\Misc;
use app\components\RuntimeFiles;
use app\components\ClientController;
use app\models\Api;
use app\models\Firms;

class ReportsController extends ClientController {

    public function actionIndex() {
        return $this->render('index', [
                    'balanceFrom' => date('Y-m-01', strtotime('-3 month')),
                    'balanceTo' => date('Y-m-d'),
                    'paymentsFrom' => date('Y-m-d'),
        ]);
    }

    public function actionBalance($from, $to) {
        $dateFrom = new \DateTime(date('Y-m-d', strtotime($from)));
        $dateTo = new \DateTime(date('Y-m-d', strtotime($to)));
        if ($dateFrom->diff($dateTo)->days > 365) {
            return $this->redirect(['index']);
        }
        $dataset = [
            'id' => Firms::findOne(Yii::$app->user->identity->firm_id)->id_1s,
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
        ];
        if ($dataset['id'] == '') {
            Yii::$app->session->setFlash('error', 'Контрагент ще не заванажений в 1С. Спробуйте пізніше.');
            return $this->redirect(['index']);
        }
        $fname = RuntimeFiles::getUid("download", "balance_{$from}-{$to}.pdf");
        $res = Api::download('/get_report_revise', $fname, $dataset);
        if (!$res) {
            Yii::$app->session->setFlash('error', 'Помилка завантаження файлу. ' . Misc::internalErrorMessage());
            return $this->redirect(['index']);
        }
    }

    public function actionPayments($from) {
        $dateFrom = new \DateTime(date('Y-m-d', strtotime($from)));
        $dataset = [
            'id' => Firms::findOne(Yii::$app->user->identity->firm_id)->id_1s,
            'date_from' => $dateFrom->format('Y-m-d'),
        ];
        if ($dataset['id'] == '') {
            Yii::$app->session->setFlash('error', 'Контрагент ще не заванажений в 1С. Спробуйте пізніше.');
            return $this->redirect(['index']);
        }
        $fname = RuntimeFiles::getUid("download", "payments_{$from}.pdf");
        $res = Api::download('/get_report_payments', $fname, $dataset);
        if (!$res) {
            Yii::$app->session->setFlash('error', 'Помилка завантаження файлу. ' . Misc::internalErrorMessage());
            return $this->redirect(['index']);
        }
    }

}
