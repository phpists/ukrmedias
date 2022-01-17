<?php

namespace app\models\novaposhta;

use Yii;
use app\models\Settings;
use app\models\Orders;

class NP_ExpressDoc extends \yii\base\Model {

    const BACKWARD_DELIVERY_MONEY = 0;

    public $Ref;
    public $DateTime;
    public $NewAddress = 1;
    public $PayerType;
    public $PaymentMethod;
    public $CargoType;
    public $VolumeGeneral;
    public $Weight = 0.1;
    public $ServiceType;
    public $SeatsAmount = 1;
    public $Description;
    public $Cost;
    public $CitySender;
    public $Sender;
    public $SenderAddress = '';
    public $SenderOffice = '';
    public $ContactSender;
    public $SendersPhone;
    public $Recipient = '';
    public $CityRecipient;
    public $RecipientCityName = '';
    public $RecipientArea = '';
    public $RecipientAreaRegions = '';
    public $RecipientAddressName;
    public $RecipientHouse = '';
    public $RecipientFlat = '';
    public $RecipientName;
    public $RecipientType;
    public $RecipientsPhone;
    public $specialCargo = '0';
    public $volumetricHeight;
    public $volumetricWidth;
    public $volumetricLength;
    //additional services
    public $AccompanyingDocuments;
    public $BackwardDeliveryData = [
        self::BACKWARD_DELIVERY_MONEY => [
            'PayerType' => 'Recipient',
            'CargoType' => 'Money',
            'RedeliveryString' => ''
        ]
    ];

    public function isSubmitted() {
        $attrs = (array) Yii::$app->request->post('NP_ExpressDoc');
        $this->setAttributes($attrs, false);
        return count($attrs) > 0;
    }

    //static protected $state_attributes = ['PayerType', 'PaymentMethod', 'Sender', 'SenderOffice', 'SenderAddress', 'CitySender', 'ContactSender', 'SendersPhone', 'RecipientType', 'ServiceType', 'CargoType', 'Description'];

    protected function _init_addr() {
        $city = NP_CitiesNp::findOne($this->CityRecipient);
        if ($city !== null) {
            $this->RecipientCityName = $city->Description;
            $this->RecipientArea = $city->AreaDescription;
            #$this->RecipientAreaRegions = $city->RegionsDescription;
        }
        $this->SenderAddress = NP_Offices::getRef($this->SenderOffice);
    }

    protected function _init_new($order) {
        $this->CityRecipient = $order->np_city_ref;
        $this->RecipientAddressName = $order->np_office_no;
        $this->RecipientName = $order->name;
        $this->RecipientsPhone = $order->phone;
        $this->Cost = $order->amount;
        $this->VolumeGeneral = $order->weight;
        $this->AccompanyingDocuments = $order->getFullNum();
        $this->BackwardDeliveryData[self::BACKWARD_DELIVERY_MONEY]['CargoType'] = $order->getNP_cargo_type();
        $this->BackwardDeliveryData[self::BACKWARD_DELIVERY_MONEY]['RedeliveryString'] = $this->Cost;
        $this->Ref = $order->np_express_ref;
        $this->DateTime = date('d.m.Y');
        $this->PayerType = NP_PayerTypes::DEFAULT_REF;
        $this->PaymentMethod = NP_PaymentTypes::DEFAULT_REF;
        $this->Sender = NP_Senders::DEFAULT_REF;
        $this->SenderOffice = NP_Offices::DEFAULT_NO;
        $this->SenderAddress = NP_Offices::DEFAULT_REF;
        $this->CitySender = NP_CitiesNp::DEFAULT_REF;
        $contact = NP_Contacts::findDefault();
        $this->ContactSender = $contact->Ref;
        $this->SendersPhone = $contact->Phones;
        $this->RecipientType = NP_Types::DEFAULT_REF;
        $this->ServiceType = NP_DeliveryTypes::DEFAULT_REF;
        $this->CargoType = NP_CargoTypes::DEFAULT_REF;
        $this->Description = NP_CargoDescr::DEFAULT_DESCR;
        //$this->restoreState();
    }

