<?php

namespace app\models;

use \Yii;

class ApiUser implements \yii\web\IdentityInterface {

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return;
    }

    public static function findIdentityByAccessToken($token, $type = null) {
        return;
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {

    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return false;
    }

}
