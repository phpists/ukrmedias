<?php

namespace app\modules\admin\controllers;

use \Yii;
use yii\helpers\Html;
use app\components\AdminController;
use app\components\Misc;
use app\models\Orders;
use app\models\OrdersDetails;
use app\models\OrderXls;
use app\models\novaposhta\NovaPoshta;
use app\models\novaposhta\NP_ExpressDoc;
use app\models\novaposhta\NP_Senders;
use app\models\novaposhta\NP_CitiesNp;
use app\models\novaposhta\NP_Offices;
use app\models\novaposhta\NP_Contacts;
use app\models\novaposhta\NP_Types;
use app\models\novaposhta\NP_PaymentTypes;
use app\models\novaposhta\NP_PayerTypes;
use app\models\novaposhta\NP_DeliveryTypes;
use app\models\novaposhta\NP_CargoTypes;
use app\models\novaposhta\NP_CargoDescr;

class OrdersController extends AdminController {

    public function actionIndex() {
        $model = new Orders;
        $model->setScenario('search');
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $model->searchByManager(),
        ]);
    }

    public function actionView($id) {
        $model = Orders::findOne($id);
        if ($model === null || !$model->isAllowView()) {
            return $this->redirect(['index']);
        }
        return $this->render('view', [
                    'order' => $model,
                    'detailsDataProvider' => OrdersDetails::search($model->id),
        ]);
    }

    public function actionXls($id) {
        $model = Orders::findOne($id);
        if ($model === null || !$model->isAllowView()) {
            return $this->redirect(['index']);
        }

        $file = Yii::$app->getRuntimePath() . uniqid('/order_') . '.xlsx';
        OrderXls::create($file, $model);
        return Misc::sendFile($file, "Замовлення №{$model->getNumber()} - {$model->getDate()}.xlsx");
    }

    public function actionNp($id) {
        $order = Orders::findOne($id);
        if ($order === null || !$order->isAllowView() || !$order->isAllowNP()) {
            return $this->redirect(['index']);
        }
        $model = new NP_ExpressDoc();
        $model->initModel($order);
        if ($model->isSubmitted()) {
            $t = Yii::$app->db->beginTransaction();
            $res = $model->saveModel();
            if ($res && empty($order->np_express_num)) {
                $data = array_shift(NovaPoshta::$responce['data']);
                $order->updateAttributes(['np_express_ref' => $data['Ref'], 'np_express_num' => $data['IntDocNumber'], 'np_date' => date('Y-m-d')]);
                $new = true;
            }
            $res ? $t->commit() : $t->rollback();
            if ($res) {
                $message = 'Інформація записана.';
                if (isset($new)) {
                    $message .= ' Експрес-накладна ' . $order->np_express_num . '.';
                }
                $info = NovaPoshta::getMessages('info', 'infoCodes');
                if (count($info) > 0) {
                    $message .= implode('. ', $info) . '. ';
                }
                $warn = NovaPoshta::getMessages('warnings', 'warningCodes');
                if (count($warn) > 0) {
                    $message .= implode('. ', $warn) . '.';
                }
                Yii::$app->session->setFlash('success', $message);
            }
            if ($res && Yii::$app->request->post('action') === 'exit') {
                return $this->redirect(['index']);
            } elseif ($res) {
                return $this->refresh();
            }
            foreach (NovaPoshta::getMessages('errors', 'errorCodes') as $message) {
                $model->addError('Ref', $message);
            }
            foreach (NovaPoshta::getMessages('warnings', 'warningCodes') as $message) {
                $model->addError('Ref', $message);
            }
        }
        return $this->render('np', array(
                    'order' => $order,
                    'model' => $model,
                    'senders' => NP_Senders::keyval(),
                    'citySenderTitle' => NP_CitiesNp::findOne(NP_CitiesNp::DEFAULT_REF)->Description,
                    'cities' => NP_CitiesNp::keyval($order->np_area_ref),
                    'offices' => NP_Offices::keyval(NP_CitiesNp::DEFAULT_REF),
                    'officesR' => NP_Offices::keyval($model->CityRecipient),
                    'contacts' => NP_Contacts::keyval($model->Sender),
                    'types' => NP_Types::keyval(),
                    'paymentTypes' => NP_PaymentTypes::keyval(),
                    'payerTypes' => NP_PayerTypes::keyval(),
                    'deliveryTypes' => NP_DeliveryTypes::keyval(),
                    'cargoTypes' => NP_CargoTypes::keyval(),
                    'cargoDescr' => NP_CargoDescr::keyval_alt(),
        ));
    }

    public function actionOfficesOptions() {
        $htmlOptions = ['prompt' => ''];
        return Html::renderSelectOptions(null, NP_Offices::keyval(Yii::$app->request->post('city_ref')), $htmlOptions);
    }

    public function actionSenderContacts() {
        return Html::renderSelectOptions(null, NP_Contacts::keyval(Yii::$app->request->get('sender_ref')));
    }

    public function actionSenderPhone() {
        $contact = NP_Contacts::findOne(Yii::$app->request->get('contact_ref'));
        return $contact ? $contact->Phones : '';
    }

}
