<?php

namespace app\models\novaposhta;

use Yii;
use app\models\Settings;

class NovaPoshta {

    const STATUS_DELETED = 2;
    const STATUS_UNDEFINED = 3;

    static public $baseUrl = 'https://api.novaposhta.ua/v2.0/json';
    #static public $key = 'a9372ea8272400b7820eee14e5ede29e';
    static public $responce;
    static protected $_mess;

    static public function getCitiesBook($page) {
        $model = 'Address';
        $method = 'getCities';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
            'methodProperties' => array(
                'Page' => $page
            )
        );
        $res = self::request("/{$model}/{$method}", $data);
        return $res ? self::$responce['data'] : false;
    }

    static public function getOfficesBook($page) {
        $model = 'Address';
        $method = 'getWarehouses';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
            'methodProperties' => array(
                'Page' => $page,
                'Limit' => 500,
            )
        );
        $res = self::request("/{$model}/{$method}", $data);
        return $res ? self::$responce['data'] : false;
    }

    static public function getSenders() {
        $model = 'Counterparty';
        $method = 'getCounterparties';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
            'methodProperties' => array(
                'CounterpartyProperty' => 'Sender',
            )
        );

        $res = self::request("/{$model}/{$method}", $data);
        return $res ? self::$responce['data'] : false;
    }

    static public function getContacts($ref, $page) {
        $model = 'Counterparty';
        $method = 'getCounterpartyContactPersons';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
            'methodProperties' => array(
                'Ref' => $ref,
                'Page' => $page,
            )
        );

        $res = self::request("/{$model}/{$method}", $data);
        return $res ? self::$responce['data'] : false;
    }

    static public function getTypes() {
        $model = 'Common';
        $method = 'getTypesOfCounterparties';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
        );

        $res = self::request("/{$model}/{$method}", $data);
        return $res ? self::$responce['data'] : false;
    }

    static public function getPaymentTypes() {
        $model = 'Common';
        $method = 'getPaymentForms';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
        );

        $res = self::request("/{$model}/{$method}", $data);
        return $res ? self::$responce['data'] : false;
    }

    static public function getPayerTypes() {
        $model = 'Common';
        $method = 'getTypesOfPayers';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
        );

        $res = self::request("/{$model}/{$method}", $data);
        return $res ? self::$responce['data'] : false;
    }

    static public function getPayerTypesBack() {
        $model = 'Common';
        $method = 'getTypesOfPayersForRedelivery';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
        );

        $res = self::request("/{$model}/{$method}", $data);
        return $res ? self::$responce['data'] : false;
    }

    static public function getDeliveryTypes() {
        $model = 'Common';
        $method = 'getServiceTypes';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
        );

        $res = self::request("/{$model}/{$method}", $data);
        return $res ? self::$responce['data'] : false;
    }

    static public function getCargoTypes() {
        $model = 'Common';
        $method = 'getCargoTypes';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
        );

        $res = self::request("/{$model}/{$method}", $data);
        return $res ? self::$responce['data'] : false;
    }

    static public function getTrackInfo($number) {
        $model = 'TrackingDocument';
        $method = 'getStatusDocuments';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
            'methodProperties' => array(
                'Documents' => array(
                    array(
                        'DocumentNumber' => $number,
                        'Phone' => ''
                    )
                ),
            )
        );

        $res = self::request("/{$model}/{$method}", $data);
        return $res ? array_shift(self::$responce['data']) : false;
    }

    static public function getDocument($number, $date) {
        $model = 'InternetDocument';
        $method = 'getDocumentList';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
            'methodProperties' => array(
                'DateTimeFrom' => date('d.m.Y', strtotime($date)),
                'DateTimeTo' => date('d.m.Y'),
                'GetFullList' => 1
            )
        );

        $res = self::request("/{$model}/{$method}", $data);
        #Yii::dump(self::$responce);
        if ($res) {
            foreach (self::$responce['data'] as $attrs) {
                if ($attrs['IntDocNumber'] === $number) {
                    return $attrs;
                }
            }
        }
        return false;
    }

    static public function getSenderAddress($ref) {
        $model = 'Counterparty';
        $method = 'getCounterpartyAddresses';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
            'methodProperties' => array(
                'Ref' => $ref,
                'CounterpartyProperty' => 'Sender'
            )
        );
        $res = self::request("/{$model}/{$method}", $data);
        return $res ? self::$responce['data'] : false;
    }