    public function initModel($order) {
        if ($order->np_express_num === '') {
            $this->_init_new($order);
            return true;
        }
        $data = NovaPoshta::getTrackInfo($order->np_express_num);
        if ($data === false) {
            return false;
        }
        if (in_array($data['StatusCode'], array(NovaPoshta::STATUS_DELETED))) {
            $order->updateAttributes(['np_express_ref' => '', 'np_express_num' => '', 'np_date' => null]);
        }
        if (empty($order->np_express_num)) {
            $this->_init_new($order);
            return true;
        }
        $data = NovaPoshta::getDocument($order->np_express_num, $order->np_date);
        if ($data === false) {
            $this->_init_new($order);
            return true;
        }
        $this->setAttributes($data, false);
        $office = NP_Offices::findOne($data['RecipientAddress']);
        #$this->RecipientCityName = $office->CityDescription;



        $this->RecipientAddressName = $office->Number;
        $this->CitySender = $data['CitySender'];
        $this->CityRecipient = $data['CityRecipient'];
        $this->Sender = $data['Sender'];
        $this->RecipientName = $data['RecipientContactPerson'];
        $this->RecipientType = $data['RecipientCounterpartyType'];
        $this->DateTime = date('d.m.Y', strtotime($data['DateTime']));
        $this->ContactSender = $data['ContactSender'];
        $this->SendersPhone = $data['SendersPhone'];
        $this->SenderOffice = $data['SenderOffice'];
        $this->VolumeGeneral = $order->getVWeight();
        $this->BackwardDeliveryData[self::BACKWARD_DELIVERY_MONEY]['CargoType'] = $order->getNP_cargo_type();
        $this->BackwardDeliveryData[self::BACKWARD_DELIVERY_MONEY]['RedeliveryString'] = $data['BackwardDeliveryMoney'];
        return true;
    }

    public function saveModel() {
        $this->_init_addr();

        $method = $this->Ref === '' ? 'save' : 'update';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => 'InternetDocument',
            'calledMethod' => $method,
            'methodProperties' => $this->attributes
        );
        if ($data['methodProperties']['volumetricHeight'] == '' && $data['methodProperties']['volumetricWidth'] == '' && $data['methodProperties']['volumetricLength'] == '') {
            $specialCargo = $data['methodProperties']['specialCargo'];
            unset($data['methodProperties']['specialCargo']);
            $data['methodProperties']['SpecialCargo'] = $specialCargo;
        }
        unset($data['methodProperties']['CityRecipient']);
        if ($this->Ref == '') {
            unset($data['methodProperties']['Ref']);
        }
        if ($data['methodProperties']['DateTime'] < date('d.m.Y')) {
            $data['methodProperties']['DateTime'] = date('d.m.Y');
        }
        if ($data['methodProperties']['BackwardDeliveryData'][self::BACKWARD_DELIVERY_MONEY]['CargoType'] === '') {
            unset($data['methodProperties']['BackwardDeliveryData']);
        }
        unset($data['methodProperties']['SenderOffice']);
        Yii::dumpAlt($data);
        $res = NovaPoshta::request("/InternetDocument/{$method}", $data);
        Yii::dumpAlt(NovaPoshta::$responce);
//        if ($res) {
//            $this->saveState();
//        }
        return $res;
    }

//    public function deleteModel() {
//        if (!empty($this->Ref)) {
//            NovaPoshta::delete($this->Ref);
//            Orders::updateAll(['np_express_ref' => '', 'np_express_num' => '', 'np_date' => null], ['np_express_ref' => $this->Ref]);
//            Yii::dump(NovaPoshta::$responce);
//        }
//    }
//
//    protected function saveState() {
//        file_put_contents(Yii::$app->getRuntimePath() . '/' . get_called_class() . '.json', json_encode($this->getAttributes(self::$state_attributes)));
//    }
//
//    protected function restoreState() {
//        $file = Yii::$app->getRuntimePath() . '/' . get_called_class() . '.json';
//        if (is_file($file)) {
//            $attrs = (array) json_decode(file_get_contents($file), true);
//            $this->setAttributes($attrs, false);
//        }
//    }

    public function getDate() {
        return date('d.m.Y');
    }

}
