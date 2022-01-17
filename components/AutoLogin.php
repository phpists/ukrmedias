<?php

namespace app\components;

use app\models\Users;
use Yii;

class AutoLogin {

    const PARAM = 'user_id';

    static public function getUserId() {
        return Yii::$app->request->cookies->getValue(self::PARAM);
    }

    static public function delCookie() {
        Yii::$app->response->cookies->remove(self::PARAM);
    }

    static public function setCookie($user_id) {
        $cookies = Yii::$app->response->cookies;
        $cookies->add(new \yii\web\Cookie([
                    'name' => self::PARAM,
                    'value' => $user_id,
                    'expire' => strtotime('+30 day'),
        ]));
    }

}