//    static public function getStreets($ref, $page) {
//        $model = 'Address';
//        $method = 'getStreet';
//        $data = array(
//            'apiKey' => SiteData::getInstance()->getNpApiKey(),
//            'modelName' => $model,
//            'calledMethod' => $method,
//            'methodProperties' => array(
//                'CityRef' => $ref,
//                'Page' => $page,
//            )
//        );
//        $res = self::request("/{$model}/{$method}", $data);
//        return $res ? self::$responce['data'] : false;
//    }
//    static public function createDefaultAddress() {
//        $model = 'Address';
//        $method = 'save';
//        $data = array(
//            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
//            'modelName' => $model,
//            'calledMethod' => $method,
//            'methodProperties' => array(
//                'CounterpartyRef' => NP_Senders::DEFAULT_REF,
//                'StreetRef' => NP_Streets::DEFAULT_REF,
//                'BuildingNumber' => '5',
//                #'Flat' => '',
//                'Note' => 'Відділення №20'
//            )
//        );
//        $res = self::request("/{$model}/{$method}", $data);
//        return $res ? self::$responce['data'] : false;
//    }

    static public function delete($ref) {
        $model = 'InternetDocument';
        $method = 'delete';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
            'methodProperties' => array(
                'DocumentRefs' => $ref,
            )
        );

        $res = self::request("/{$model}/{$method}", $data);
        return $res ? self::$responce['data'] : false;
    }

    static public function getCargoDescr($page) {
        $model = 'Common';
        $method = 'getCargoDescriptionList';
        $data = array(
            'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
            'modelName' => $model,
            'calledMethod' => $method,
            'methodProperties' => array(
                'Page' => $page,
            )
        );

        $res = self::request("/{$model}/{$method}", $data);
        return $res ? self::$responce['data'] : false;
    }

    static public function syncCitiesBook() {
        $page = 1;
        do {
            $data = self::getCitiesBook($page);
            if (!is_array($data)) {
                return;
            }
            foreach ($data as $row) {
                $model = NP_CitiesNp::findModel($row['Ref']);
                $model->setAttributes($row, false);
                $model->save();
                $area = NP_Areas::findModel($row['Area']);
                if ($area->isNewRecord) {
                    $area->Ref = $row['Area'];
                    $area->Description = $row['AreaDescription'];
                    $area->save();
                }
            }
            $page++;
        } while (count($data) > 0);
    }

    static public function syncOfficesBook() {
        $page = 1;
        do {
            $data = self::getOfficesBook($page);
            if (!is_array($data)) {
                return;
            }
            foreach ($data as $row) {
                $model = NP_Offices::findModel($row['Ref']);
                $model->setAttributes($row, false);
                $model->save();
            }
            $page++;
        } while (count($data) > 0);
    }

