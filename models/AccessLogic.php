<?php

namespace app\models;

use Yii;

class AccessLogic
{

//    static public function isAllowHouseService($user_id, $device) {
//        if (empty($user_id)) {
//            return true;
//        }
//        $res = DataHelper::isAllowHouseService($user_id, $device->service_id_1, $device->house_id);
//        if (!$res) {
//            $device->addError('service_id_1', 'Клієнт не надає послугу "' . Services::$labels[$device->service_id_1] . '" за обраною адресою.');
//        }
//        if ($device->type_id == Devices::TYPE_UNIVERSAL && !empty($device->service_id_2)) {
//            $res = DataHelper::isAllowHouseService($user_id, $device->service_id_2, $device->house_id);
//            if (!$res) {
//                $device->addError('service_id_2', 'Клієнт не надає послугу "' . Services::$labels[$device->service_id_2] . '" за обраною адресою.');
//            }
//        }
//        return !$device->hasErrors();
//    }

    static public function isLoginAs()
    {
        return is_array(Yii::$app->session['login-as']);
    }

    static public function setLoginAs($returnUrl)
    {
        Yii::$app->session['login-as'] = ['id' => Yii::$app->user->getId(), 'name' => Yii::$app->user->identity->getName(), 'url' => $returnUrl];
    }

    static public function get($key = null)
    {
        return Yii::$app->session['login-as'] !== NULL ? Yii::$app->session['login-as'][$key] : Yii::$app->session['login-as'];
    }

    static public function resetLoginAs()
    {
        Yii::$app->session['login-as'] = null;
    }

    static public function isClientActive()
    {
        return Yii::$app->user->identity->client_active > 0;
    }

}