//    static public function syncStreets() {
//        foreach (NP_Cities::getCityRefs() as $ref) {
//            $page = 1;
//            do {
//                $data = self::getStreets($ref, $page);
//                if (!is_array($data)) {
//                    return false;
//                }
//                foreach ($data as $row) {
//                    $model = NP_Streets::model()->findModel($row['Ref']);
//                    $model->setAttributes($row, false);
//                    $model->CityRef = $ref;
//                    $model->save();
//                }
//                $page++;
//            } while (count($data) > 0);
//        }
//        return true;
//    }

    static public function syncTypes() {
        $data = self::getTypes();
        if (!is_array($data)) {
            return false;
        }
        foreach ($data as $row) {
            $model = NP_Types::findModel($row['Ref']);
            $model->setAttributes($row, false);
            $model->save();
        }
        return true;
    }

    static public function syncPaymentTypes() {
        $data = self::getPaymentTypes();
        if (!is_array($data)) {
            return false;
        }
        foreach ($data as $row) {
            $model = NP_PaymentTypes::findModel($row['Ref']);
            $model->setAttributes($row, false);
            $model->save();
        }
        return true;
    }

    static public function syncPayerTypes() {
        $data = self::getPayerTypes();
        if (!is_array($data)) {
            return false;
        }
        foreach ($data as $row) {
            $model = NP_PayerTypes::findModel($row['Ref']);
            $model->setAttributes($row, false);
            $model->save();
        }
        return true;
    }

    static public function syncPayerTypesBack() {
        $data = self::getPayerTypesBack();
        if (!is_array($data)) {
            return false;
        }
        foreach ($data as $row) {
            $model = NP_PayerTypesBack::findModel($row['Ref']);
            $model->setAttributes($row, false);
            $model->save();
        }
        return true;
    }

    static public function syncDeliveryTypes() {
        $data = self::getDeliveryTypes();
        if (!is_array($data)) {
            return false;
        }
        foreach ($data as $row) {
            $model = NP_DeliveryTypes::findModel($row['Ref']);
            $model->setAttributes($row, false);
            $model->save();
        }
        return true;
    }

    static public function syncCargoTypes() {
        $data = self::getCargoTypes();
        if (!is_array($data)) {
            return false;
        }
        foreach ($data as $row) {
            $model = NP_CargoTypes::findModel($row['Ref']);
            $model->setAttributes($row, false);
            $model->save();
        }
        return true;
    }

    static public function syncCargoDescr() {
        $page = 1;
        do {
            $data = self::getCargoDescr($page);
            if (!is_array($data)) {
                return;
            }
            foreach ($data as $row) {
                $model = NP_CargoDescr::findModel($row['Ref']);
                $model->setAttributes($row, false);
                $model->save();
            }
            $page++;
        } while (count($data) > 0);
    }

    static public function syncSenders() {
        $res = self::syncTypes();
        if (!$res) {
            return;
        }
        $data = self::getSenders();
        if (!is_array($data)) {
            return;
        }
        foreach ($data as $row) {
            $model = NP_Senders::findModel($row['Ref']);
            $model->setAttributes($row, false);
            $model->_type = 'Sender';
            $model->save();
            self::syncContacts($row['Ref']);
        }
    }

    static public function syncContacts($ref) {
        $page = 1;
        do {
            $data = self::getContacts($ref, $page);
            if (!is_array($data)) {
                return;
            }
            foreach ($data as $row) {
                $model = NP_Contacts::findModel($row['Ref']);
                $model->setAttributes($row, false);
                $model->_partner_ref = $ref;
                $model->save();
            }
            $page++;
        } while (count($data) > 0);
    }

    static public function syncMessages() {
        $model = 'CommonGeneral';
        $method = 'getMessageCodeText';
        $res = self::request("/{$model}/{$method}", array(
                    'apiKey' => Settings::get(Settings::NOVA_POSHTA_KEY),
                    'modelName' => $model,
                    'calledMethod' => $method,
        ));
        $data = $res ? self::$responce['data'] : false;
        if (!is_array($data)) {
            return false;
        }
        foreach ($data as $row) {
            $model = NP_Messages::findModel($row['MessageCode']);
            $model->setAttributes($row, false);
            $model->save();
        }
        return true;
    }

    static public function request($url, $data) {
        $url = self::$baseUrl . $url;
        $ch = curl_init($url);
        $headers = array(
            'Accept: application/json',
            'Host: ' . parse_url($url, \PHP_URL_HOST),
        );
        curl_setopt($ch, \CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, \CURLINFO_HEADER_OUT, false);
        curl_setopt($ch, \CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, \CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, \CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, \CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, \CURLOPT_POST, true);
        curl_setopt($ch, \CURLOPT_POSTFIELDS, json_encode($data));
        self::$responce = json_decode(curl_exec($ch), true);
        #Yii::dump(self::$responce);
        curl_close($ch);
        $res = isset(self::$responce['success']) && self::$responce['success'] === true;
        if (!$res) {
            Yii::dump(__METHOD__ . ': URL=' . $url, $data, self::$responce);
        }
        return $res;
    }

    static public function getMessages($index, $indexCodes) {
        if (self::$_mess === null) {
            self::$_mess = NP_Messages::keyval();
        }
        foreach ((array) self::$responce[$index] as $i => $message) {
            if (!isset(self::$responce[$indexCodes][$i]) || !array_key_exists(self::$responce[$indexCodes][$i], self::$_mess)) {
                continue;
            }
            self::$responce[$index][$i] = self::$_mess[self::$responce[$indexCodes][$i]];
        }
        return (array) self::$responce[$index];
    }

}
